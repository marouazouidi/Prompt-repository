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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - DevGenius</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

    <nav class="auth-nav">
        <span class="auth-nav-brand">DevGenius</span>
        <a href="register.php" class="auth-nav-link">S'inscrire</a>
    </nav>

    <div class="auth-container">
        <div class="auth-card">

            <h1> Connexion</h1>
            <h3>Connexion au Repository DevGenius</h3>

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

                <p class="text-center">
                    Nouveau ici ? <a href="register.php">Créer un compte</a>
                </p>
            </form>

        </div>
    </div>

    <footer class="auth-footer">
        <p>
            <a href="#">Terms</a>
            <a href="#">Privacy</a>
            <a href="#">Support</a>
        </p>
        <p style="margin-top:.4rem;">© 2026 DevGenius. All rights reserved.</p>
    </footer>

</body>
</html>