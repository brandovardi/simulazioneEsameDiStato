<?php

if (!isset($_SESSION)) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="../Js/jquery-3.7.1.min.js"></script>
    <script src="../Js/login.js"></script>
</head>

<body>

    <form id="loginForm" action="../Controllers/checkLogin.php" method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Login">
    </form>

    <div id="error"></div>

    <a href="register.php">Register</a>

</body>

</html>