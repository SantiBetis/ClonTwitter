<?php
// Procesar la actualización de la descripción del usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_description'])) {
    $newDescription = trim($_POST['description']);
    
    // Actualizar la descripción solo si es el usuario logueado
    if ($userId == $profileUserId && !empty($newDescription)) {
        $updateDescription = "UPDATE social_network.users SET description = :description WHERE id = :userId";
        $stmtUpdateDescription = $pdo->prepare($updateDescription);
        $stmtUpdateDescription->bindParam(':description', $newDescription);
        $stmtUpdateDescription->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmtUpdateDescription->execute();
        header("Location: perfil.php?id=$userId"); // Redirigir a la misma página para ver el cambio
        exit;
    }
}

?>