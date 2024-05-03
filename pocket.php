<?php
session_start();
require_once 'config.php';
require_once 'applyWatermark.php';

// Проверяем, авторизован ли пользователь
function isUserLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Проверяем, была ли отправлена форма и пользователь авторизован
if ($_SERVER["REQUEST_METHOD"] == "POST" && isUserLoggedIn()) {
    $uploadDir = __DIR__ . '/uploads/';

    // Получаем путь к загруженному водяному знаку
    $watermarkFile = basename($_FILES['watermark']['name']);
    $position = $_POST['position'];
    $opacity = $_POST['opacity'];


    // Перемещаем загруженный водяной знак в нужное место
    move_uploaded_file($_FILES['watermark']['tmp_name'], $uploadDir . $watermarkFile);

    // Ограничение количества обрабатываемых изображений до 50
    $maxImages = 50;
    $countImages = count($_FILES['images']['tmp_name']);
    if ($countImages > $maxImages) {
        echo "<p class='error'>Вы можете загрузить не более $maxImages изображений за раз.</p>";
    } else {
        // Обрабатываем все загруженные файлы
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $imageFile = basename($_FILES['images']['name'][$key]);

            // Перемещаем загруженное изображение в нужное место
            move_uploaded_file($_FILES['images']['tmp_name'][$key], $uploadDir . $imageFile);

            // Получаем user_id из сессии
            $userId = $_SESSION['user_id'];

            // Вставляем информацию об изображении в базу данных
            $sql = "INSERT INTO images (image_name, watermark_name, user_id) VALUES ('$imageFile', '$watermarkFile', '$userId')";

            if ($conn->query($sql) === true) {
                // Получаем ID только что добавленного изображения
                $imageId = $conn->insert_id;

                // Применяем водяной знак к изображению
                applyWatermarkFromDB($imageId, __DIR__ . '/downloads/', $userId, $position, $opacity);
            } else {
                echo "<p class='error'>Ошибка: " . $sql . "<br>" . $conn->error . "</p>";
            }
        }
    }
} elseif (!isUserLoggedIn()) {
    header("Location: login.php"); // Перенаправляем неавторизованных пользователей на страницу входа
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пакетная обработка изображений</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">

</head>

<body>
    <header>
        <h1>Пакетная обработка изображений</h1>
        <?php if (isUserLoggedIn()): ?>
            <h3>Привет, <?php echo $_SESSION['username']; ?></h3>
        <?php endif;?>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="insrtuct.php">Инструкция</a></li>
                <li><a href="profile.php">Личный кабинет</a></li>
                <li><a href="logout.php">Выйти</a></li>
            </ul>
        </nav>
    </header>

    <section class="wmk">
    <article class="leftwmk">
        <form action="" method="post" enctype="multipart/form-data">
            <label class="input-file">                    
                    <input type="file" name="images[]" id="images" accept="image/*" multiple required>
                    <span class="input-file-btn">Выберите файлы</span>
                    <span class="input-file-text" type="text"></span>

                </label>
                <br>
                <label class="input-file">
                    <input type="file" name="watermark" id="watermark" accept="image/*" required>
                    <span class="input-file-btn">Выберите водяной знак:</span>
                    <span class="input-file-text" type="text"></span>
                </label>

            <label for="position">Выберите положение водяного знака:</label>
            <select name="position" id="position">
                <option value="top-left">Верхний левый угол</option>
                <option value="top-right">Верхний правый угол</option>
                <option value="bottom-left">Нижний левый угол</option>
                <option value="bottom-right">Нижний правый угол</option>
            </select>

            <label for="opacity">Выберите прозрачность водяного знака:</label>
            <input type="range" id="opacity" name="opacity" min="0" max="100" value="50">

            <input type="submit" value="Применить водяной знак">
            <br>
            <a href="index.php" class="pocket">Назад</a>

        </form>
        </article>
        <article class="rightwmk">
    <?php
// Проверяем, была ли отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST" && isUserLoggedIn()) {
    // Выводим сообщения об успешном добавлении изображений
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $imageFile = basename($_FILES['images']['name'][$key]);
        echo "<p>Изображение $imageFile успешно добавлено в базу данных.</p>";
    }
}
?>
</article>


    </section>

    <footer>
        <p>&copy; 2024 КП</p>
    </footer>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        var positionSelect = document.getElementById('position');
        positionSelect.addEventListener('change', function() {
            var selectedPosition = positionSelect.options[positionSelect.selectedIndex].value;
            // Дополнительная логика для обработки выбора положения
        });

        var opacityInput = document.getElementById('opacity');
        opacityInput.addEventListener('input', function() {
            var selectedOpacity = opacityInput.value;
            // Дополнительная логика для обработки изменения прозрачности
        });
    });
</script>
</body>

</html>
