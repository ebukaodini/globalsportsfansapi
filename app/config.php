<?php

// SERVER
define('SERVER', 'http://localhost:8080');

// APPLICATION
define('APP_NAME', 'Global Sports Fans');
define('APP_BASEDIR', \dirname(__DIR__) . '/');
define('APP_KEY', 'f14df4c3d9532ea7705c9dc044ed2cd934cf7e8e5a0a9122d4be6880f41c8fc4');
define('ENV', 'dev'); // test, live

// RESOURCES
define('TEMPLATE_DIR', APP_BASEDIR . 'app/views/');
define('ASSETS_PATH', 'app/assets/');
define('STORAGE_PATH', 'storage/public/');
define('STORAGE_DIR', APP_BASEDIR . STORAGE_PATH);

// ERROR LOG
define('LOG_FILE', APP_BASEDIR . 'storage/logs/error.log');
define('LOG_EMAIL', false);
define('LOG_ADDRESS', 'postmaster@localhost');

// AUTHENTICATION
define('AUTH_LIFETIME', 60); // Minutes

// SESSION
define('SESSION_DIR', APP_BASEDIR . 'storage/session/');
define('SESSION_NAME', 'ISESSID');
define('SESSION_EXPIRE', 'AUTH_LIFETIME');

// CACHE
// define('CACHE_REQUEST', true);
// define('CACHE_EXPIRE', 3600);
// define('CACHE_DIR', APP_BASEDIR . 'storage/cache/');

// DB
define('DB_DRIVER', 'mysql');
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'globalsports');
define('DB_PORT', '3306');
define('DB_PREFIX', '');
define('DB_ENGINE', 'MyISAM');
define('DB_DEFAULT_CHARSET', 'latin1');
define('DB_COLLATION', 'utf8mb4_general_ci');

// MAIL
define('MAIL_DRIVER', 'smtp'); // mail, smtp, sendmail
define('MAIL_SMTP_HOST', '');
define('MAIL_SMTP_PORT', 25);
define('MAIL_SMTP_AUTH', false);
define('MAIL_SMTP_USERNAME', '');
define('MAIL_SMTP_PASSWORD', '');
define('MAIL_SMTP_SECURE', 'none'); // none, tls, ssl
define('MAIL_SMTP_TIMEOUT', 10);

// TIMEZONE
define("TIMEZONE", "UTC");

// CUSTOM
// define your custom configurations...
// define('CONFIG', 'VALUE');