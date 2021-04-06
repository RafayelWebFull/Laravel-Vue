<h2>Hello {{$user->name}}</h2>
<p>Verify your email ara by this link
    <a href="{{"task.loc/api/auth/verify?email=$user->email&token=$token"}}">Link</a>
    <h2>{{env('APP_FRONT_URL')}}</h2>
</p>
