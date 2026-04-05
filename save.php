<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

$errors = array();

// Данные для подключения к БД
$user = 'u82257';
$pass = '2312202';  // ЗАМЕНИ НА СВОЙ ПАРОЛЬ!

// ========== ПРОВЕРКА ПОЛЕЙ ==========

// 1. ФИО
$full_name = trim($_POST['full_name']);
if ($full_name == "") {
    $errors[] = "Заполните ФИО";
} elseif (strlen($full_name) > 150) {
    $errors[] = "ФИО не длиннее 150 символов";
} elseif (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u', $full_name)) {
    $errors[] = "ФИО только из букв и пробелов";
}

// 2. Телефон
$phone = trim($_POST['phone']);
if ($phone == "") {
    $errors[] = "Заполните телефон";
}

// 3. Email
$email = trim($_POST['email']);
if ($email == "") {
    $errors[] = "Заполните email";
} elseif (!strpos($email, '@')) {
    $errors[] = "Email должен содержать @";
}

// 4. Дата рождения
$birth_date = $_POST['birth_date'];
if ($birth_date == "") {
    $errors[] = "Заполните дату рождения";
}

// 5. Пол
$gender = $_POST['gender'];
if ($gender != "male" && $gender != "female") {
    $errors[] = "Выберите пол";
}

// 6. Языки программирования
$languages = array();
if (isset($_POST['languages'])) {
    $languages = $_POST['languages'];
}
if (count($languages) == 0) {
    $errors[] = "Выберите хотя бы один язык программирования";
}

// 7. Биография (необязательное поле)
$biography = trim($_POST['biography']);

// 8. Чекбокс
$contract = 0;
if (isset($_POST['contract'])) {
    $contract = 1;
}
if ($contract == 0) {
    $errors[] = "Подтвердите ознакомление с контрактом";
}

// ========== ЕСЛИ ЕСТЬ ОШИБКИ ==========
if (count($errors) > 0) {
    $_SESSION['errors'] = $errors;
    header("Location: index.php");
    exit();
}

// ========== СОХРАНЕНИЕ В БД ==========
try {
    // Подключаемся к БД
    $db = new PDO("mysql:host=localhost;dbname=$user;charset=utf8", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Сохраняем заявку
    $stmt = $db->prepare("INSERT INTO applications (full_name, phone, email, birth_date, gender, biography, contract_agreed) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$full_name, $phone, $email, $birth_date, $gender, $biography, $contract]);
    
    // Получаем ID новой заявки
    $app_id = $db->lastInsertId();
    
    // 2. Сохраняем выбранные языки
    $stmt2 = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
    foreach ($languages as $lang_id) {
        $stmt2->execute([$app_id, $lang_id]);
    }
    
    // Всё хорошо, перенаправляем с успехом
    header("Location: index.php?success=1");
    exit();
    
} catch (PDOException $e) {
    $_SESSION['errors'] = array("Ошибка базы данных: " . $e->getMessage());
    header("Location: index.php");
    exit();
}
?>
