<?php
require_once('config.php');

if(isset($_GET['id'])) {
    $imageId = $_GET['id'];

    // Удаляем изображение из базы данных
    $sql = "DELETE FROM images WHERE id = $imageId";
    if ($conn->query($sql) === TRUE) {
        // Успешно удалено, перенаправляем пользователя на страницу личного кабинета
        header('Location: profile.php');
        exit();
    } else {
        // Ошибка при удалении, перенаправляем пользователя на страницу личного кабинета с сообщением об ошибке
        header('Location: profile.php?error=delete_failed');
        exit();
    }
} else {
    // ID изображения не указан, перенаправляем пользователя на страницу личного кабинета с сообщением об ошибке
    header('Location: profile.php?error=image_id_missing');
    exit();
}
?>
