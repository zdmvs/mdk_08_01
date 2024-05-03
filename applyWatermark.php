<?php
function applyWatermarkFromDB($imageId, $outputDir, $userId, $position, $opacity)
{
    require_once 'config.php';

    global $conn;

    $sql = "SELECT * FROM images WHERE id = '$imageId'";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $imageFile = $row["image_name"];
            $watermarkFile = $row["watermark_name"];

            $sql_update = "UPDATE images SET user_id = '$userId' WHERE id = '$imageId'";
            $update_result = $conn->query($sql_update);
            if (!$update_result) {
                echo "Ошибка при обновлении user_id: " . $conn->error;
                return;
            }

            $uploadDir = __DIR__ . '/uploads/';
            $imageExtension = pathinfo($imageFile, PATHINFO_EXTENSION);
            $outputFile = $outputDir . '/downloads/' . basename($imageFile);

            switch (strtolower($imageExtension)) {
                case 'png':
                    $image = imagecreatefrompng($uploadDir . $imageFile);
                    break;
                case 'jpeg':
                case 'jpg':
                    $image = imagecreatefromjpeg($uploadDir . $imageFile);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($uploadDir . $imageFile);
                    break;
                default:
                    echo "Неподдерживаемый тип изображения";
                    return;
            }

            if (!$image) {
                echo "Не удалось создать изображение из файла.";
                return;
            }

            $watermark = imagecreatefrompng($uploadDir . $watermarkFile);

            $watermarkWidth = imagesx($watermark);
            $watermarkHeight = imagesy($watermark);

            $imageWidth = imagesx($image);
            $imageHeight = imagesy($image);

            // Определяем размер водяного знака в 25% от ширины изображения
            $newWatermarkWidth = round($imageWidth * 0.25);
            $newWatermarkHeight = round($watermarkHeight * ($newWatermarkWidth / $watermarkWidth));

            // Масштабируем изображение водяного знака до новых размеров
            $resizedWatermark = imagecreatetruecolor($newWatermarkWidth, $newWatermarkHeight);
            imagealphablending($resizedWatermark, false);
            imagesavealpha($resizedWatermark, true);
            imagecopyresampled($resizedWatermark, $watermark, 0, 0, 0, 0, $newWatermarkWidth, $newWatermarkHeight, $watermarkWidth, $watermarkHeight);

            // Определяем координаты водяного знака в зависимости от выбранного положения
            switch ($position) {
                case 'top-left':
                    $x = 0;
                    $y = 0;
                    break;
                case 'top-right':
                    $x = $imageWidth - $newWatermarkWidth;
                    $y = 0;
                    break;
                case 'bottom-left':
                    $x = 0;
                    $y = $imageHeight - $newWatermarkHeight;
                    break;
                case 'bottom-right':
                    $x = $imageWidth - $newWatermarkWidth;
                    $y = $imageHeight - $newWatermarkHeight;
                    break;
                default:
                    $x = 0;
                    $y = 0;
            }


// Применение прозрачности к водяному знаку
$roundedOpacity = round($opacity); // Округляем значение прозрачности до ближайшего целого числа
imagefilter($resizedWatermark, IMG_FILTER_COLORIZE, 0, 0, 0, 100 - $opacity);

// Наложение водяного знака на изображение с прозрачностью
imagecopy($image, $resizedWatermark, $x, $y, 0, 0, $newWatermarkWidth, $newWatermarkHeight);




            // Создаем имя файла для обработанного изображения
            $processedImageName =  $imageId . '_' . $imageFile;

            switch (strtolower($imageExtension)) {
                case 'png':
                    imagepng($image, $outputDir . $processedImageName);
                    break;
                case 'jpeg':
                case 'jpg':
                    imagejpeg($image, $outputDir . $processedImageName);
                    break;
                case 'gif':
                    imagegif($image, $outputDir . $processedImageName);
                    break;
                default:
                    echo "Неподдерживаемый тип изображения";
                    return;
            }

            imagedestroy($image);
            imagedestroy($resizedWatermark);

            $sql_update = "UPDATE images SET processed_image_name = '$processedImageName' WHERE id = '$imageId'";
            $update_result = $conn->query($sql_update);
            if (!$update_result) {
                echo "Ошибка при обновлении processed_image_name: " . $conn->error;
                return;
            }

        } else {
            echo "Изображение не найдено в базе данных.";
            return;
        }
    } else {
        echo "Ошибка при выполнении запроса: " . $conn->error;
        return;
    }
}
?>
