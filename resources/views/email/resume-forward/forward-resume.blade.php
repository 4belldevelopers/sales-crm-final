@php

$data = is_object($data) ? get_object_vars($data) : $data;

@endphp
 {!! @$data['message'] !!}



 @include('email.global_settings')