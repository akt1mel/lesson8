<?php

require_once ('db.php');

session_start();
$error = '';
//Добавление задачи
if (isset($_POST['add_task'])) {
    if($_POST['desc']){
      $stmt = $pdo->prepare("INSERT INTO task (user_id, assigned_user_id, description, date_added) VALUES (:user_id, :assigned_user_id, :description, :date_added)");
      $stmt->execute(array("user_id" => $_SESSION['user_id'], "assigned_user_id" => $_SESSION['user_id'], "description" => $_POST['desc'], ':date_added' => date('Y-m-d H:i:s')));
  } else {
    $error = 'Введите описание задачи';
  }
}

//Вывод всех задач
$myTasks = $pdo->prepare("SELECT * FROM task WHERE user_id = ? ORDER BY date_added ASC");
$myTasks->execute(array($_SESSION['user_id']));


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
        header("Location: task.php");
    } else {
        $stmt->execute(array(0,$_SESSION['user_id'], $_GET['id']));
    }
}


$assignedUserList = $pdo->query("SELECT * FROM user")->fetchAll();


//Назначение задачи другому лицу
if (isset($_POST['assign'])) {
  $stmt = $pdo->prepare('UPDATE task SET assigned_user_id = ? WHERE id = ? LIMIT 1');
  $stmt->execute(array($_POST['assigned_user_id'], $_POST['task_id']));
  header("Location: task.php");
}


//Вывод дел, назначенных другими лицами
$assignedTasks = $pdo->prepare('SELECT * FROM task t INNER JOIN user u ON u.id=t.user_id WHERE t.assigned_user_id = ?');
$assignedTasks->execute(array($_SESSION['user_id']));

$taskCounter = $pdo->prepare('SELECT count(*) FROM task t WHERE t.user_id = ? OR t.assigned_user_id = ?');
$taskCounter->execute(array($_SESSION['user_id'],$_SESSION['user_id']));
$taskCount = $taskCounter->fetchColumn();
?>


<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>To Do</title>
</head>
<body>
  <h2>Мои дела</h2>
    <?= $error ?>
    <form method="POST">
        <input type="text" name="desc" placeholder="Описание">
        <input type="submit" name="add_task" value="Добавить дело">
    </form>
    <table border="1">
        <tr>
            <th>Дела</th>
            <th>Когда</th>
            <th>Статус</th>
            <th>Исполнитель</th>
            <th>Удалить дело</th>
        </tr>
        <?php foreach ($myTasks as $task): ?>
            <?php $status = $task['is_done'] ? "Выполнено" : "В процессе"; ?>
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
                    <input type="submit" name="assign" value="Делегировать">
                </form>
            </td>
            <td><a href="task.php?del=<?= $task['id'] ?>">Удалить</a></td>

        </tr>
        <?php endforeach; ?>
    </table>
    <br>

    <h2>Дела, назначенные на меня</h2>
    <table border="1">
        <tr>
            <th>Дела</th>
            <th>Когда</th>
            <th>Статус</th>
            <th>Автор</th>
        </tr>
        <?php foreach ($assignedTasks as $task): ?>
          <?php $status = $task['is_done'] ? "Выполнено" : "В процессе"; ?>
          <tr>
            <td><?= $task['description'] ?></td>
            <td><?= $task['date_added']?></td>
            <td><a href="task.php?status=<?= $task['is_done']?>&id=<?= $task['id']?>"><?= $status ?></a></td>
            <td><?= $task['login']?></td>
          </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <h3>Итого дел: <?= $taskCount ?></h3>
    <a href="logout.php">Выход</a>


</body>
</html>
