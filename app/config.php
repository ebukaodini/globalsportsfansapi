<?php

// SERVER
define('SERVER', 'https://globalsportsfansapi.initframework.com');

// APPLICATION
define('APP_NAME', 'Global Sports Fans');
define('APP_BASEDIR', \dirname(__DIR__) . '/');
define('APP_KEY', 'f14df4c3d9532ea7705c9dc044ed2cd934cf7e8e5a0a9122d4be6880f41c8fc4');
define('ENV', 'test'); // test, live

// RESOURCES
define('TEMPLATE_DIR', APP_BASEDIR . 'app/views/');
define('ASSETS_PATH', 'app/assets/');
define('STORAGE_PATH', 'storage/public/');
define('STORAGE_DIR', APP_BASEDIR . STORAGE_PATH);

// ERROR LOG
define('LOG_ERROR', true);
define('LOG_FILE', APP_BASEDIR . 'storage/logs/error.log');
define('DISPLAY_ERROR', false);
define('SEND_EMAIL_LOG', false);
define('SEND_EMAIL_LOG_ADDRESS', 'postmaster@localhost');

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
define('DB_USERNAME', 'initfram_globalsports');
define('DB_PASSWORD', 'Ni2E5YoY5kER');
define('DB_DATABASE', 'initfram_globalsports');
define('DB_PORT', '3306');
define('DB_PREFIX', '');
define('DB_ENGINE', 'MyISAM');
define('DB_DEFAULT_CHARSET', 'latin1');
define('DB_COLLATION', 'latin1_general_ci');

// MAIL
define('MAIL_DRIVER', 'smtp'); // mail, smtp, sendmail
define('MAIL_SMTP_HOST', 'globalsportsfans.initframework.com');
define('MAIL_SMTP_PORT', 465);
define('MAIL_SMTP_AUTH', true);
define('MAIL_SMTP_USERNAME', 'info@globalsportsfans.initframework.com');
define('MAIL_SMTP_PASSWORD', 'ND&pZVv.bDQz');
define('MAIL_SMTP_SECURE', 'ssl'); // none, tls, ssl
define('MAIL_SMTP_TIMEOUT', 10);

// TIMEZONE
define("TIMEZONE", "Africa/Lagos");

// CUSTOM
// define your custom configurations...
// define('CONFIG', 'VALUE');

define('ORG_EMAIL', 'info@globalsportsfans.initframework.com');

define('ORG_REFERRAL_CODE', 'SPORTSFANS');
