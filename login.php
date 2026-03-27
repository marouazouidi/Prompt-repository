<?php
session_start();
require 'db.php';

$error = "";

if(isset($_POST['login'])){
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $_POST['email']]);
    $user = $stmt->fetch();

    if(!$user){
        $error = "Email introuvable !";
    } else if(!password_verify($_POST['password'], $user['password'])){
        $error = "Mot de passe incorrect !";
    } else if($user['role'] != $_POST['role']){
        $error = "Le rôle sélectionné est incorrect !";
    } else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        if($user['role'] == 'User'){
            header("Location: dashboard.php");
        } else {
            header("Location: admin.php");
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - DevGenius</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body"> <div class="auth-container">
        <h1>🚀 DevGenius</h1>
        <h3>Connexion au Repository</h3>

        <?php if($error != ""): ?>
            <div class="error-box"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Email professionnel</label>
            <input type="email" name="email" placeholder="votre@email.com" required>
            
            <label>Mot de passe</label>
            <input type="password" name="password" placeholder="••••••••" required>
            
            <label>Votre Rôle</label>
            <select name="role">
                <option value="User">👨‍💻 Développeur</option>
                <option value="Admin">🛠️ Administrateur</option>
            </select>
            
            <button type="submit" name="login">Se connecter</button>
            
            <p class="text-center mt-20">
                Nouveau ici ? <a href="register.php">Créer un compte</a>
            </p>
        </form>
    </div>

</body>
</html>