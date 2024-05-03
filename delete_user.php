<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Перенаправляем неавторизованных пользователей на страницу входа
    exit();
}

// Проверяем, является ли пользователь администратором
if ($_SESSION['role'] !== 'admin') {
    echo "У вас нет прав для выполнения этого действия.";
    exit();
}

// Проверяем, был ли передан идентификатор пользователя для удаления
if (isset($_GET['id'])) {
    $userToDeleteId = $_GET['id'];

    include 'config.php';

    // Предотвращаем SQL-инъекции с использованием подготовленных запросов
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userToDeleteId);

    if ($stmt->execute()) {
        header('Location: profile.php');
    } else {
        echo "Ошибка при удалении пользователя: " . $stmt->error;
    }

    // Закрываем соединение с базой данных
    $stmt->close();
    $conn->close();
} else {
    echo "Не был предоставлен идентификатор пользователя для удаления.";
}
?>
