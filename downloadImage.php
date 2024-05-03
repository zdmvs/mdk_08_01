<?php
require_once('config.php');
require_once('applyWatermark.php');

if(isset($_GET['id'])) {
    $imageId = $_GET['id'];
    $outputDir = __DIR__ ;
    applyWatermarkFromDB($imageId, $outputDir);

    // Получаем путь к водяно-замарознному файлу
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
    echo "ID изображения не указано.";
}
?>
