<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

global $config;

$config['server_local']         = array(''); //local server host name
$config['server_staging']       = array(''); // staging server host name
$config['server_production']    = array(''); // production server host name

function getCurrentServer() {

    global $config;

    $host = $_SERVER['HTTP_HOST'];

    if (in_array($host, $config['server_production'])) {
        return 'production';
    } else if (in_array($host, $config['server_staging'])) {
        return 'staging';
    } else {
        return 'default';
    }
}
