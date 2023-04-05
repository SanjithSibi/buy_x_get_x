<?php

namespace Fg\App\controller;
defined('ABSPATH') or exit();


class Base
{
    public function addFreeGiftTab($tabs){
        if(! is_array($tabs)) {
            return $tabs;
        }
        $tabs['free-gift'] = array(
            'label' => __('Free Gifts', 'free-gift'),
            'priority' => 50,
            'target' => 'Free_gift',
        );
        return $tabs;
    }
    function woocommerceProductCustomFields()
    {
        global  $post;
        if(! isset($post->ID) || (! is_numeric($post->ID))){ return ; }
        $product_id = $post->ID;
        $checkbox_value = get_post_meta( $product_id, '_checkbox_meta', true );
        $value = (isset($checkbox_value) && $checkbox_value == 'on') ? 'checked="checked"' : '';
        ?>
        <div id="Free_gift" class="panel woocommerce_options_panel hidden">
            <input type="checkbox" id="my_checkbox" name="my_checkbox" value="on" <?php echo $value; ?>>
        </div>

        <?php

    }
    function saveCheckboxValueToDatabase( $post_id ) {
        $checkbox_value = isset($_POST['my_checkbox']) ? sanitize_text_field($_POST['my_checkbox']):'';
        if(!empty($checkbox_value) && $checkbox_value=='on') {
            update_post_meta($post_id, '_checkbox_meta', $checkbox_value);
        }
    }
    function checkCartItems()
    {
        if (!function_exists('WC')) return;
        $cart = WC()->cart;
        $parent_key = array();
        $gift_id = array();
        $gift_key=array();
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['product_id'];
            $checkbox_value = get_post_meta($product_id, '_checkbox_meta', true);
            if (!isset($cart_item['variation']['bogo']) && $checkbox_value == 'on') {
                $parent_key[] = $cart_item_key;
            }
            if (isset($cart_item['variation']['bogo'])) {
                 $gift_key[]=$cart_item_key;
                $gift_id[] = $cart_item['variation']['bogo'];
            }
        }
        if (!empty($parent_key)) {
            foreach ($parent_key as $key) {
                $cart_item = $cart->get_cart_item($key);
                $product_id = $cart_item['product_id'];
                $quant = $cart_item['quantity'];
                if (empty($gift_id) || !in_array($key, $gift_id)) {
                    $quantity = $quant;
                    WC()->cart->add_to_cart($product_id, $quantity, array(), array('bogo' => $key));
                }
                                }
            }
        foreach ($gift_key as $gift_product_key){
            $cart_item = $cart->get_cart_item($gift_product_key);
            $parent_key=$cart_item['variation']['bogo'];
            if (!WC()->cart->find_product_in_cart($parent_key)) {
                WC()->cart->remove_cart_item($gift_product_key);
            }
        }

        }
        function customPrice($cart_object)
        {
            if (!isset($cart_object) || !is_object($cart_object)) {
                return;
            }
            foreach ($cart_object->get_cart() as $key => $value) {
                if (isset($value['variation']['bogo'])) {
                    $price = 0;
                    $value['data']->set_price($price);
                    $parent_key = $value['variation']['bogo'];
                    $cart = WC()->cart;
                    $cart_item = $cart->get_cart_item($key);
                    $gift_quantity = $cart_item['quantity'];
                    $cart_item = $cart->get_cart_item($parent_key);
                    $parent_quantity = $cart_item['quantity'];
                    $quantity = $parent_quantity;
                    if ($gift_quantity != $parent_quantity) {
                        WC()->cart->set_quantity($key, $quantity);
                    }
                }
            }
        }
}




