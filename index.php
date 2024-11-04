<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/index.css">
    <title>Login - Clon de Twitter</title>
    <link rel="icon" href="img/logo2.png" type="image/png">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <img src="img/logo.png" alt="Logo" class="login-logo">
        </div>

        <div class="login-right">
            <div class="login-form-container">
                <h1>Inicia sesión en Twitter</h1>
                <form action="scripts/login.php" method="POST" class="login-form">
                    <div class="form-group">
                        <input type="text" id="username" name="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="login-button">Iniciar sesión</button>
                    </div>
                </form>

                <div class="login-links">
                    <p>¿No tienes cuenta? <a href="main/registroform.php">Regístrate aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
