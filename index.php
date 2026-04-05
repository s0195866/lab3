<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// Подключение к БД для получения списка языков
$user = 'u82257';
$pass = '2312202';  // ЗАМЕНИ НА СВОЙ ПАРОЛЬ!

try {
    $pdo = new PDO("mysql:host=localhost;dbname=$user;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Получаем список языков из БД
    $languages = $pdo->query("SELECT id, name FROM programming_languages ORDER BY name")->fetchAll();
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Анкета разработчика</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #4a6fa5 0%, #2c3e50 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .form-content {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .required:after {
            content: " *";
            color: #e74c3c;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #4a6fa5;
            box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.1);
        }

        .radio-group {
            display: flex;
            gap: 20px;
            padding: 10px 0;
        }

        .radio-group label {
            display: inline-flex;
            align-items: center;
            font-weight: normal;
            cursor: pointer;
        }

        .radio-group input[type="radio"] {
            margin-right: 8px;
            cursor: pointer;
            width: auto;
        }

        select[multiple] {
            min-height: 150px;
        }

        select[multiple] option {
            padding: 8px;
            cursor: pointer;
        }

        select[multiple] option:hover {
            background: #e3f2fd;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input {
            width: auto;
            cursor: pointer;
        }

        .checkbox-group label {
            margin-bottom: 0;
            cursor: pointer;
            font-weight: normal;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 40px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: fadeIn 0.3s;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .error-list {
            margin-top: 10px;
            padding-left: 20px;
        }

        small {
            color: #666;
            font-size: 12px;
        }

        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Регистрационная анкета</h1>
            <p>Заполните все поля для сохранения информации</p>
        </div>
        <div class="form-content">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    ✅ Данные успешно сохранены! Спасибо за заполнение анкеты.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                <div class="alert alert-error">
                    ❌ При заполнении формы обнаружены ошибки:
                    <ul class="error-list">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            
            <form action="save.php" method="POST">
                <div class="form-group">
                    <label for="full_name" class="required">ФИО</label>
                    <input type="text" id="full_name" name="full_name" required
                           placeholder="Иванов Иван Иванович"
                           value="<?php echo isset($_GET['full_name']) ? htmlspecialchars($_GET['full_name']) : ''; ?>">
                    <small>Только буквы, пробелы и дефисы, не более 150 символов</small>
                </div>

                <div class="form-group">
                    <label for="phone" class="required">Телефон</label>
                    <input type="tel" id="phone" name="phone" required
                           placeholder="+7 (999) 123-45-67"
                           value="<?php echo isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : ''; ?>">
                    <small>Формат: +7XXXXXXXXXX или 8XXXXXXXXXX</small>
                </div>

                <div class="form-group">
                    <label for="email" class="required">E-mail</label>
                    <input type="email" id="email" name="email" required
                           placeholder="example@mail.ru"
                           value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="birth_date" class="required">Дата рождения</label>
                    <input type="date" id="birth_date" name="birth_date" required
                           value="<?php echo isset($_GET['birth_date']) ? htmlspecialchars($_GET['birth_date']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="required">Пол</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="gender" value="male" required
                                   <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'male') ? 'checked' : ''; ?>> Мужской
                        </label>
                        <label>
                            <input type="radio" name="gender" value="female"
                                   <?php echo (isset($_GET['gender']) && $_GET['gender'] == 'female') ? 'checked' : ''; ?>> Женский
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="languages" class="required">Любимый язык программирования</label>
                    <select name="languages[]" id="languages" multiple required size="6">
                        <?php foreach ($languages as $lang): ?>
                            <option value="<?php echo $lang['id']; ?>">
                                <?php echo htmlspecialchars($lang['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small>Удерживайте Ctrl (Cmd) для выбора нескольких языков</small>
                </div>

                <div class="form-group">
                    <label for="biography">Биография</label>
                    <textarea id="biography" name="biography" 
                              placeholder="Расскажите немного о себе..."><?php echo isset($_GET['biography']) ? htmlspecialchars($_GET['biography']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="contract" name="contract" required
                               <?php echo (isset($_GET['contract']) && $_GET['contract'] == 'on') ? 'checked' : ''; ?>>
                        <label for="contract" class="required">Я ознакомлен(а) с условиями контракта</label>
                    </div>
                </div>

                <button type="submit" class="btn-submit">💾 Сохранить</button>
            </form>
        </div>
    </div>
</body>
</html>
