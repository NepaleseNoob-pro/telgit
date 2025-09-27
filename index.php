<?php
// Set header to return JSON
header('Content-Type: application/json');

// Get Bot Token and Chat ID from Environment Variables
$botToken = getenv('TELEGRAM_BOT_TOKEN');
$chatId = getenv('TELEGRAM_CHAT_ID');

if (!$botToken || !$chatId) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Server configuration error: TELEGRAM_BOT_TOKEN and TELEGRAM_CHAT_ID environment variables must be set.']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Only POST method is accepted.']);
    exit;
}

// Get the raw POST data
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Basic validation
if (!isset($data['question']) || !isset($data['options']) || !isset($data['correct_option_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Missing required parameters: question, options, correct_option_id.']);
    exit;
}

// Prepare data for Telegram
$telegram_data = [
    'chat_id' => $chatId,
    'question' => $data['question'],
    'options' => json_encode($data['options']),
    'type' => 'quiz',
    'correct_option_id' => $data['correct_option_id'],
    'explanation' => $data['explanation'] ?? '',
    'is_anonymous' => false
];

// Send to Telegram using cURL
$url = 'https://api.telegram.org/bot' . $botToken . '/sendPoll';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $telegram_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Check for errors and return response
if ($curl_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'cURL Error: ' . $curl_error, 'telegram_response' => $response]);
} elseif ($http_code >= 400) {
    http_response_code($http_code);
    echo json_encode(['success' => false, 'message' => 'Telegram API Error.', 'telegram_response' => json_decode($response)]);
} else {
    echo json_encode(['success' => true, 'message' => 'Poll forwarded to Telegram successfully.', 'telegram_response' => json_decode($response)]);
}
?>