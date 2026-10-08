-- Moneda de cada empresa para mostrar salarios y montos a pagar (colones, dolares o euros).
ALTER TABLE settings
    ADD COLUMN currency ENUM('CRC', 'USD', 'EUR') NOT NULL DEFAULT 'USD' AFTER logo_path;
