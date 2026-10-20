{{-- 
  Jero.works Unified Authentication Architecture (Master Shell)
  Signup routes to auth.login with initialMode='signup' to maintain pure single-page master shell.
--}}
@include('auth.login', ['initialMode' => 'signup'])
