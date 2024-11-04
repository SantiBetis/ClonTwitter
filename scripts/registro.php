<?php

if (isset($_POST["submit"])) {
    require_once("./connection.php");

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    $description = ""; 
    $createDate = date("Y-m-d H:i:s"); 

    if ($username && $email && $password) {
        $pass = password_hash($password, PASSWORD_BCRYPT, ["cost" => 4]);
        
        $sql = "INSERT INTO users (username, email, password, description, createDate) VALUES (:username, :email, :password, :description, :createDate)";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $pass);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':createDate', $createDate);
            
            if ($stmt->execute()) {
                header("Location: ../index.php");
                exit;
            } else {
                header("Location: ../error/error.php");
                exit;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: ../error/error.php");
        exit;
    }
}
