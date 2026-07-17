<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Candidates Summary</title>
    <style>
        /* Poppins with a Helvetica/Arial fallback, matching the app's print-PDF
           convention (see offer-letter.blade.php) — no Poppins font file is
           embedded for dompdf yet, so this currently renders via the fallback. */
        body {
            font-family: 'Poppins', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #1e1e2d;
            margin: 0;
            padding: 0;
        }
        @page {
            margin: 30px 36px;
        }
        h1 {
            font-size: 16px;
            margin: 0 0 4px;
        }
        .print-meta {
            font-size: 10px;
            color: #7e8299;
            margin-bottom: 18px;
        }
        .candidate-card {
            border: 1px solid #e4e6ef;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .candidate-name {
            font-size: 14px;
            font-weight: 700;
            color: #181c32;
        }
        .candidate-role {
            font-size: 11px;
            color: #5e6278;
            margin-bottom: 8px;
        }
        table.fields {
            width: 100%;
            border-collapse: collapse;
        }
        table.fields td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11px;
        }
        table.fields td.label {
            width: 110px;
            color: #99a1b7;
            font-weight: 600;
        }
        .skills-line {
            margin-top: 6px;
            font-size: 10.5px;
            color: #3f4254;
        }
        .skills-line .label {
            color: #99a1b7;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <h1>Candidates Summary</h1>
    <div class="print-meta">Generated {{ date('d M Y, h:i A') }} — {{ count($candidates) }} candidate(s)</div>

    @forelse ($candidates as $c)
        <div class="candidate-card">
            <div class="candidate-name">{{ $c->full_name ?: 'N/A' }}</div>
            <div class="candidate-role">
                {{ $c->current_designation ?: 'N/A' }}{{ $c->current_company ? ' at ' . $c->current_company : '' }}
            </div>
            <table class="fields">
                <tr>
                    <td class="label">Email</td>
                    <td>{{ $c->email ?: 'N/A' }}</td>
                    <td class="label">Phone</td>
                    <td>{{ $c->phone ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Experience</td>
                    <td>{{ $c->total_exp !== '' ? $c->total_exp . ' yrs' : 'N/A' }}</td>
                    <td class="label">Location</td>
                    <td>{{ $c->city_name ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Notice Period</td>
                    <td>{{ $c->notice }}</td>
                    <td class="label">Current Salary</td>
                    <td>{{ $c->salary }}</td>
                </tr>
                @if ($c->job_title)
                    <tr>
                        <td class="label">Tracked For</td>
                        <td colspan="3">{{ $c->job_title }}{{ $c->client_name ? ' (' . $c->client_name . ')' : '' }}</td>
                    </tr>
                @endif
            </table>
            @if ($c->skills)
                <div class="skills-line"><span class="label">Skills:</span> {{ $c->skills }}</div>
            @endif
        </div>
    @empty
        <p>No candidates found for the selected rows.</p>
    @endforelse
</body>

</html>
