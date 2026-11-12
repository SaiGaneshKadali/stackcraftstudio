<?php
declare(strict_types=1);

/*
 * Contact form endpoint for PHP hosting.
 * Update $CONTACT_EMAIL, $ALLOWED_ORIGINS, and $SMTP_CONFIG before deploying to a PHP server.
 */

$CONTACT_EMAIL = 'hello@stackcraftstudio.com';
$SITE_NAME = 'StackCraft Studio';
$MAIL_SUBJECT = 'New Project Inquiry - StackCraft Studio';
$ALLOWED_ORIGINS = [
    'https://stackcraftstudio.com',
    'https://www.stackcraftstudio.com',
];
$SMTP_CONFIG = [
    'host' => 'smtp.stackcraftstudio.com',
    'port' => 587,
    'user' => 'noreply@stackcraftstudio.com',
    'pass' => '',
    'from' => 'noreply@stackcraftstudio.com',
];

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    handleCors($ALLOWED_ORIGINS);
    http_response_code(204);
    exit;
}

handleCors($ALLOWED_ORIGINS);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('Only POST requests are allowed.', 405);
}

$website = trim((string)($_POST['website'] ?? ''));
if ($website !== '') {
    respondOk();
}

$name = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$service = trim((string)($_POST['service'] ?? ''));
$budget = trim((string)($_POST['budget'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    respondError('Please complete all required fields.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respondError('Please provide a valid email address.');
}

$clientIp = getClientIp();
if (!checkRateLimit($clientIp, 5, 3600)) {
    respondError('Rate limit reached. Please try again later.', 429);
}

$emailPayload = buildEmailPayload($SITE_NAME, $MAIL_SUBJECT, $name, $email, $service, $budget, $phone, $message, $clientIp);

$sent = sendViaSmtp($SMTP_CONFIG, $CONTACT_EMAIL, $emailPayload);

if (!$sent) {
    respondError('Unable to send message right now. Please try again later.', 500);
}

respondOk();

function handleCors(array $allowedOrigins): void
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if ($origin === '') {
        return;
    }

    if (!in_array($origin, $allowedOrigins, true)) {
        respondError('Origin not allowed.', 403);
    }

    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Vary: Origin');
}

function checkRateLimit(string $clientIp, int $maxRequests, int $windowSeconds): bool
{
    $tmpDirectory = is_dir('/tmp') ? '/tmp' : sys_get_temp_dir();
    $safeIp = preg_replace('/[^a-zA-Z0-9]/', '_', $clientIp) ?: 'unknown';
    $lockFile = $tmpDirectory . '/form_ratelimit_' . $safeIp . '.lock';
    $now = time();
    $timestamps = [];

    if (is_file($lockFile)) {
        $raw = file_get_contents($lockFile);
        $decoded = json_decode((string)$raw, true);

        if (is_array($decoded)) {
            $timestamps = array_values(array_filter($decoded, static function ($timestamp) use ($now, $windowSeconds) {
                return is_int($timestamp) && ($timestamp + $windowSeconds) > $now;
            }));
        }
    }

    if (count($timestamps) >= $maxRequests) {
        return false;
    }

    $timestamps[] = $now;
    file_put_contents($lockFile, json_encode($timestamps, JSON_UNESCAPED_SLASHES), LOCK_EX);

    return true;
}

function buildEmailPayload(
    string $siteName,
    string $subject,
    string $name,
    string $email,
    string $service,
    string $budget,
    string $phone,
    string $message,
    string $clientIp
): array {
    $lines = [
        'New inquiry from StackCraft Studio contact form.',
        '',
        'Name: ' . $name,
        'Email: ' . $email,
        'Service Required: ' . ($service !== '' ? $service : 'Not specified'),
        'Budget: ' . ($budget !== '' ? $budget : 'Not specified'),
        'Phone: ' . ($phone !== '' ? $phone : 'Not provided'),
        'IP: ' . $clientIp,
        'Date: ' . gmdate('Y-m-d H:i:s') . ' UTC',
        '',
        'Project Description:',
        $message,
    ];

    $bodyText = implode("\n", $lines);
    $bodyHtml = nl2br(htmlspecialchars($bodyText, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    $replyToName = sanitizeHeaderValue($name);
    $replyToEmail = sanitizeHeaderValue($email);

    $mimeBoundary = 'boundary_' . bin2hex(random_bytes(12));
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $mimeBoundary . '"',
        'Reply-To: "' . $replyToName . '" <' . $replyToEmail . '>',
        'X-Mailer: PHP/' . PHP_VERSION,
    ];

    $mimeBody = '--' . $mimeBoundary . "\r\n";
    $mimeBody .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $mimeBody .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $mimeBody .= $bodyText . "\r\n\r\n";
    $mimeBody .= '--' . $mimeBoundary . "\r\n";
    $mimeBody .= "Content-Type: text/html; charset=UTF-8\r\n";
    $mimeBody .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $mimeBody .= '<strong>' . htmlspecialchars($siteName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</strong><br><br>' . $bodyHtml . "\r\n\r\n";
    $mimeBody .= '--' . $mimeBoundary . "--\r\n";

    return [
        'subject' => $subject,
        'bodyText' => $bodyText,
        'bodyMime' => $mimeBody,
        'headers' => $headers,
    ];
}

function sendViaSmtp(array $smtpConfig, string $recipient, array $payload): bool
{
    $host = (string)($smtpConfig['host'] ?? '');
    $port = (int)($smtpConfig['port'] ?? 587);
    $username = (string)($smtpConfig['user'] ?? '');
    $password = (string)($smtpConfig['pass'] ?? '');
    $from = (string)($smtpConfig['from'] ?? $username);

    if ($host === '' || $username === '' || $password === '' || $from === '') {
        return false;
    }

    $socket = @stream_socket_client(
        'tcp://' . $host . ':' . $port,
        $errorNumber,
        $errorMessage,
        15,
        STREAM_CLIENT_CONNECT
    );

    if (!$socket) {
        return false;
    }

    stream_set_timeout($socket, 15);

    try {
        expectSmtpCode($socket, [220]);
        smtpCommand($socket, 'EHLO localhost', [250]);
        smtpCommand($socket, 'STARTTLS', [220]);

        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException('Unable to enable TLS.');
        }

        smtpCommand($socket, 'EHLO localhost', [250]);
        smtpCommand($socket, 'AUTH LOGIN', [334]);
        smtpCommand($socket, base64_encode($username), [334]);
        smtpCommand($socket, base64_encode($password), [235]);
        smtpCommand($socket, 'MAIL FROM:<' . sanitizeHeaderValue($from) . '>', [250]);
        smtpCommand($socket, 'RCPT TO:<' . sanitizeHeaderValue($recipient) . '>', [250, 251]);
        smtpCommand($socket, 'DATA', [354]);

        $dataLines = [
            'Subject: ' . encodeHeader($payload['subject']),
            'From: <' . sanitizeHeaderValue($from) . '>',
        ];

        foreach ($payload['headers'] as $header) {
            $dataLines[] = $header;
        }

        $dataLines[] = '';
        $dataLines[] = normalizeSmtpData($payload['bodyMime']);

        fwrite($socket, implode("\r\n", $dataLines) . "\r\n.\r\n");
        expectSmtpCode($socket, [250]);
        smtpCommand($socket, 'QUIT', [221]);
    } catch (Throwable $exception) {
        fclose($socket);
        return false;
    }

    fclose($socket);
    return true;
}

function smtpCommand($socket, string $command, array $expectedCodes): string
{
    fwrite($socket, $command . "\r\n");
    return expectSmtpCode($socket, $expectedCodes);
}

function expectSmtpCode($socket, array $expectedCodes): string
{
    $response = '';

    while (!feof($socket)) {
        $line = fgets($socket, 515);
        if ($line === false) {
            break;
        }

        $response .= $line;

        if (preg_match('/^\d{3} /', $line) === 1) {
            break;
        }
    }

    $code = (int)substr($response, 0, 3);
    if (!in_array($code, $expectedCodes, true)) {
        throw new RuntimeException('Unexpected SMTP response: ' . trim($response));
    }

    return $response;
}

function normalizeSmtpData(string $content): string
{
    $content = str_replace(["\r\n", "\r"], "\n", $content);
    $content = preg_replace('/^\./m', '..', $content) ?? $content;
    return str_replace("\n", "\r\n", $content);
}

function encodeHeader(string $value): string
{
    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function sanitizeHeaderValue(string $value): string
{
    return trim(str_replace(["\r", "\n"], '', $value));
}

function getClientIp(): string
{
    $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($forwarded !== '') {
        $parts = array_map('trim', explode(',', $forwarded));
        if (!empty($parts[0])) {
            return $parts[0];
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function respondOk(): void
{
    echo json_encode(['status' => 'ok'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function respondError(string $message, int $statusCode = 400): void
{
    http_response_code($statusCode);
    echo json_encode(
        ['status' => 'error', 'message' => $message],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}
