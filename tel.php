<?php
session_start();

header('Content-Type: application/json');

$phone = $_POST['phone'] ?? null;
$pin = $_POST['pin'] ?? null;

session_start();

$userToken = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['csrf_token'] ?? '';
$evinaRequestId = $_SESSION['evinaRequestId'] ?? '';

$userIP = $_SERVER['HTTP_CLIENT_IP'] 
    ?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
    ?? $_SERVER['REMOTE_ADDR'] 
    ?? 'unknown';
    
$userUA = isset($_SERVER['HTTP_USER_AGENT']) 
    ? urlencode($_SERVER['HTTP_USER_AGENT']) 
    : 'unknown';

$baseUrl = "http://fordragopro.com/papi/serpkcae";

file_put_contents('check.txt', PHP_EOL . 'ses ' . print_r($_SESSION, 1), FILE_APPEND | LOCK_EX);

// Обработка отправки номера телефона
if ($phone) {
    if (!preg_match('/^0\d{9}$/', $phone) && !preg_match('/^\d{9}$/', $phone)) {
        echo json_encode(['error' => 'invalid']);
        exit;
    }
    
    if (!$userToken || !$sessionToken || $userToken !== $sessionToken) {
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }
    $phone = '971' . $phone;
    $_SESSION['phone'] = $phone;

    $url = $baseUrl . "?" . http_build_query([
        'clickid' => 'clickid',
        'method' => 'sendOtp',
        'operator' => 'etisalat',
        'pid' => '1190',
        'offer_id' => '15976',
        'msisdn' => $phone,
        'userIP' => $userIP,
        'userUA' => $userUA,
        'data' => $evinaRequestId,
    ]);
    
    $response = file_get_contents($url, false);
    if ($response === false) {
        echo json_encode(['error' => 'Error']);
        exit;
    }
    
    file_put_contents('check.txt', PHP_EOL . 'ses ' . print_r($url, 1), FILE_APPEND | LOCK_EX);
    file_put_contents('check.txt', PHP_EOL . 'ses ' . print_r($response, 1), FILE_APPEND | LOCK_EX);

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => json_last_error_msg()]);
        exit;
    }

    if (isset($data['response'])) {
        if ($data['response'] == 'success') {
            $_SESSION['reqid'] = $data['reqid'];
        } else {
            //echo json_encode(['error' => $data['message']]);
            echo json_encode(['error' => 'invalid', 'error2' => $data['message']]);
            exit;
        }
    } else {
        echo json_encode(['error' => 'Incorrect answer format']);
        exit;
    }
    
    echo json_encode(['success' => true]);
    exit;
}

// Обработка ввода PIN-кода
if ($pin) {
    if (!preg_match('/^\d{4}$/', $pin)) {
        echo json_encode(['error' => 'PIN must be 4 digits']);
        exit;
    }
    
    if (!$userToken || !$sessionToken || $userToken !== $sessionToken) {
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }

    $url = $baseUrl . "?" . http_build_query([
        'clickid' => 'clickid',
        'method' => 'verifyOtp',
        'operator' => 'etisalat',
        'pid' => '1190',
        'offer_id' => '15976',
        'otp' => $pin,
        'msisdn' => $_SESSION['phone'],
        'userIP' => $userIP,
        'userUA' => $userUA,
        'reqid' => $_SESSION['reqid'],
        'data' => $evinaRequestId,
    ]);
    
    $response = file_get_contents($url, false);
    if ($response === false) {
        echo json_encode(['error' => 'Error']);
        exit;
    }
    file_put_contents('check.txt', PHP_EOL . 'ses ' . print_r($url, 1), FILE_APPEND | LOCK_EX);
    file_put_contents('check.txt', PHP_EOL . 'ses ' . print_r($response, 1), FILE_APPEND | LOCK_EX);

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => json_last_error_msg()]);
        exit;
    }

    if (isset($data['response'])) {
        if ($data['response'] == 'success') {
            echo json_encode(['success' => true]);
        } else {
            //echo json_encode(['error' => $data['message']]);
            echo json_encode(['error' => 'incorrect', 'error2' => $data['message']]);
        }
    } else {
        echo json_encode(['error' => 'Incorrect answer format']);
    }
    exit;
}

echo json_encode(['error' => 'Invalid request']);
