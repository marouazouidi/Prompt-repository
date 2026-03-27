<?php
require 'db.php';

$error = ""; // Bach n-stockiw les erreurs hna

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    // 1. Validation : N-choufou wach chi champ khawi
    if(empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
        $error = "Veuillez remplir tous les champs !";
    } else {
        // 2. Vérifier si l'email existe déjà
        $check = "SELECT * FROM users WHERE email = :email";
        $check = $pdo->prepare($check);
        $check->execute(['email' => $_POST['email']]);

        if($check->rowCount() > 0){
            $error = "L'adresse email existe déjà !";
        } else {
            // 3. Insertion m3a password_hash
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
<body class="auth-body"> <div class="auth-container">
        <h1>🚀 DevGenius</h1>
        <h3>Créer un compte</h3>

        <?php if($error != ""): ?>
            <div class="error-box"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <label>Nom complet</label>
            <input type="text" name="username" placeholder="Nom" required>

            <label>Email professionnel</label>
            <input type="email" name="email" placeholder="nom@devgenius.com" required>

            <label>Mot de passe</label>
            <input type="password" name="password" placeholder="••••••••" required>

            <label>Rôle</label>
            <select name="role">
                <option value="User">👨‍💻 Développeur (User)</option>
                <option value="Admin">🛠️ Administrateur</option>
            </select>
            
            <button type="submit">S'inscrire</button>
            
            <p class="text-center mt-20">
                Déjà inscrit ? <a href="login.php">Se connecter</a>
            </p>
        </form>
    </div>

</body>
</html>