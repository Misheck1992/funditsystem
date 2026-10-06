<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* Defaults can be overridden per installation in mithenga.local.php. */
$config['mithenga_base_url'] = 'https://mithenga.com/api/v1';
$config['mithenga_account_id'] = 13;
$config['mithenga_api_key'] = getenv('MITHENGA_API_KEY') ?: '';
$config['mithenga_default_country_code'] = '260';
$config['mithenga_timeout'] = 15;
$config['fundit_public_url'] = rtrim(getenv('FUNDIT_PUBLIC_URL') ?: '', '/');

$local_config = __DIR__ . DIRECTORY_SEPARATOR . 'mithenga.local.php';
if (is_file($local_config)) {
    require $local_config;
}
$config['fundit_public_url'] = rtrim($config['fundit_public_url'], '/');
