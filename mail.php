<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// GET JSON BODY
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data) {
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$site = $data["site"] ?? "";
$name = htmlspecialchars($data["name"] ?? "");
$email = htmlspecialchars($data["email"] ?? "");
$phone = htmlspecialchars($data["phone"] ?? "");
$company = htmlspecialchars($data["company"] ?? "");
$subject = htmlspecialchars($data["subject"] ?? "");
$message = nl2br(htmlspecialchars($data["message"] ?? ""));

// SELECT THEME & RECIPIENT BASED ON SITE
if ($site === "Shri Bhagwati Polypack") {
    $themeColor = "#05aad3";
    $toEmail = "tirthgajera12345@gmail.com";  // CHANGE THIS
    $title = "Shri Bhagwati Polypack - Contact Form";
} else {
    $themeColor = "#613D08";  // Primary Kalpataru
    $secondary = "#30852C";   // Secondary Kalpataru
    $toEmail = "tirthgajera12345@gmail.com"; // CHANGE THIS
    $title = "Kalpataru Packaging Solution - Contact Form";
}

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "devoraofficial11@gmail.com"; 
    $mail->Password = "jdwk tepl iwdc blxh";  
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom("devoraofficial11@gmail.com", $site);
    $mail->addAddress($toEmail);

    $mail->isHTML(true);
    $mail->Subject = "$title";

    // HTML DESIGN
    $mail->Body = '
    <div style="font-family:Arial;padding:20px;border-left:5px solid '.$themeColor.';">
        <h2 style="color:'.$themeColor.';">'.$title.'</h2>
        
        <table cellpadding="10" cellspacing="0" border="1" style="border-collapse:collapse;width:100%;">
            <tr><td><strong>Name</strong></td><td>'.$name.'</td></tr>
            <tr><td><strong>Email</strong></td><td>'.$email.'</td></tr>
            <tr><td><strong>Phone</strong></td><td>'.$phone.'</td></tr>
            <tr><td><strong>Company</strong></td><td>'.$company.'</td></tr>
            <tr><td><strong>Subject</strong></td><td>'.$subject.'</td></tr>
            <tr><td><strong>Message</strong></td><td>'.$message.'</td></tr>
        </table>

        <p style="margin-top:20px;color:'.$themeColor.';">
            Sent from: <strong>'.$site.'</strong>
        </p>
    </div>';

    $mail->send();

    echo json_encode(['success' => true]);
} 
catch (Exception $e) {
    echo json_encode(['error' => $mail->ErrorInfo]);
}
?>
