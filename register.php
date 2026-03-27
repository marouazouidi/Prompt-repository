<?php
require 'db.php';

$error = ""; 

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    if(empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
        $error = "Veuillez remplir tous les champs !";
    } else {

        $check = "SELECT * FROM users WHERE email = :email";
        $check = $pdo->prepare($check);
        $check->execute(['email' => $_POST['email']]);

        if($check->rowCount() > 0){
            $error = "L'adresse email existe déjà !";
        } else {

            $sql = "INSERT INTO users(name, email, password, role)
                   VALUES(:username, :email, :password, :role)";
                   
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'username' => $_POST['username'],
                'email'    => $_POST['email'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'role'     => $_POST['role']
            ]);
                
            header('Location: login.php?success=account_created');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - DevGenius</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

    <nav class="auth-nav">
        <span class="auth-nav-brand">DevGenius</span>
        <a href="login.php" class="auth-nav-link">Connexion</a>
    </nav>

    <div class="auth-container">
        <div class="auth-card">

            <h1>Créer un compte</h1>
            <h3>Rejoignez la communauté DevGenius</h3>

            <?php if($error != ""): ?>
                <div class="error-box"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <label>Full Name</label>
                <input type="text" name="username" placeholder="Nom" required>

                <label>Email Address</label>
                <input type="email" name="email" placeholder="nom@gmail.com" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>

                <label>Rôle</label>
                <select name="role">
                    <option value="User">👨‍💻 Développeur (User)</option>
                    <option value="Admin">🛠️ Administrateur</option>
                </select>

                <button type="submit">Create Account</button>

                <p class="text-center">
                    Déjà un compte ? <a href="login.php">Log In</a>
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
        <p style="margin-top:.4rem;">© 2024 DevGenius. All rights reserved.</p>
    </footer>

</body>
</html>
