<?php
require_once('config.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'config.php';

    $userId = $_POST['editUserId'];
    $newUsername = $_POST['editUsername'];
    $newRole = $_POST['editRole'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssi", $newUsername, $newRole, $userId);

    if ($stmt->execute()) {
        header("Location: profile.php");
    } else {
        echo "Ошибка при обновлении информации о пользователе: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>
