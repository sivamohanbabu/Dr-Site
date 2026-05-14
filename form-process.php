<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Invalid request method.';
    exit;
}

function field(string $key): string
{
    return trim((string) filter_input(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

$firstName = field('fname');
$lastName = field('lname');
$phone = field('phone');
$message = field('message');
$email = trim((string) filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

if ($firstName === '' || $lastName === '' || $phone === '' || $email === '') {
    http_response_code(400);
    echo 'Please complete all required fields.';
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Please enter a valid email address.';
    exit;
}

if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
    http_response_code(400);
    echo 'Please enter a valid phone number.';
    exit;
}

$to = getenv('CONTACT_FORM_TO') ?: 'ranjith.g554@gmail.com';
$subject = 'New website enquiry - AB Neuro Centre';
$body = implode("\n", [
    'New contact form enquiry',
    '',
    'Name: ' . $firstName . ' ' . $lastName,
    'Phone: ' . $phone,
    'Email: ' . $email,
    'Message: ' . ($message !== '' ? $message : 'Not provided'),
    '',
    'Source: ' . ($_SERVER['HTTP_HOST'] ?? 'drranjithneuro.com'),
]);

$headers = [
    'From: AB Neuro Centre <noreply@drranjithneuro.com>',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
];

if (mail($to, $subject, $body, implode("\r\n", $headers))) {
    echo 'success';
    exit;
}

http_response_code(500);
echo 'Message could not be sent. Please call +91 9848999917.';
