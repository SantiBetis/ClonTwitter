<?php
require_once "../scripts/connection.php";

if (!isset($_SESSION["id"])) {
    header("Location: ../index.php");
    exit;
}

$idUsuario = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['id'];

try {
    $stmt = $pdo->prepare("SELECT username FROM social_network.users WHERE id = :id");
    $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Usuario no encontrado.");
    }

    $userName = $user['username'];

    $sqlSeguidas = "SELECT u.id, u.username, u.description
                    FROM social_network.follows f
                    JOIN social_network.users u ON f.userToFollowId = u.id
                    WHERE f.users_id = :idUsuario";
    $stmtSeguidas = $pdo->prepare($sqlSeguidas);
    $stmtSeguidas->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmtSeguidas->execute();
    $seguidos = $stmtSeguidas->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unfollow_user'])) {
        $unfollowUserId = (int)$_POST['unfollow_user'];
        $deleteFollow = "DELETE FROM social_network.follows WHERE users_id = :userId AND userToFollowId = :unfollowUserId";
        $stmtDeleteFollow = $pdo->prepare($deleteFollow);
        $stmtDeleteFollow->bindParam(':userId', $idUsuario, PDO::PARAM_INT);
        $stmtDeleteFollow->bindParam(':unfollowUserId', $unfollowUserId, PDO::PARAM_INT);
        $stmtDeleteFollow->execute();
        
        header("Location: seguidos.php?id=$idUsuario&from=can_unfollow"); 
        exit;
    }

} catch (PDOException $e) {
    echo "Error de base de datos: " . $e->getMessage();
    exit;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Personas que sigue <?php echo htmlspecialchars($userName); ?></title>
    <link rel="icon" href="../img/logo2.png" type="image/png">
    <link rel="stylesheet" href="../css/seguidos.css">
</head>
<body>
    <div class="container">
        <div class=buttons>
            <a href="./main.php" class="nav-button home">Inicio</a>
            <a href="./perfil.php?id=<?php echo htmlspecialchars($idUsuario); ?>" class="nav-button profile">Atrás</a>
        </div>
        <h1>Personas que sigue <?php echo htmlspecialchars($userName); ?></h1>

        <div class="user-cards">
            <?php if (!empty($seguidos)): ?>
                <?php foreach ($seguidos as $seguido): ?>
                    <div class="user-card">
                        <h2>
                            <a href="perfil.php?id=<?php echo htmlspecialchars($seguido['id']); ?>">
                                <?php echo htmlspecialchars($seguido['username']); ?>
                            </a>
                        </h2>
                        <p><?php echo htmlspecialchars($seguido['description'] ?? 'No hay descripción disponible.'); ?></p>

                        <?php if (isset($_GET['from']) && $_GET['from'] === 'can_unfollow'): ?>
                            <form method="POST" action="seguidos.php?id=<?php echo htmlspecialchars($idUsuario); ?>" class="mt-2 flex justify-center">
                                <input type="hidden" name="unfollow_user" value="<?php echo htmlspecialchars($seguido['id']); ?>">
                                <button type="submit" class="unfollow-button">Dejar de Seguir</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-500">No sigues a nadie.</p>
            <?php endif; ?>
        </div>

        
    </div>
</body>
</html>
