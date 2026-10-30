<?php

return [
    'mailer' => getenv('MAIL_MAILER') ?: 'log',
    'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
    'port' => (int) (getenv('MAIL_PORT') ?: 587),
    'username' => getenv('MAIL_USERNAME') ?: '',
    'password' => getenv('MAIL_PASSWORD') ?: '',
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
    'from_email' => getenv('MAIL_FROM_EMAIL') ?: 'no-reply@lobibus.local',
    'from_name' => getenv('MAIL_FROM_NAME') ?: 'LobiBus',
    'log_path' => getenv('MAIL_LOG_PATH') ?: dirname(__DIR__) . '/public/uploads/mail.log',
];
