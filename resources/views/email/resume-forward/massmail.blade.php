@php

$data = is_object($data) ? get_object_vars($data) : $data;

@endphp

<p> Hi  {{ @$data['condidate_name'] }}</p>
{!! @$data['message'] !!}



@include('email.global_settings')