<?php

require_once ('db.php');

session_start();

//Добавление задачи
if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO task (user_id, assigned_user_id, description, date_added) VALUES (:user_id, :assigned_user_id, :description, :date_added)");
    $stmt->execute(array("user_id" => $_SESSION['user_id'], "assigned_user_id" => $_SESSION['user_id'], "description" => $_POST['desc'], ':date_added' => $_POST['date']));
}

//Вывод всех задач
$tasks = $pdo->prepare("SELECT * FROM task WHERE user_id = ? ORDER BY date_added ASC");
$tasks->execute(array($_SESSION['user_id']));


//Удаление задачи
if (isset($_GET['del'])) {
    $stmt = $pdo->prepare("DELETE FROM task WHERE user_id= ? AND id = ? LIMIT 1");
    $stmt->execute(array($_SESSION['user_id'], $_GET['del']));
    header("Location: task.php");
}


//Изменения статуса выполнения задачи
if (isset($_GET['status'])) {
    $stmt = $pdo->prepare("UPDATE task SET is_done = ? WHERE user_id = ? AND id = ? LIMIT 1");
    if ($_GET['status'] == 0) {
        $stmt->execute(array(1,$_SESSION['user_id'], $_GET['id']));
    } else {
        $stmt->execute(array(0,$_SESSION['user_id'], $_GET['id']));
    }
}


$assignedUserList = $pdo->query("SELECT * FROM user");

?>


<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>To Do</title>
</head>
<body>
    <form method="POST">
        <input type="text" name="desc" placeholder="Описание">
        <input type="date" name="date">
        <input type="submit" value="Добавить дело" />
    </form>
    <table border="1">
        <tr>
            <th>Дела</th>
            <th>Когда</th>
            <th>Статус</th>
            <th>Исполнитель</th>
            <th>Удалить дело</th>
        </tr>
        <?php foreach ($tasks as $task): ?>
            <?php $status = $task['is_done'] ? "Выполнено" : "Выполнить"; ?>
        <tr>
            <td><?= $task['description'] ?></td>
            <td><?= $task['date_added']?></td>
            <td><a href="task.php?status=<?= $task['is_done']?>&id=<?= $task['id']?>"><?= $status ?></a></td>
            <td>
                <form method="POST">
                    <input name="task_id" type="hidden" value="<?= $task['id'] ?>">
                    <select name="assigned_user_id">
                        <?php foreach ($assignedUserList as $assignedUser): ?>
                            <option <?php if ($task['assigned_user_id'] == $assignedUser['id']):?>
                                selected<?php endif; ?> value="<?= $assignedUser['id'] ?>">
                                <?= $assignedUser['login'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Делегировать</button>
                </form>
            </td>
            <td><a href="task.php?del=<?= $task['id'] ?>">Удалить</a></td>

        </tr>
        <?php endforeach; ?>
    </table>
    <br>

    <a href="logout.php">Выход</a>


</body>
</html>
