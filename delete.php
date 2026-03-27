<?php 
require 'db.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

if(isset($_GET['id'])){

    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM prompts
            WHERE id = :id ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $id,
        ]);

    header('Location: dashboard.php');
    exit();
}

?>