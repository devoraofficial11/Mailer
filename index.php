<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Allow only POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

// Read JSON from fetch()
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Invalid JSON"]);
    exit;
}

/* FORM FIELDS */
$site    = $data["site"] ?? "";
$name    = $data["name"] ?? "";
$email   = $data["email"] ?? "";
$phone   = $data["phone"] ?? "";
$company = $data["company"] ?? "";
$subject = $data["subject"] ?? "";
$message = nl2br($data["message"] ?? "");

/* SITE-BASED ROUTING */
if ($site === "Shri Bhagwati Polypack") {
    $to = "tirthgajera12345@gmail.com";
    $color = "#05aad3";
    $title = "Shri Bhagwati Polypack";
} else {
    $to = "tirthgajera12345@gmail.com";
    $color = "#613D08";
    $title = "Kalpataru Packaging Solution";
}

/* EMAIL HTML BODY */
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

/* RESEND API KEY */
$RESEND_API_KEY = "re_CBFFwbXr_4oLaFvXyzG6S2iF8UqWGzvtQ";

/* API REQUEST PAYLOAD */
$payload = [
    "from" => "Website Forms <onboarding@resend.dev>",
    "to" => [$to],
    "subject" => $title . " - Contact Form",
    "html" => $body
];

/* CURL REQUEST TO RESEND */
$ch = curl_init("https://api.resend.com/emails");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $RESEND_API_KEY,
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

/* RETURN RESPONSE */
if ($httpCode == 200 || $httpCode == 202) {
    echo json_encode(["success" => true, "msg" => "Mail Sent"]);
} else {
    echo json_encode(["error" => "Mail API Failed", "response" => $response]);
}

?>
