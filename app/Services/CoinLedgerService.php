<?php

namespace App\Services;

use App\Models\CoinLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * CoinLedgerService
 *
 * Implements Architecture V2.2 Master Specification:
 * - Double-entry append-only immutable coin ledger
 * - Atomic row-level locking (FOR UPDATE) to prevent concurrency balance corruption
 * - Idempotency protection against network retries
 * - Multi-account support (operational, sales, escrow)
 * - 50% SLA penalty liquidation rules
 */
class CoinLedgerService
{
    /**
     * Get the authoritative available balance for a company.
     */
    public function getBalance(int $companyId, string $accountType = 'operational'): float
    {
        $coinRecord = DB::table('company_coins')
            ->where('company_id', $companyId)
            ->where('event_type', $accountType)
            ->first();

        return (float) ($coinRecord->balance_coin ?? 0.00);
    }

    /**
     * Reserve coins into escrow upon Agency Job Claim.
     *
     * @param  string  $referenceId  (e.g. recommendation_relation.id or claim UUID)
     * @param  string  $accountType  ('operational'|'sales')
     * @param  string  $coinType  ('standard'|'promotional'|'enterprise')
     * @return string Transaction ID
     *
     * @throws \Exception
     */
    public function reserveCoinsForClaim(
        int $companyId,
        float $amount,
        string $referenceId,
        string $idempotencyKey,
        string $accountType = 'operational',
        string $coinType = 'standard'
    ): string {
        return DB::transaction(function () use ($companyId, $amount, $referenceId, $idempotencyKey, $accountType, $coinType) {
            // 1. Idempotency Check
            $existing = CoinLedger::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return str_replace('_CR', '', $existing->transaction_id);
            }

            // 2. Row-level Lock on company coins
            $companyCoin = DB::table('company_coins')
                ->where('company_id', $companyId)
                ->where('event_type', $accountType)
                ->lockForUpdate()
                ->first();

            $currentBalance = (float) ($companyCoin->balance_coin ?? 0.00);
            if ($currentBalance < $amount) {
                throw new \RuntimeException("Insufficient coin balance ({$currentBalance}) for claim reservation of {$amount} coins.");
            }

            $newBalance = round($currentBalance - $amount, 2);
            $txnId = 'TXN_RSV_'.Str::upper(Str::random(12));

            // 3. Update legacy balance
            DB::table('company_coins')
                ->where('id', $companyCoin->id)
                ->update([
                    'balance_coin' => $newBalance,
                    'updated_at' => now(),
                ]);

            // 4. Double-Entry: DEBIT Operational Account
            CoinLedger::create([
                'transaction_id' => $txnId.'_DR',
                'company_id' => $companyId,
                'account_type' => $accountType,
                'coin_type' => $coinType,
                'entry_type' => 'DEBIT',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference_type' => 'job_claim_escrow',
                'reference_id' => $referenceId,
                'idempotency_key' => $idempotencyKey.'_dr',
                'metadata' => ['step' => 'reserve', 'target' => 'escrow'],
                'created_at' => now(),
            ]);

            // 5. Double-Entry: CREDIT Escrow Account
            CoinLedger::create([
                'transaction_id' => $txnId.'_CR',
                'company_id' => $companyId,
                'account_type' => 'escrow',
                'coin_type' => $coinType,
                'entry_type' => 'CREDIT',
                'amount' => $amount,
                'balance_after' => $amount,
                'reference_type' => 'job_claim_escrow',
                'reference_id' => $referenceId,
                'idempotency_key' => $idempotencyKey,
                'metadata' => ['step' => 'reserve', 'source' => $accountType],
                'created_at' => now(),
            ]);

            return $txnId;
        });
    }

    /**
     * Settle coins in escrow upon successful candidate submission or hire.
     */
    public function settleClaim(
        int $companyId,
        float $amount,
        string $referenceId,
        string $idempotencyKey,
        string $coinType = 'standard'
    ): string {
        return DB::transaction(function () use ($companyId, $amount, $referenceId, $idempotencyKey, $coinType) {
            $existing = CoinLedger::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing->transaction_id;
            }

            $txnId = 'TXN_SET_'.Str::upper(Str::random(12));

            // DEBIT Escrow Account
            CoinLedger::create([
                'transaction_id' => $txnId,
                'company_id' => $companyId,
                'account_type' => 'escrow',
                'coin_type' => $coinType,
                'entry_type' => 'DEBIT',
                'amount' => $amount,
                'balance_after' => 0.00,
                'reference_type' => 'claim_settlement',
                'reference_id' => $referenceId,
                'idempotency_key' => $idempotencyKey,
                'metadata' => ['action' => 'settle_claim'],
                'created_at' => now(),
            ]);

            return $txnId;
        });
    }

    /**
     * Refund reserved coins from escrow back to the operational balance.
     */
    public function refundClaim(
        int $companyId,
        float $amount,
        string $referenceId,
        string $idempotencyKey,
        ?string $reason = 'CLAIM_REJECTED',
        string $accountType = 'operational',
        string $coinType = 'standard'
    ): string {
        return DB::transaction(function () use ($companyId, $amount, $referenceId, $idempotencyKey, $reason, $accountType, $coinType) {
            $existing = CoinLedger::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing->transaction_id;
            }

            // Lock company coins row
            $companyCoin = DB::table('company_coins')
                ->where('company_id', $companyId)
                ->where('event_type', $accountType)
                ->lockForUpdate()
                ->first();

            $currentBalance = (float) ($companyCoin->balance_coin ?? 0.00);
            $newBalance = round($currentBalance + $amount, 2);
            $txnId = 'TXN_RFD_'.Str::upper(Str::random(12));

            DB::table('company_coins')
                ->where('id', $companyCoin->id)
                ->update([
                    'balance_coin' => $newBalance,
                    'updated_at' => now(),
                ]);

            // DEBIT Escrow
            CoinLedger::create([
                'transaction_id' => $txnId.'_DR',
                'company_id' => $companyId,
                'account_type' => 'escrow',
                'coin_type' => $coinType,
                'entry_type' => 'DEBIT',
                'amount' => $amount,
                'balance_after' => 0.00,
                'reference_type' => 'claim_refund',
                'reference_id' => $referenceId,
                'idempotency_key' => $idempotencyKey.'_dr',
                'metadata' => ['reason' => $reason],
                'created_at' => now(),
            ]);

            // CREDIT Operational
            CoinLedger::create([
                'transaction_id' => $txnId.'_CR',
                'company_id' => $companyId,
                'account_type' => $accountType,
                'coin_type' => $coinType,
                'entry_type' => 'CREDIT',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference_type' => 'claim_refund',
                'reference_id' => $referenceId,
                'idempotency_key' => $idempotencyKey,
                'metadata' => ['reason' => $reason],
                'created_at' => now(),
            ]);

            return $txnId;
        });
    }

    /**
     * Penalize SLA Violation: 50% forfeited/burned, 50% refunded to company balance.
     *
     * @return array Contains 'refunded', 'forfeited', 'transaction_id'
     */
    public function penalizeSlaViolation(
        int $companyId,
        float $totalEscrowAmount,
        string $referenceId,
        string $idempotencyKey,
        string $accountType = 'operational',
        string $coinType = 'standard'
    ): array {
        return DB::transaction(function () use ($companyId, $totalEscrowAmount, $referenceId, $idempotencyKey, $accountType, $coinType) {
            $existing = CoinLedger::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return [
                    'refunded' => round($totalEscrowAmount * 0.50, 2),
                    'forfeited' => round($totalEscrowAmount * 0.50, 2),
                    'transaction_id' => $existing->transaction_id,
                ];
            }

            $refundAmount = round($totalEscrowAmount * 0.50, 2);
            $penaltyAmount = round($totalEscrowAmount - $refundAmount, 2);

            $companyCoin = DB::table('company_coins')
                ->where('company_id', $companyId)
                ->where('event_type', $accountType)
                ->lockForUpdate()
                ->first();

            $currentBalance = (float) ($companyCoin->balance_coin ?? 0.00);
            $newBalance = round($currentBalance + $refundAmount, 2);
            $txnId = 'TXN_SLA_'.Str::upper(Str::random(12));

            DB::table('company_coins')
                ->where('id', $companyCoin->id)
                ->update([
                    'balance_coin' => $newBalance,
                    'updated_at' => now(),
                ]);

            // 1. Escrow debited for total
            CoinLedger::create([
                'transaction_id' => $txnId.'_ESC_DR',
                'company_id' => $companyId,
                'account_type' => 'escrow',
                'coin_type' => $coinType,
                'entry_type' => 'DEBIT',
                'amount' => $totalEscrowAmount,
                'balance_after' => 0.00,
                'reference_type' => 'sla_penalty_clearance',
                'reference_id' => $referenceId,
                'idempotency_key' => $idempotencyKey.'_esc_dr',
                'metadata' => ['total_escrow' => $totalEscrowAmount],
                'created_at' => now(),
            ]);

            // 2. Operational credited 50%
            CoinLedger::create([
                'transaction_id' => $txnId.'_OP_CR',
                'company_id' => $companyId,
                'account_type' => $accountType,
                'coin_type' => $coinType,
                'entry_type' => 'CREDIT',
                'amount' => $refundAmount,
                'balance_after' => $newBalance,
                'reference_type' => 'sla_partial_refund',
                'reference_id' => $referenceId,
                'idempotency_key' => $idempotencyKey,
                'metadata' => ['refund_ratio' => '50%'],
                'created_at' => now(),
            ]);

            // 3. System penalty forfeiture recorded
            CoinLedger::create([
                'transaction_id' => $txnId.'_PENALTY',
                'company_id' => $companyId,
                'account_type' => 'escrow',
                'coin_type' => $coinType,
                'entry_type' => 'DEBIT',
                'amount' => $penaltyAmount,
                'balance_after' => 0.00,
                'reference_type' => 'sla_forfeiture_penalty',
                'reference_id' => $referenceId,
                'idempotency_key' => $idempotencyKey.'_penalty',
                'metadata' => ['penalty_ratio' => '50%', 'forfeited' => $penaltyAmount],
                'created_at' => now(),
            ]);

            return [
                'refunded' => $refundAmount,
                'forfeited' => $penaltyAmount,
                'transaction_id' => $txnId,
            ];
        });
    }
}
