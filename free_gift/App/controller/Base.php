<?php

namespace Fg\App\controller;
defined('ABSPATH') or exit();
class Base
{
    public function
    addFreeGiftTab($tabs){

        if(! is_array($tabs)) {
            return $tabs;
        }
        $tabs['fg-free-gift'] = array(
            'label' => __('Free Gifts', 'fg-free-gift'),
            'priority' => 50,
            'target' => 'fg_free_gift',
        );
        return $tabs;
    }
    function woocommerceProductCustomFields()
    {
        global  $post;
        if(! isset($post->ID) || (! is_numeric($post->ID))){ return ; }
        $product_id = $post->ID;
        $checkbox_value = get_post_meta( $product_id, '_fg_checkbox_meta', true );
        $value = (!empty($checkbox_value) && $checkbox_value == 'on') ? 'checked="checked"' : '';
        wc_get_template( 'admin_product.php', array('Value' => $value), '',WP_PLUGIN_DIR . '/free_gift/App/view/' );
    }
    function saveCheckboxValueToDatabase( $post_id ) {
        $checkbox_value = isset($_POST['my_checkbox']) ?  sanitize_text_field($_POST['my_checkbox']):'off';
        if(!empty($checkbox_value) ) {
            update_post_meta($post_id, '_fg_checkbox_meta', $checkbox_value);
        }
    }
    function checkCartItems()
    {

        if(!function_exists('WC') || ! is_object(WC()->cart) || ! method_exists(WC()->cart,'get_cart')){return;}
            $cart = WC()->cart->get_cart();

        if(! isset($cart) || ! is_array($cart)){return;}
        $parent_key = array();
        $gift_id = array();
        $gift_key=array();
        foreach ($cart as $cart_item_key => $cart_item) {
            if(!is_array($cart_item)){continue;}
            if(!isset($cart_item['product_id'])){continue;}
            $product_id = $cart_item['product_id'];
            $checkbox_value = get_post_meta($product_id, '_fg_checkbox_meta', true);
            if ( $checkbox_value === false || $checkbox_value === '' ) {continue;}
            if ((!isset($cart_item['variation'])|| !isset($cart_item['variation']['bogo'])) && $checkbox_value == 'on'
            ) {
                if(isset($parent_key) && is_array($parent_key)) {
                    $parent_key[] = $cart_item_key;
                }
            }
            if (isset($cart_item['variation']) && isset($cart_item['variation']['bogo'])) {
                if(isset($gift_key) && is_array($gift_key)){
                 $gift_key[]=$cart_item_key;}
                if(isset($gift_id) && is_array($gift_id)){
                $gift_id[] = $cart_item['variation']['bogo'];}
            }
        }
        if (!empty($parent_key) && is_array($parent_key)) {
            foreach ($parent_key as $key) {
                if (! method_exists( WC()->cart, 'get_cart_item' ) ) {return;}
                $cart_item = WC()->cart->get_cart_item($key);
                if(!isset($cart_item['product_id']) || !isset($cart_item['quantity'])){continue;}
                $product_id = $cart_item['product_id'];
                $parent_quantity = $cart_item['quantity'];
                if (empty($gift_id) || !in_array($key, $gift_id)) {
                    $quantity = $parent_quantity;
                    if (! method_exists( WC()->cart, 'add_to_cart' ) ) {return;}
                    WC()->cart->add_to_cart($product_id, $quantity, array(), array('bogo' => $key));
                }
            }
            }
        if(!empty($gift_key) && is_array($gift_key)) {
            foreach ($gift_key as $gift_product_key) {
                if (! method_exists( WC()->cart, 'get_cart_item' ) ) {return;}
                $cart_item =  WC()->cart->get_cart_item($gift_product_key);
                if(!isset($cart_item['variation']) || !isset($cart_item['variation']['bogo'])){continue;}
                $parent_key = $cart_item['variation']['bogo'];
                if (!WC()->cart->find_product_in_cart($parent_key)) {
                    WC()->cart->remove_cart_item($gift_product_key);
                }
            }
        }
        }
        function customPrice($cart_object)
        {
            if (!isset($cart_object) || !is_object($cart_object)) {
                return;
            }
            if(! is_object($cart_object) || ! method_exists(WC()->cart,'get_cart')){return;}
            foreach ($cart_object->get_cart() as $key => $value) {
                if(!is_array($value)){continue;}
                if ( !isset($value['variation']) ||! isset($value['variation']['bogo'])) {continue;}
                    $price = 0;
                if (!isset($value['data'])|| !is_object($value['data']) || ! method_exists( $value['data'], 'set_price' ) ) {continue;}
                    $value['data']->set_price($price);
                    $parent_key = $value['variation']['bogo'];
                if (!function_exists('WC') || !is_object(WC()->cart) || ! method_exists( WC()->cart, 'get_cart_item' )) return;
                $cart_item = WC()->cart->get_cart_item($key);;
                if(! isset($cart_item['quantity'])){continue;}
                    $gift_quantity = $cart_item['quantity'];
                    $cart_item = WC()->cart->get_cart_item($parent_key);
                    $parent_quantity = $cart_item['quantity'];
                    $quantity = $parent_quantity;
                    if ($gift_quantity != $parent_quantity && method_exists( WC()->cart, 'set_quantity' )) {
                        WC()->cart->set_quantity($key, $quantity);
                }
            }
        }
}




