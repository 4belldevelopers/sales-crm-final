@php
$data   = is_object($data) ? get_object_vars($data) : $data;
$skills = $data['candidate_skills'] ?? [];
@endphp

{!! @$data['message'] !!}

@if(!empty($skills))
<div style="margin-top:20px;">
    <p style="font-family:Arial,sans-serif;font-size:13px;font-weight:bold;color:#333;margin-bottom:8px;">
        Key Skills:
    </p>
    <div style="display:flex;flex-wrap:wrap;gap:6px;">
        @foreach($skills as $skill)
        <span style="
            display:inline-block;
            background-color:#e8f4fd;
            color:#1a73e8;
            border:1px solid #b3d4f5;
            border-radius:4px;
            padding:3px 10px;
            font-family:Arial,sans-serif;
            font-size:12px;
            font-weight:600;
        ">{{ $skill }}</span>
        @endforeach
    </div>
</div>
@endif

@include('email.global_settings')
