<h1>Forget Password Email</h1>
<p>{{ $token }}</p>
<br>
You can reset password from bellow link:
<a href="{{ route('reset.password.get', $token) }}">Reset Password</a>
