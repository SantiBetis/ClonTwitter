<?php
require_once "./connection.php"; 

$error = "";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST["username"]);
    $pass = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR); 

    if ($stmt->execute()) {
        if ($stmt->rowCount() === 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($pass, $usuario["password"])) {
                $_SESSION["username"] = $usuario["username"];
                $_SESSION["id"] = $usuario["id"];
                header("Location: ../main/main.php");
                exit;
            } else {
                $_SESSION['error'] = "Contraseña incorrecta";
                header("Location: ../index.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Usuario no encontrado";
            header("Location: ../index.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Error al ejecutar la consulta.";
        header("Location: ../index.php");
        exit;
    }
}
?>