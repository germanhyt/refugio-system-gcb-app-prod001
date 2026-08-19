<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prueba de correo</title>
</head>
<body style="margin:0;padding:0;background:#f7f3ee;font-family:Arial,Helvetica,sans-serif;color:#2b241f;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f7f3ee;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:#1e4d33;padding:22px 28px;color:#fff;font-size:18px;letter-spacing:.04em;text-transform:uppercase;">
                            Refugio Gastronómico
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 12px;font-size:20px;color:#a7623d;">Correo de prueba</p>
                            <p style="margin:0 0 16px;line-height:1.5;">
                                Este envío se generó desde <strong>Configuraciones del sitio</strong> para validar el correo emisor de marketing.
                            </p>
                            <p style="margin:0 0 8px;line-height:1.5;">
                                Destinatario: <strong>{{ $recipient }}</strong>
                            </p>
                            <p style="margin:0;line-height:1.5;color:#776f69;font-size:14px;">
                                Fecha: {{ now()->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
