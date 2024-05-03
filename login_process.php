<?php
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['role'];

            header('Location: index.php');
        } else {
            echo "Неверный пароль";
        }
    } else {
        echo "Пользователь не найден";
    }

    $sql->close();
}

$conn->close();
?>
