<?php
$webhook_url = "https://discord.com/api/webhooks/your-webhook-id/your-webhook-token";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haetaan lomakkeen tiedot
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    $payload = json_encode([
        "embeds" => [
            [
                "title" => "Uusi yhteydenotto",
                "color" => 5814783,
                "fields" => [
                    [
                        "name" => "Nimi",
                        "value" => $name,
                        "inline" => true
                    ],
                    [
                        "name" => "Sähköposti",
                        "value" => $email,
                        "inline" => true
                    ],
                    [
                        "name" => "Aihe",
                        "value" => $subject,
                        "inline" => false
                    ],
                    [
                        "name" => "Viesti",
                        "value" => $message,
                        "inline" => false
                    ]
                ],
                "footer" => [
                    "text" => "Lähetetty Agentti.NET-sivustolta",
                ],
                "timestamp" => date("c")
            ]
        ]
    ]);

    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($http_code === 204) {
        echo "Viesti lähetetty onnistuneesti!";
    } else {
        echo "Virhe viestiä lähetettäessä. Varmista webhook-URL.";
    }
} else {
    echo "Lomaketta ei lähetetty oikein.";
}
?>
