$DbName = 'wordpress'
$DbUser = 'root'
$DbPassword = ''
$DbHost = '127.0.0.1'

$ConfigPath = 'wordpress\\wordpress\\wp-config.php'

$Config = @'
<?php

define('DB_NAME', 'wordpress');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', '127.0.0.1');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

define('WP_DEBUG', true);

 = 'wp_';

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once ABSPATH . 'wp-settings.php';
'@

Set-Content -Path $ConfigPath -Value $Config -Encoding UTF8
Write-Host 'wp-config.php written to ' $ConfigPath
