<?php


require_once ('db.php');

if ($_POST) {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE (login = ?) AND (password = ?)");
    $stmt->execute(array($_POST['login'], $_POST['password']));
    $row = $stmt->fetch();

    if (!$row) {
        echo 'Неверный логин или пароль';
    } else {
        session_start();
        $_SESSION['user_id'] = $row['id'];
        header('Location: task.php');
    }
}



?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>To Do</title>
</head>
<body>
    <h1>Вход</h1>
    <form method="POST">
        <label for="login">логин</label>
        <input type="text" name="login" id="login">
        <label for="login">пароль</label>
        <input type="password" name="password" id="password">
        <input type="submit" name="enter" value="вход">
    </form>
    <ul>
        <li><a href="index.php">Вход</a></li>
        <li><a href="register.php">Регистрация</a></li>
    </ul>
</body>
</html>
