<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Our Application</title>
</head>
<body>
    <h1>Welcome, {{ $user->firstname }} {{ $user->lastname }}!</h1>
    <p>Thank you for registering with our application.</p>
    <p>Your family code is: <strong>{{ $familyCode }}</strong></p>
    <p>Please keep this code for your records.</p>
</body>
</html>