<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    Hi Admin,
    <br>
    New user has been register now. Please check user detail below 
    <br>
    Name <strong>{{ $name }}</strong>
    <br>
    Email  <strong>{{ $email }}</strong>
    <br>
    Username  <strong>{{ $username }}</strong>
    <br>
    Please verify this user on <a href="{{$link}}" target="__blank">click here</a> OR
    In admin panel
    <br/>
</div>

</body>
</html>