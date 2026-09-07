-- Intent: persist company/service replacement and payment policies on the existing tenant-scoped commercial record.
-- Apply once to MySQL 8 before deploying the matching application code.
ALTER TABLE `commericials`
    ADD COLUMN `replacement_period_days` SMALLINT UNSIGNED NULL AFTER `salary_currency`,
    ADD COLUMN `replacement_type` VARCHAR(32) NULL AFTER `replacement_period_days`,
    ADD COLUMN `replacement_eligibility` VARCHAR(32) NULL AFTER `replacement_type`,
    ADD COLUMN `billing_frequency` VARCHAR(24) NULL AFTER `replacement_eligibility`,
    ADD COLUMN `billing_trigger` VARCHAR(32) NULL AFTER `billing_frequency`,
    ADD COLUMN `payment_terms_days` SMALLINT UNSIGNED NULL AFTER `billing_trigger`,
    ALGORITHM=INSTANT;

-- Rollback (data-destructive; run only when explicitly approved):
-- ALTER TABLE `commericials`
--     DROP COLUMN `payment_terms_days`,
--     DROP COLUMN `billing_trigger`,
--     DROP COLUMN `billing_frequency`,
--     DROP COLUMN `replacement_eligibility`,
--     DROP COLUMN `replacement_type`,
--     DROP COLUMN `replacement_period_days`,
--     ALGORITHM=INSTANT;
