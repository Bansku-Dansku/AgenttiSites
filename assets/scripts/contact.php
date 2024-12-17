<?php
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haetaan lomakkeen tiedot
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        die('Kaikki kentät ovat pakollisia!');
    }

    $webhookUrl = $_ENV['DISCORD_WEBHOOK_URL'] ?? '';
    if (empty($webhookUrl)) {
        die('Webhook URL puuttuu!');
    }

    $payload = json_encode([
        "embeds" => [
            [
                "title" => "Uusi yhteydenotto",
                "color" => 5814783,
                "fields" => [
                    ["name" => "Nimi", "value" => $name, "inline" => true],
                    ["name" => "Sähköposti", "value" => $email, "inline" => true],
                    ["name" => "Aihe", "value" => $subject, "inline" => false],
                    ["name" => "Viesti", "value" => $message, "inline" => false],
                ],
                "footer" => [
                    "text" => "Agentti.NET | " . date('Y-m-d H:i:s'),
                ],
            ],
        ],
    ]);

    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode === 204) {
        echo 'Viesti lähetetty onnistuneesti!';
    } else {
        echo 'Viestin lähettäminen epäonnistui. Tarkista webhook URL.';
    }
} else {
    echo 'Virheellinen pyyntö.';
}
?>
