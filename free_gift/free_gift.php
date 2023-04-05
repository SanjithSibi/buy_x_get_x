<?php
/*
 * Plugin name:Buy X Get X
 * Plugin Description:Adding gifts to the products
 * version:1.0
 * Author:Sibi
 * Text Domain: free-gift
 */

use Fg\App\Router;

defined('ABSPATH') or exit();
if(!file_exists(WP_PLUGIN_DIR.'/free_gift/vendor/autoload.php')) return;
require_once WP_PLUGIN_DIR.'/free_gift/vendor/autoload.php';

if((!class_exists('Fg\App\Router'))  || (!method_exists(\Fg\App\Router::class,'hooks'))) return;
$router=new Router();
$router->hooks();
