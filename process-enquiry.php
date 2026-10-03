<?php
session_start();
require __DIR__ . '/database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$clean = fn($k, $max = 200) => mb_substr(trim(strip_tags($_POST[$k] ?? '')), 0, $max);
$formType = $clean('form_type', 20) === 'viewing' ? 'viewing' : 'portfolio';
$anchor   = $formType === 'viewing' ? '#contact' : '#home';

$name = $clean('name', 100); $phone = $clean('phone', 20); $email = $clean('email', 150);
$config = $clean('configuration', 50); $message = $clean('message', 1000);
if ($clean('day') || $clean('time')) $message = trim("Viewing: " . $clean('day') . ", " . $clean('time') . ". " . $message);

$digits = preg_replace('/\D/', '', $phone);
$error = '';
if ($name === '') $error = 'Please enter your full name.';
elseif (strlen($digits) < 10 || strlen($digits) > 13) $error = 'Please enter a valid mobile number.';
elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Please enter a valid email address.';

if (!$error && DB_ENABLED) {
    try {
        $conn = db_connect();
        $stmt = $conn->prepare("INSERT INTO enquiries (name, phone, email, configuration, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $phone, $email, $config, $message);
        $stmt->execute();
    } catch (Throwable $e) {
        error_log($e->getMessage());
        $error = 'DB error: ' . $e->getMessage();
    }
}

if ($error) {
    $_SESSION['flash'] = ['form' => $formType, 'msg' => $error, 'old' => compact('name', 'phone', 'email')];
    header('Location: index.php' . $anchor);
    exit;
}

// Saved -> open WhatsApp with a pre-filled message
$text = "Hello, I am $name.\n\nI am interested in Shiv Kailasa.\n\n"
      . "Mobile: $phone\n"
      . "Configuration: " . ($config ?: '2 / 3 BHK') . "\n"
      . ($message ? "Note: $message\n" : '')
      . "\nI would like to know more details and book a site visit.";
header('Location: https://wa.me/' . WA_NUMBER . '?text=' . rawurlencode($text));
exit;
