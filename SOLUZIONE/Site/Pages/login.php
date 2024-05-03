<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi</title>
    <script src="../Js/jquery-3.7.1.min.js"></script>
    <script src="../Js/login.js"></script>
    <style>
        body {
            margin-top: 10%;
            text-align: center;
        }
    </style>
</head>

<body>

    <form id="loginForm" action="../Controllers/checkLogin.php" method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required><br>
        <label for="numeroTessera">Numero della tessera</label>
        <input type="password" id="numeroTessera" name="numeroTessera"><br>
        <input type="submit" value="Login">
    </form>

    <div id="error"></div>

    Non hai ancora un'account? <a href="register.php">Registrati!</a>

</body>

</html>