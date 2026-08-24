# Ficha de admissão de pacientes

Formulário web de cadastro de pacientes para a Dra. Roseli Domingues, com
geração de PDF no navegador e envio automático por e-mail. Sistema em uso
real (não é um exercício acadêmico).

## Como funciona

1. `index.html` — formulário em etapas (stepper) com a identidade visual da
   clínica. Ao concluir, monta um PDF com os dados no próprio navegador e
   envia para `enviar.php` como JSON (dados + PDF em base64).
2. `enviar.php` — recebe o JSON e chama a API HTTP da [Brevo](https://www.brevo.com/)
   para enviar um e-mail com os dados formatados e o PDF anexado à
   destinatária. Usa a API HTTP (não SMTP) porque a hospedagem (Hostinger)
   bloqueia conexões SMTP de saída.
3. `src/` — biblioteca [PHPMailer](https://github.com/PHPMailer/PHPMailer)
   vendorizada; não é usada no fluxo atual (que fala direto com a API da
   Brevo), mantida aqui como parte do histórico do projeto.

## Configuração

As credenciais **não** ficam no código. Antes de rodar:

```bash
cp config.example.php config.php
```

E preencha `config.php` com:
- `brevo_api_key` — chave de API da sua conta Brevo
- `remetente_email` — e-mail verificado como remetente na Brevo
- `destinatario_email` — e-mail de quem deve receber a ficha

`config.php` está no `.gitignore` — em produção (Hostinger), ele é criado
diretamente no servidor, nunca commitado.

## Tecnologias

HTML, CSS, JavaScript (geração de PDF no cliente), PHP, API HTTP da Brevo.
