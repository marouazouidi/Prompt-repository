<?php
require 'db.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
    exit();
}

$cats = $pdo->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']=== 'POST'){
    $sql = "INSERT INTO prompts(title , content, category_id, user_id)
            VALUES (:title, :content, :category, :user)";
    
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'category' => $_POST['category_id'],
        'user'=> $_SESSION['user_id']
    ]);


    header('Location: dashboard.php');
    exit();
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div >
        <h2>➕ Ajouter de nouvelles Prompts</h2>
        
        <form method="POST">

            <input name="title" placeholder="le titre">
            <textarea name="content" placeholder="Prompt"></textarea>
            
            <select name="category_id">  
                <option value="">Selectionner une catégorie</option>  
            <?php foreach($cats as $c){ ?> 
            <option value="<?php echo $c['id']; ?>">
                <?php echo $c['name'] ?>
            </option>
            <?php }?>
            </select>
            
            <button>Ajouter Prompts</button>
        
        </form>
    </div>


    

</body>
</html>