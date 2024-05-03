<?php
require_once('config.php');

if (isset($_GET['id'])) {
    $imageId = $_GET['id'];

    // Получаем путь к обработанному изображению из базы данных
    $sql = "SELECT processed_image_name FROM images WHERE id = '$imageId'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $processedImageName = 'downloads/' . $row['processed_image_name']; // Добавляем префикс "downloads/"

        // Определяем MIME-тип изображения
        $imageMimeType = mime_content_type($processedImageName);

        // Устанавливаем заголовки для корректного отображения изображения
        header("Content-Type: $imageMimeType");

        // Выводим содержимое изображения
        readfile($processedImageName);
    } else {
        echo "Изображение не найдено в базе данных.";
    }
} else {
    echo "ID изображения не указано.";
}
?>
