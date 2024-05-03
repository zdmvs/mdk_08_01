<?php
session_start();
require_once 'config.php';
require_once 'applyWatermark.php';

function isUserLoggedIn()
{
    return isset($_SESSION['user_id']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadDir = __DIR__ . '/uploads/';

    $imageFile = basename($_FILES['image']['name']);
    $watermarkFile = basename($_FILES['watermark']['name']);
    $position = $_POST['position'];

    // Округляем значение прозрачности до ближайшего целого числа
    $opacity = round($_POST['opacity']);

    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageFile);
    move_uploaded_file($_FILES['watermark']['tmp_name'], $uploadDir . $watermarkFile);

    $userId = isUserLoggedIn() ? $_SESSION['user_id'] : 0;

    $sql = "INSERT INTO images (image_name, watermark_name, user_id) VALUES ('$imageFile', '$watermarkFile', '$userId')";

    if ($conn->query($sql) === true) {
        /* echo "<p>Изображение успешно добавлено в базу данных.</p>"; */

        $imageId = $conn->insert_id;

        // Передаем округленное значение прозрачности в функцию applyWatermarkFromDB
        applyWatermarkFromDB($imageId, __DIR__ . '/downloads/', $userId, $position, $opacity);
    } else {
        echo "<p class='error'>Ошибка: " . $sql . "<br>" . $conn->error . "</p>";
    }
}

$userId = isUserLoggedIn() ? $_SESSION['user_id'] : null;
$sql = "SELECT id, processed_image_name FROM images WHERE user_id = '$userId' OR user_id IS NULL ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление водяного знака</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
</head>
<body>
<header>
    <h1>AquaMark</h1>
    <?php if (isUserLoggedIn()): ?>
        <h3>Привет, <?php echo $_SESSION['username']; ?></h3>
    <?php endif;?>
    <nav>
        <ul>
            <li><a href="index.php">Главная</a></li>
            <li><a href="insrtuct.php">Инструкция</a></li>
            <?php if (isUserLoggedIn()): ?>
                <li><a href="profile.php">Личный кабинет</a></li>
                <li><a href="logout.php">Выйти</a></li>
            <?php else: ?>
                <li><a href="login.php">Войти</a></li>
                <li><a href="register.php">Зарегистрироваться</a></li>
            <?php endif;?>
        </ul>
    </nav>
</header>
<section class="wmk">
    <article class="leftwmk">
        <form action="" method="post" enctype="multipart/form-data">
                <label class="input-file">                    
                    <input type="file" name="image" id="image" accept="image/*" required>
                    <span class="input-file-btn">Выберите файл</span>
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
<br><br>
            <label for="opacity">Выберите прозрачность водяного знака:</label>
            <input type="range" id="opacity" name="opacity" min="0" max="100" value="50" step="1" oninput="updateOpacityValue()">


<br><br>
            <input type="submit" class="confirm" value="Применить водяной знак">
            <br>
            <a href="pocket.php" class="pocket">Обработать несколько изображений</a>
        </form>
    </article>
    <article class="rightwmk">
        <?php
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imageId = $row["id"];
    $imageName = $row["processed_image_name"];
    echo '<a class="button" href="download.php?id=' . $imageId . '">Скачать изображение</a>';
    echo '<img class="thumbnail" src="showImage.php?id=' . $imageId . '" alt="' . $imageName . '">';
} else {
    echo "<p class='error'>Добавьте изображение и водяной знак.</p>";
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('.input-file input[type=file]').on('change', function(){
        let file = this.files[0];
        $(this).closest('.input-file').find('.input-file-text').html(file.name);
    });
});
</script>

</body>
</html>
