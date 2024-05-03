<?php
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = "user";

    $sql = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $sql->bind_param("sss", $username, $password, $role);

    if ($sql->execute()) {
        header('Location: login.php');
    } else {
        echo "Ошибка при регистрации: " . $conn->error;
    }

    $sql->close();
}

$conn->close();
?>
