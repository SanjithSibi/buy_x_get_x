<?php
/**
 * Plugin name:Buy X Get X
 * Plugin Description:Adding gifts to the products
 * version:1.0
 * Author:Sibi
 * Text Domain: free-gift
 */


defined('ABSPATH') or exit();
if(!file_exists(WP_PLUGIN_DIR.'/free_gift/vendor/autoload.php')) return;
require_once WP_PLUGIN_DIR.'/free_gift/vendor/autoload.php';
defined("BOGO_PATH") or define("BOGO_PATH",plugin_dir_url(__FILE__));

if((!class_exists('Fg\App\Router'))  || (!method_exists(\Fg\App\Router::class,'hooks'))) return;
$router=new Fg\App\Router();
$router->hooks();
