<?php

header("Content-Type: application/json");

$configPath = __DIR__ . '/config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    echo json_encode([
        "status" => "erro",
        "erro"   => "config.php não encontrado. Copie config.example.php para config.php e preencha os valores.",
    ]);
    exit;
}

$config = require $configPath;

$entrada = json_decode(file_get_contents("php://input"), true);

if (!$entrada || empty($entrada["dados"]) || empty($entrada["pdf"])) {
    echo json_encode(["status" => "erro", "erro" => "Dados inválidos ou ausentes."]);
    exit;
}

$dados     = $entrada["dados"];
$pdfBase64 = $entrada["pdf"]; // já vem como base64 puro do JS

// =====================================================
//  CONFIGURAÇÃO — vem de config.php (não versionado)
// =====================================================
$brevo_api_key      = $config['brevo_api_key'];
$remetente_email    = $config['remetente_email']; // e-mail que você verificou no Brevo
$destinatario_email = $config['destinatario_email'];
// =====================================================

$nome_paciente = $dados["nome"] ?? "Paciente";

$html_body = "
<div style='font-family:Arial,sans-serif;max-width:540px;color:#3D2427;'>
    <h2 style='color:#5C3547;border-bottom:2px solid #F5E8EC;padding-bottom:8px;'>
        Nova ficha recebida 🌸
    </h2>
    <table style='width:100%;border-collapse:collapse;'>
        <tr><td style='padding:8px 0;border-bottom:1px solid #F5EDEF;width:40%;color:#C9899A;font-size:12px;text-transform:uppercase;font-weight:bold;'>Paciente</td>
            <td style='padding:8px 0;border-bottom:1px solid #F5EDEF;'>" . htmlspecialchars($dados["nome"] ?? '') . "</td></tr>
        <tr><td style='padding:8px 0;border-bottom:1px solid #F5EDEF;color:#C9899A;font-size:12px;text-transform:uppercase;font-weight:bold;'>Nascimento</td>
            <td style='padding:8px 0;border-bottom:1px solid #F5EDEF;'>" . htmlspecialchars($dados["nascimento"] ?? '') . "</td></tr>
        <tr><td style='padding:8px 0;border-bottom:1px solid #F5EDEF;color:#C9899A;font-size:12px;text-transform:uppercase;font-weight:bold;'>Nome da mãe</td>
            <td style='padding:8px 0;border-bottom:1px solid #F5EDEF;'>" . htmlspecialchars($dados["mae"] ?? '') . "</td></tr>
        <tr><td style='padding:8px 0;border-bottom:1px solid #F5EDEF;color:#C9899A;font-size:12px;text-transform:uppercase;font-weight:bold;'>Nome do pai</td>
            <td style='padding:8px 0;border-bottom:1px solid #F5EDEF;'>" . htmlspecialchars($dados["pai"] ?? '') . "</td></tr>
        <tr><td style='padding:8px 0;border-bottom:1px solid #F5EDEF;color:#C9899A;font-size:12px;text-transform:uppercase;font-weight:bold;'>Endereço</td>
            <td style='padding:8px 0;border-bottom:1px solid #F5EDEF;'>" . htmlspecialchars($dados["endereco"] ?? '') . "</td></tr>
        <tr><td style='padding:8px 0;color:#C9899A;font-size:12px;text-transform:uppercase;font-weight:bold;vertical-align:top;padding-top:12px;'>Queixa principal</td>
            <td style='padding:8px 0;padding-top:12px;'>" . nl2br(htmlspecialchars($dados["queixa"] ?? '')) . "</td></tr>
    </table>
    <p style='font-size:12px;color:#A08888;margin-top:24px;border-top:1px solid #EAD8DF;padding-top:12px;'>
        Preenchido em: " . htmlspecialchars($dados["data_envio"] ?? '') . "<br>
        A ficha em PDF está anexada a este e-mail.
    </p>
</div>
";

$payload = [
    "sender" => [
        "name"  => "Cadastro de Pacientes — Roseli Domingues",
        "email" => $remetente_email
    ],
    "to" => [
        ["email" => $destinatario_email]
    ],
    "subject"     => "Nova ficha — " . $nome_paciente,
    "htmlContent" => $html_body,
    "attachment"  => [
        [
            "content" => $pdfBase64,  // base64 puro, direto do JS
            "name"    => "Ficha_" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $nome_paciente) . ".pdf"
        ]
    ]
];

// Chama a API do Brevo via HTTP (sem SMTP — não é bloqueado pelo Hostinger)
$ch = curl_init("https://api.brevo.com/v3/smtp/email");

curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        "Content-Type: application/json",
        "Accept: application/json",
        "api-key: " . $brevo_api_key
    ]
]);

$resposta  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 201) {
    echo json_encode(["status" => "ok"]);
} else {
    $detalhe = json_decode($resposta, true);
    echo json_encode([
        "status" => "erro",
        "erro"   => $detalhe["message"] ?? "Erro ao enviar (HTTP $http_code)"
    ]);
}
