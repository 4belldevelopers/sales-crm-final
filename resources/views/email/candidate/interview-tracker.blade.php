<?php  

 

$data = is_object($data) ? get_object_vars($data) : $data;


?>
 {!! $data['details'] !!}


 @include('email.global_settings')
