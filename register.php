<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">

    <title>Регистрация</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #3498db;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        nav {
            color: #fff;
            padding: 10px 0;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }

        nav li {
            margin: 0 10px;
        }

        nav a {
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
        }

        form {
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            max-width: 400px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        input[type="password"] {
            position: relative;
        }

        .password-toggle {
            cursor: pointer;
            padding-bottom: 15px;
            display: flex;
        }

        input[type="submit"],
        a.button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 3px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            display: block;
            margin: 0 auto;
        }

        footer {
            margin-top: auto;
            padding: 10px;
            background-color: #3498db;
            color: #fff;
            text-align: center;
        }

        span {}
    </style>
</head>
<body>

    <header>
        <h1>Регистрация</h1>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="insrtuct.php">Инструкция</a></li>

                <li><a href="login.php">Войти</a></li>
            </ul>
        </nav>
    </header>

    <form action="register_process.php" method="post">
        <label for="username">Имя пользователя:</label>
        <input type="text" name="username" required>

        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required>

        <span class="password-toggle" onclick="togglePasswordVisibility('password')">Показать пароль</span>
        <input type="submit" value="Зарегистрироваться">        

    </form>

    <script>
        function togglePasswordVisibility(fieldId) {
            var field = document.getElementById(fieldId);
            field.type = (field.type === "password") ? "text" : "password";
        }
    </script>

    <footer>
        <p>&copy; 2024 КП</p>
    </footer>

</body>
</html>
