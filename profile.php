<?php
session_start();
require_once('config.php');

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Перенаправляем неавторизованных пользователей на страницу входа
    exit();
}

// Ваш код для личного кабинета здесь
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Получаем изображения пользователя из базы данных
$sql = "SELECT * FROM images WHERE user_id = $user_id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
<link rel="stylesheet" href="style.css">
<link rel="icon" href="img/favicon.ico" type="image/x-icon">

<style>
table {
    width: 700px;
    margin: 20px auto; /* Центрирование таблицы */
    border-collapse: collapse;
}

th, td {
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

tr:nth-child(even) {
}

tr:hover {
    background-color: #ddd;
}

td button {
    padding: 5px 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
}

td button:hover {
    background-color: #45a049;
}

td a {
    padding: 5px 10px;
    background-color: #f44336;
    color: white;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

td a:hover {
    background-color: #d32f2f;
}
/* Стили для всплывающего окна */
#editPopup {
    display: none; /* По умолчанию скрыто */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    z-index: 9999;
}

#editPopup h2 {
    margin-top: 0;
    font-size: 18px;
    color: #333;
}

#editPopup form {
    margin-bottom: 0;
}

#editPopup input[type="text"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
    box-sizing: border-box;
}

#editPopup input[type="submit"],
#editPopup button {
    padding: 10px 20px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

#editPopup input[type="submit"].save {
    background-color: #4CAF50;
    color: #fff;
}

#editPopup button.cledpp {
    background-color: #f44336;
    color: #fff;
    margin-left: 10px;
}

/* Стили для затемненного фона */
.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9998;
}

/* Стили для активации всплывающего окна */
.overlay.active,
#editPopup.active {
    display: block;
}
.dropdown {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 60px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            padding: 10px;
            border-radius: 5px;
        }
.delred{
color: red;
}
        .dropdown:hover .dropdown-content {
            display: block;
        }
</style>
    <script>
    function openEditPopup(userId, currentUsername, currentRole) {
        document.getElementById('editUserId').value = userId;
        document.getElementById('editUsername').value = currentUsername;

        document.getElementById('editPopup').style.display = 'block';
    }

    function closeEditPopup() {
        document.getElementById('editPopup').style.display = 'none';
    }
    </script>
</head>

<body>

<?php include 'header.php'; ?>

    <h1>Привет, <?php echo $username; ?>!</h1>
    <p>Ваш ID: <?php echo $user_id; ?></p>
    <p>Ваша роль: <?php echo $role; ?></p>

    <?php
    // Отображение изображений только для определенной роли
    if ($role == 'admin') {
        include 'config.php';

        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Роль</th>
                <th style='width: 31%;'>Действия</th>
            </tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . $row["username"] . "</td>
                    <td>" . $row["role"] . "</td>
                    <td>";

                // Проверяем, если текущий пользователь не админ, отображаем кнопку "Удалить"
                if ($row["role"] != 'admin') {
                    echo "<button onclick='openEditPopup(" . $row["id"] . ", \"" . $row["username"] . "\", \"" . $row["role"] . "\")'>Редактировать</button>
                          <a href='delete_user.php?id=" . $row["id"] . "'>Удалить</a>";
                } else {
                    // Если это админ, отображаем только кнопку "Редактировать"
                    echo "<button onclick='openEditPopup(" . $row["id"] . ", \"" . $row["username"] . "\", \"" . $row["role"] . "\")'>Редактировать</button>";
                }

                echo "</td></tr>";
            }

            echo "</table>";
        } else {
            echo "0 результатов";
        }

        $conn->close();
    }
    ?>

    <?php
    // Отображение изображений пользователя
    if ($result->num_rows > 0) {
        echo "<h2>Ваши изображения:</h2>";
    ?>
<div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-content: flex-end;">
        <?php
        while ($row = $result->fetch_assoc()) {
            $imageId = $row['id'];
            $imageName = $row['processed_image_name'];
            echo "<div class='dropdown' style='margin: 10px;'>";
            echo "<img src='showImage.php?id=$imageId' alt='$imageName' style='width: 100px; height: 100px; object-fit: cover; cursor: pointer;'>";
            echo "<div class='dropdown-content'>";
            echo "<a href='showImage.php?id=$imageId' class='opengreen'>Открыть</a><br>";
            echo "<a href='download.php?id=$imageId'>Скачать</a><br>";
            echo "<a href='deleteImage.php?id=$imageId' class='delred'>Удалить</a>";
            echo "</div>";
            echo "</div>";
        }
        ?>
    </div>
    <?php
    }
    ?>
    

<div id="editPopup">
    
    <form action="update_user.php" method="post">
        <h2>Редактировать пользователя</h2>
        <input type="hidden" name="editUserId" id="editUserId">
        Имя пользователя: <input type="text" name="editUsername" id="editUsername" required><br>
        <input type="submit" class="save" value="Сохранить">
        <button type="button" class="cledpp" onclick="closeEditPopup()">Отмена</button>
    </form>
</div>


    <footer>
        <p>&copy; 2024 КП</p>
    </footer>

</body>

</html>