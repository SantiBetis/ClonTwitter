<?php
require_once "./connection.php";

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php"); 
    exit();
}

try {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $userIdToFollow = intval($_GET['id']);
        $followerUserId = $_SESSION['id'];

        $sql = "INSERT INTO social_network.follows (users_id, userToFollowId) VALUES (:followerUserId, :userIdToFollow)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':followerUserId', $followerUserId, PDO::PARAM_INT);
        $stmt->bindParam(':userIdToFollow', $userIdToFollow, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: ../pages/usuarios.php?mensaje=Usuario seguido con éxito");
            exit();
        } else {
            echo "Error al seguir al usuario.";
        }
    } else {
        header("Location: ../pages/usuarios.php?mensaje=ID de usuario no válido");
        exit();
    }
} catch (PDOException $e) {
    echo "Error en la base de datos: " . $e->getMessage();
}
?>