<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Time Tracking',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost:8080',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'UTC',

    // Umbral diario a partir del cual las horas se consideran extra (informativo,
    // el pago siempre se calcula como Horas Trabajadas x Salario por Hora).
    'overtime_daily_threshold' => 8,
];
