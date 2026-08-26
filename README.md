# Ficha de admissão de pacientes

Formulário web de cadastro de pacientes para uma Psicóloga independente, com
geração de PDF no navegador e envio automático por e-mail. Sistema em uso
real.

<img alt="image" src="https://github.com/user-attachments/assets/e31f1cf8-8fbe-4aff-a295-4f6f9aaa0d5f" />


## Como funciona

1. `index.html` — formulário em etapas (stepper) com a identidade visual da
   clínica. Ao concluir, monta um PDF com os dados no próprio navegador e
   envia para `enviar.php` como JSON (dados + PDF em base64).
2. `enviar.php` — recebe o JSON e chama a API HTTP da [Brevo](https://www.brevo.com/)
   para enviar um e-mail com os dados formatados e o PDF anexado à
   destinatária.

## Configuração

E preencha `enviar.php` com:
- `brevo_api_key` — chave de API da sua conta Brevo
- `remetente_email` — e-mail verificado como remetente na Brevo
- `destinatario_email` — e-mail de quem deve receber a ficha


## Tecnologias

HTML, CSS, JavaScript (geração de PDF no cliente), PHP, API HTTP da Brevo.
