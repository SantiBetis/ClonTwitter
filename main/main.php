<?php
require_once '../scripts/connection.php';

$userId = $_SESSION['id'];

try {
    $queryUser = "SELECT * FROM users WHERE id = :id";
    $stmtUser = $pdo->prepare($queryUser);
    $stmtUser->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmtUser->execute();
    $usuarioLogueado = $stmtUser->fetch(PDO::FETCH_ASSOC);

    $tweetOption = 'following';
    if (isset($_POST['tweet_option'])) {
        $tweetOption = $_POST['tweet_option'];
    }

    if ($tweetOption === 'following') {
        $sqlTweets = "SELECT p.*, u.username FROM publications p
                      JOIN users u ON p.userId = u.id
                      WHERE p.userId IN (SELECT userToFollowId FROM follows WHERE users_id = :userId)
                      ORDER BY p.createDate DESC";
    } else {
        $sqlTweets = "SELECT p.*, u.username FROM publications p
                      JOIN users u ON p.userId = u.id
                      ORDER BY p.createDate DESC";
    }

    $stmtTweets = $pdo->prepare($sqlTweets);
    if ($tweetOption === 'following') {
        $stmtTweets->bindParam(':userId', $userId, PDO::PARAM_INT);
    }
    $stmtTweets->execute();

    $tweets = [];
    while ($tweet = $stmtTweets->fetch(PDO::FETCH_ASSOC)) {
        $tweets[] = $tweet;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tweet_content'])) {
        $tweetContent = trim($_POST['tweet_content']);

        if (strlen($tweetContent) > 280) {
            echo "<p class='error-text'>Error: El tweet no puede tener más de 280 caracteres.</p>";
        } elseif (!empty($tweetContent)) {
            $insertTweet = "INSERT INTO publications (userId, text, createDate) VALUES (:userId, :text, CURRENT_TIMESTAMP)";
            $stmtInsert = $pdo->prepare($insertTweet);
            $stmtInsert->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtInsert->bindParam(':text', $tweetContent, PDO::PARAM_STR);
            $stmtInsert->execute();
            header("Location: main.php");
            exit;
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio / Twitter</title>
    <link rel="icon" href="../img/logo2.png" type="image/png">
    <link rel="stylesheet" href="../css/main.css">
</head>
<body class="bg-gray-100">

    <div class="container">
        <div class="user-info-container">
            <div class="user-info">
                <img src="../img/logo2.png" alt="Logo" class="user-logo">
                <h1><?php echo htmlspecialchars($usuarioLogueado['username']); ?></h1>
                <p><?php echo htmlspecialchars($usuarioLogueado['description'] ?? ''); ?></p>
                <div class="user-actions">
                    <a href="perfil.php?id=<?php echo $userId; ?>" class="profile-button">Perfil</a>
                    <a href="../index.php" class="logout-button">Cerrar sesión</a>
                </div>
            </div>
        </div>


        
        <div class="tweets-container">
        <div class="tweet-options">
                <form method="POST" action="main.php">
                    <button type="submit" name="tweet_option" value="following" class="option-button <?php echo ($tweetOption === 'following') ? 'active' : ''; ?>">
                        Siguiendo
                    </button>
                    <button type="submit" name="tweet_option" value="all" class="option-button <?php echo ($tweetOption === 'all') ? 'active' : ''; ?>">
                        Para ti
                    </button>
                </form>
            </div>

            <div class="tweet-form">
                <form method="POST" action="main.php">
                    <textarea name="tweet_content" rows="4" maxlength="280" placeholder="Escribe un tweet" required></textarea>
                    <p id="charCount">280 caracteres restantes</p>
                    <button type="submit" class="submit-button">Postear</button>
                </form>
            </div>

            <h2 class="tweets-title">¡¿Qué está pasando?!</h2>
            <?php if (empty($tweets)): ?>
                <p>No hay tweets.</p>
            <?php else: ?>
                <?php foreach ($tweets as $tweet): ?>
                    <div class="tweet">
                        <p class="tweet-author"><a href="perfil.php?id=<?php echo $tweet['userId']; ?>"><?php echo htmlspecialchars($tweet['username']); ?></a></p>
                        <p><?php echo nl2br(htmlspecialchars(wordwrap($tweet['text'], 100, "\n", true))); ?></p>
                        <p class="tweet-date"><?php echo $tweet['createDate']; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>