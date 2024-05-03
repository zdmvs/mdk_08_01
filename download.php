<?php
require_once('config.php');

if(isset($_GET['id'])) {
    $imageId = $_GET['id'];
    $outputDir = __DIR__ ;

    // Получаем информацию о изображении из базы данных
    $sql = "SELECT * FROM images WHERE id = $imageId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $processedImageName = $row['processed_image_name'];
        
        // Формируем путь к файлу
        $imagePath = $outputDir . '/downloads/'. basename($processedImageName);

        // Проверяем существует ли файл
        if (file_exists($imagePath)) {
            // Отправляем заголовки для скачивания файла
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($imagePath));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($imagePath));
            readfile($imagePath);
        } else {
            echo "Файл с изображением не найден.";
        }
    } else {
        echo "Изображение не найдено в базе данных.";
    }
} else {
    echo "ID изображения не указано.";
}
?>
