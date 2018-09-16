<?php

require_once ('db.php');

if ($_POST) {
    $stmt = $pdo->prepare("SELECT id FROM user WHERE login = ?");
    $stmt->execute(array($_POST["login"]));
    $row = $stmt->fetch();

    if ($row) {
        echo "Пользователь с таким логином уже существует";
    } else {
        $stmt = $pdo->prepare("INSERT INTO user (login, password) VALUES (?,?)");
        $stmt->execute(array($_POST['login'], $_POST['password']));
        header('Location: index.php');
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
    <h1>Регистрация</h1>
    <form method="POST">
        <label for="login">логин</label>
        <input type="text" name="login" id="login">
        <label for="login">пароль</label>
        <input type="password" name="password" id="password">
        <input type="submit" name="enter" value="Зарегистрироваться">
    </form>
    <ul>
        <li><a href="index.php">Вход</a></li>
        <li><a href="register.php">Регистрация</a></li>
    </ul>
</body>
</html>
