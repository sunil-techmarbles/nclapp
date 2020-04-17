<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    Hi Admin,
    <br>
    You have a new user for approval, below are the details
    <br>
    Name <strong>{{ $name }}</strong>
    <br>
    Email  <strong>{{ $email }}</strong>
    <br>
    Username  <strong>{{ $username }}</strong>
    <br>
    Please verify the account in admin panel or by <a href="{{$link}}" target="__blank">clicking here</a>
    <br/>
</div>

</body>
</html>