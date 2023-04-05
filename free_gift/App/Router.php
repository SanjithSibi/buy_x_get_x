<?php

namespace Fg\App;

use Fg\App\controller\Base;
defined('ABSPATH') or exit();


class Router
{
    public function hooks(){
        $base=new Base();
        add_filter('woocommerce_product_data_tabs', array($base, 'addFreeGiftTab'));
        add_action('woocommerce_product_data_panels', array($base, 'woocommerceProductCustomFields'));
        add_action( 'woocommerce_process_product_meta', array($base,'saveCheckboxValueToDatabase' ));
        add_action( 'woocommerce_check_cart_items', array($base,'checkCartItems'));
        add_action( 'woocommerce_before_calculate_totals', array($base,'customPrice') );


    }
}