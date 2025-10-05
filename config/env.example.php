<?php
// Copy this file to env.php and update with your actual configuration

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'apotek_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'Apotek Sehat');
define('APP_URL', 'http://localhost/apotek-web');
define('APP_TIMEZONE', 'Asia/Jakarta');

// Payment Configuration
define('QRIS_MERCHANT_ID', 'your_merchant_id');
define('QRIS_SECRET_KEY', 'your_secret_key');
define('PAYMENT_TIMEOUT', 3600); // 1 hour in seconds

// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email@gmail.com');
define('SMTP_PASS', 'your_app_password');
define('EMAIL_FROM', 'noreply@apoteksehat.com');

// File Upload Configuration
define('UPLOAD_MAX_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx']);

// Security Configuration
define('ENCRYPTION_KEY', 'your_encryption_key_here');
define('SESSION_TIMEOUT', 1800); // 30 minutes

// Development Mode
define('DEBUG_MODE', true);
?>