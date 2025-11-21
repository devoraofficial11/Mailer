<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

$site = $data["site"] ?? "";
$name = $data["name"] ?? "";
$email = $data["email"] ?? "";
$phone = $data["phone"] ?? "";
$company = $data["company"] ?? "";
$subject = $data["subject"] ?? "";
$message = nl2br($data["message"] ?? "");

/* SELECT RECIPIENT + THEME */
if ($site === "Shri Bhagwati Polypack") {
    $to = "tirthgajera12345@gmail.com";
    $color = "#05aad3";
    $title = "Shri Bhagwati Polypack";
} else {
    $to = "tirthgajera12345@gmail.com";
    $color = "#613D08";
    $title = "Kalpataru Packaging Solution";
}

/* EMAIL BODY */
$body = "
<div style='font-family: Arial; padding:20px; border-left:4px solid {$color};'>
    <h2 style='color:{$color};'>{$title} - Contact Form</h2>
    <p><strong>Name:</strong> {$name}</p>
    <p><strong>Email:</strong> {$email}</p>
    <p><strong>Phone:</strong> {$phone}</p>
    <p><strong>Company:</strong> {$company}</p>
    <p><strong>Subject:</strong> {$subject}</p>
    <p><strong>Message:</strong><br>{$message}</p>
</div>
";

/* SMTP SEND (NO PHPMailer) */
$from = "devoraofficial11@gmail.com";
$appPass = "jdwk tepl iwdc blxh";

$smtpHost = "smtp.gmail.com";
$smtpPort = 587;

/* SMTP CONNECTION */
$socket = fsockopen($smtpHost, $smtpPort, $errno, $errstr, 10);
if (!$socket) {
    echo json_encode(["error" => "Failed to connect to SMTP server"]);
    exit;
}
if (!$socket) {
    echo json_encode(["error" => "SMTP connection failed"]);
    exit;
}

function sendLine($socket, $cmd) {
    fputs($socket, $cmd . "\r\n");
}

function getResp($socket) {
    return fgets($socket, 512);
}

/* SMTP HANDSHAKE */
getResp($socket);
sendLine($socket, "EHLO localhost");
getResp($socket);

/* STARTTLS */
sendLine($socket, "STARTTLS");
getResp($socket);

stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

/* EHLO AGAIN */
sendLine($socket, "EHLO localhost");
getResp($socket);

/* LOGIN */
sendLine($socket, "AUTH LOGIN");
getResp($socket);

sendLine($socket, base64_encode($from));
getResp($socket);

sendLine($socket, base64_encode($appPass));
getResp($socket);

/* MAIL SEND */
sendLine($socket, "MAIL FROM:<$from>");
getResp($socket);

sendLine($socket, "RCPT TO:<$to>");
getResp($socket);

sendLine($socket, "DATA");
getResp($socket);

$headers =
"From: Website <{$from}>\r\n".
"MIME-Version: 1.0\r\n".
"Content-Type: text/html; charset=UTF-8\r\n".
"Subject: {$title} - Contact Form\r\n\r\n";

sendLine($socket, $headers . $body . "\r\n.");
getResp($socket);

/* QUIT */
sendLine($socket, "QUIT");
fclose($socket);

echo json_encode(["success" => true]);
?>
