<?php
// Copie este arquivo para "config.php" e preencha com os valores reais.
// config.php NÃO é versionado (está no .gitignore) — cada ambiente
// (local ou o servidor de produção) mantém o seu próprio, com a chave real.

return [
    // Chave de API da conta Brevo (https://app.brevo.com) usada para enviar o e-mail.
    'brevo_api_key'      => 'SUA_CHAVE_AQUI',

    // E-mail verificado como remetente na sua conta Brevo.
    'remetente_email'    => 'seu-email-verificado@exemplo.com',

    // E-mail de quem deve receber a ficha preenchida.
    'destinatario_email' => 'destinatario@exemplo.com',
];
