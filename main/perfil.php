<?php
require_once '../scripts/connection.php';

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

// Usuario
$profileUserId = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['id'];
$userId = $_SESSION['id'];

// Perfil
$queryProfileUser = "SELECT * FROM social_network.users WHERE id = :profileUserId";
$stmtProfileUser = $pdo->prepare($queryProfileUser);
$stmtProfileUser->bindParam(':profileUserId', $profileUserId, PDO::PARAM_INT);
$stmtProfileUser->execute();
$profileUser = $stmtProfileUser->fetch(PDO::FETCH_ASSOC);

if (!$profileUser) {
    header("Location: ../index.php");
    exit;
}

$countFollowersQuery = "SELECT COUNT(*) as followerCount FROM social_network.follows WHERE userToFollowId = :profileUserId";
$stmtFollowers = $pdo->prepare($countFollowersQuery);
$stmtFollowers->bindParam(':profileUserId', $profileUserId, PDO::PARAM_INT);
$stmtFollowers->execute();
$rowFollowers = $stmtFollowers->fetch(PDO::FETCH_ASSOC);
$followerCount = $rowFollowers['followerCount'];

$countFollowingQuery = "SELECT COUNT(*) as followingCount FROM social_network.follows WHERE users_id = :profileUserId";
$stmtFollowing = $pdo->prepare($countFollowingQuery);
$stmtFollowing->bindParam(':profileUserId', $profileUserId, PDO::PARAM_INT);
$stmtFollowing->execute();
$rowFollowing = $stmtFollowing->fetch(PDO::FETCH_ASSOC);
$followingCount = $rowFollowing['followingCount'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['follow_user'])) {
    $followUserId = $_POST['follow_user'];
    $insertFollow = "INSERT INTO social_network.follows (users_id, userToFollowId) VALUES (:userId, :followUserId)";
    $stmtInsertFollow = $pdo->prepare($insertFollow);
    $stmtInsertFollow->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmtInsertFollow->bindParam(':followUserId', $followUserId, PDO::PARAM_INT);
    $stmtInsertFollow->execute();
    header("Location: perfil.php?id=$followUserId"); 
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unfollow_user'])) {
    $unfollowUserId = $_POST['unfollow_user'];
    $deleteFollow = "DELETE FROM social_network.follows WHERE users_id = :userId AND userToFollowId = :unfollowUserId";
    $stmtDeleteFollow = $pdo->prepare($deleteFollow);
    $stmtDeleteFollow->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmtDeleteFollow->bindParam(':unfollowUserId', $unfollowUserId, PDO::PARAM_INT);
    $stmtDeleteFollow->execute();
    header("Location: perfil.php?id=$unfollowUserId");
    exit;
}

$isFollowing = false;
$checkFollow = "SELECT * FROM social_network.follows WHERE users_id = :userId AND userToFollowId = :profileUserId";
$stmtCheckFollow = $pdo->prepare($checkFollow);
$stmtCheckFollow->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmtCheckFollow->bindParam(':profileUserId', $profileUserId, PDO::PARAM_INT);
$stmtCheckFollow->execute();
if ($stmtCheckFollow->rowCount() > 0) {
    $isFollowing = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_description'])) {
    $newDescription = trim($_POST['description']);
    
    if ($userId == $profileUserId && !empty($newDescription)) {
        $updateDescription = "UPDATE social_network.users SET description = :description WHERE id = :userId";
        $stmtUpdateDescription = $pdo->prepare($updateDescription);
        $stmtUpdateDescription->bindParam(':description', $newDescription);
        $stmtUpdateDescription->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmtUpdateDescription->execute();
        header("Location: perfil.php?id=$userId");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($profileUser['username']); ?></title>
    <link rel="icon" href="../img/logo2.png" type="image/png">
    <link rel="stylesheet" href="../css/perfil.css">
</head>
<body>

    <div class="container">
        <div class="back-button">
            <a href="main.php" class="homepage">
                <button class="button-homepage">Inicio</button>
            </a>
            <a href="../scripts/logout.php" class="logout">
                <button class="button-logout">Cerrar sesión</button>
            </a>
        </div>
        <div class="profile-header">
            <div>
                <h1><?php echo htmlspecialchars($profileUser['username']); ?></h1>
                <p><?php echo htmlspecialchars($profileUser['description'] ?? ''); ?></p>
                <div class="update-description-container">
            <?php if ($profileUserId == $userId): ?>
                <h2>Modificar descripción</h2>
                <form method="POST" action="perfil.php?id=<?php echo $profileUserId; ?>">
                    <textarea name="description" rows="4" placeholder="Escribe tu nueva descripción aquí..."><?php echo htmlspecialchars($profileUser['description'] ?? ''); ?></textarea>
                    <input type="hidden" name="update_description" value="1">
                    <button type="submit" class="update-description-button">Actualizar</button>
                </form>
            <?php endif; ?>
        </div>
            </div>
            
            <?php if ($profileUserId != $userId): ?>
                <form method="POST" action="perfil.php?id=<?php echo $profileUserId; ?>">
                    <?php if ($isFollowing): ?>
                        <input type="hidden" name="unfollow_user" value="<?php echo $profileUserId; ?>">
                        <button type="submit" class="unfollow-button">Dejar de Seguir</button>
                    <?php else: ?>
                        <input type="hidden" name="follow_user" value="<?php echo $profileUserId; ?>">
                        <button type="submit" class="follow-button">Seguir</button>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>

        <div class="extra-buttons">
            <a href="seguidos.php?id=<?php echo $profileUserId; ?>" class="following-button"><?php echo htmlspecialchars($followingCount); ?> Siguiendo</a>
            <a href="seguidores.php?id=<?php echo $profileUserId; ?>&type=followers" class="followers-button"><?php echo htmlspecialchars($followerCount); ?> Seguidores</a>
        </div>

        

        <div class="tweets-container">
            <h2>Posts</h2>
            <?php
            $sqlTweets = "SELECT p.*, u.username FROM social_network.publications p
                          JOIN social_network.users u ON p.userId = u.id
                          WHERE p.userId = :profileUserId
                          ORDER BY p.createDate DESC";
            
            $stmtTweets = $pdo->prepare($sqlTweets);
            $stmtTweets->bindParam(':profileUserId', $profileUserId, PDO::PARAM_INT);
            $stmtTweets->execute();

            $tweets = $stmtTweets->fetchAll(PDO::FETCH_ASSOC);
            if (empty($tweets)): ?>
                <p>No hay tweets.</p>
            <?php else: ?>
                <?php foreach ($tweets as $tweet): ?>
                    <div class="tweet">
                        <p><strong><?php echo htmlspecialchars($tweet['username']); ?></strong></p>
                        <p><?php echo htmlspecialchars($tweet['text']); ?></p>
                        <p><?php echo htmlspecialchars($tweet['createDate']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        
    </div>
</body>
</html>
