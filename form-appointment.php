<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Invalid request method.';
    exit;
}

function appointment_field(string $key): string
{
    return trim((string) filter_input(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

$firstName = appointment_field('fname');
$lastName = appointment_field('lname');
$phone = appointment_field('phone');
$service = appointment_field('services');
$date = appointment_field('date');
$email = trim((string) filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

if ($firstName === '' || $lastName === '' || $phone === '' || $email === '' || $service === '' || $date === '') {
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

$appointmentDate = DateTime::createFromFormat('Y-m-d', $date);
if (!$appointmentDate) {
    http_response_code(400);
    echo 'Please choose a valid appointment date.';
    exit;
}

$to = getenv('APPOINTMENT_FORM_TO') ?: 'ranjith.g554@gmail.com';
$subject = 'New appointment request - AB Neuro Centre';
$body = implode("\n", [
    'New appointment request',
    '',
    'Name: ' . $firstName . ' ' . $lastName,
    'Phone: ' . $phone,
    'Email: ' . $email,
    'Service: ' . $service,
    'Preferred date: ' . $appointmentDate->format('d M Y'),
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
echo 'Appointment request could not be sent. Please call +91 9848999917.';
