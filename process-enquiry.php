
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/database/db.php';

$clean = fn($key, $max = 200) =>
    mb_substr(
        trim(strip_tags((string)($_POST[$key] ?? ''))),
        0,
        $max
    );

$formType = $clean('form_type', 20) === 'viewing'
    ? 'viewing'
    : 'lead';

$anchor = $formType === 'viewing' ? '#contact' : '#home';

$name = $clean('name', 100);
$phone = $clean('phone', 20);
$email = $clean('email', 150);
$config = $clean('configuration', 50);
$message = $clean('message', 1000);

$purpose = $clean('purpose', 30);
$preferredLocation = $clean('preferred_location', 100);
$currentCity = $clean('current_city', 100);
$visitDay = $clean('day', 20);
$visitTime = $clean('time', 20);

if ($visitDay || $visitTime) {
    $message = trim(
        'Viewing: ' . $visitDay . ', ' .
        $visitTime . '. ' . $message
    );
}

$digits = preg_replace('/\D/', '', $phone);
$error = '';

if ($name === '') {
    $error = 'Please enter your full name.';
} elseif (!preg_match('/^[6-9][0-9]{9}$/', $digits)) {
    $error = 'Please enter a valid 10-digit mobile number.';
} elseif (
    $email !== '' &&
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    $error = 'Please enter a valid email address.';
}

if (!$error && $formType === 'lead') {
    if (!in_array($purpose, ['Residential', 'Investment'], true)) {
        $error = 'Please select a buying purpose.';
    } elseif (
        $purpose === 'Residential' &&
        !in_array($config, ['2 BHK', '3 BHK'], true)
    ) {
        $error = 'Please select a configuration.';
    } elseif (
        !in_array($preferredLocation, [
            'MIHAN, Nagpur',
            'Other area in Nagpur',
            'Outside Nagpur'
        ], true)
    ) {
        $error = 'Please select your preferred location.';
    }
}

if ($error !== '') {
    $_SESSION['flash'] = [
        'form' => $formType,
        'success' => false,
        'msg' => $error,
        'old' => [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'purpose' => $purpose,
            'configuration' => $config,
            'preferred_location' => $preferredLocation,
            'current_city' => $currentCity
        ]
    ];

    header('Location: index.php' . $anchor);
    exit;
}

$leadTag = $preferredLocation === 'MIHAN, Nagpur'
    ? 'MIHAN'
    : 'FUTURE_OPPORTUNITY';

$saved = false;
if (DB_ENABLED) {
    try {
        $conn = db_connect();
        $stmt = $conn->prepare(
            'INSERT INTO leads
                (form_type, name, phone, email, purpose, configuration,
                 preferred_location, current_city, visit_day, visit_time,
                 message, lead_tag)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'ssssssssssss',
            $formType, $name, $phone, $email, $purpose, $config,
            $preferredLocation, $currentCity, $visitDay, $visitTime,
            $message, $leadTag
        );
        $stmt->execute();
        $stmt->close();
        $conn->close();
        $saved = true;
    } catch (\Throwable $e) {
        // Don't block the WhatsApp handoff if the DB write fails.
        error_log('Lead DB insert failed: ' . $e->getMessage());
    }
}

$text = "Hello, I am $name.\n\n";
$text .= "I am interested in Shiv Kailasa.\n\n";
$text .= "Mobile: $phone\n";

if ($email !== '') {
    $text .= "Email: $email\n";
}

if ($formType === 'lead') {
    $text .= "Purpose: $purpose\n";
    $text .= "Configuration: " . ($config ?: 'Not selected') . "\n";
    $text .= "Preferred location: $preferredLocation\n";
    $text .= "Current city: $currentCity\n";
    $text .= "Lead tag: $leadTag\n";
} else {
    $text .= "Configuration: " . ($config ?: '2 / 3 BHK') . "\n";
}

if ($message !== '') {
    $text .= "Note: $message\n";
}

$text .= "\nI would like to know more details and book a site visit.";

// Replace with your real WhatsApp number, including country code.
$waNumber = '919022142587';
$waUrl = '';

if (strpos($waNumber, 'X') === false) {
    $waUrl = 'https://wa.me/' . $waNumber .
        '?text=' . rawurlencode($text);
}

if ($formType === 'lead') {
    $_SESSION['flash'] = [
        'form' => 'lead',
        'success' => true,
        'hot' => $leadTag === 'MIHAN',
        'msg' => $visitDay
            ? 'Your enquiry and visit slot have been received. Our team will confirm shortly.'
            : 'Your enquiry has been received. Our team will reach out shortly.',
        'wa' => $waUrl
    ];

    header('Location: index.php#home');
    exit;
}

if ($waUrl !== '') {
    header('Location: ' . $waUrl);
    exit;
}

$_SESSION['flash'] = [
    'form' => 'viewing',
    'success' => false,
    'msg' => 'Please configure your WhatsApp number to continue.',
    'old' => compact('name', 'phone', 'email')
];

header('Location: index.php#contact');
exit;
