<?php

/*
 *  Plugin name: Woocommerce Auto Sale Category
 *  Plugin URI: https://greasydesign.sk
 *  Description: WooCommerce plugin to automatically assign product to sale category on save. If product sale price is removed, the product is removed from sale category.
 *  Version: 1.0
 *  Author: Erik Masný
 *  Author URI: https://greasydesign.sk
 * 
 */


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



// ================================== //
//             SETTINGS               //
// ================================== //

$cat_name = "Sale";




// ================================== //
//          ADD TO SALE CAT           //
// ================================== //

add_action( 'save_post_product', 'add_product_to_sale_cat' );

function add_product_to_sale_cat( $post_id ) {
    global $cat_name;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    if ( ! current_user_can( 'edit_product', $post_id ) ) {
        return $post_id;
    }

    if( get_post_status( $post_id ) == 'publish' && isset($_POST['_sale_price']) ) {
        $sale_price = $_POST['_sale_price'];

        if( $sale_price >= 0 && ! has_term( $cat_name, 'product_cat', $post_id ) ){
            wp_set_object_terms($post_id, $cat_name, 'product_cat', true );
        }
    }
}



// ================================== //
//       REMOVE FROM SALE CAT         //
// ================================== //


add_action( 'save_post_product', 'remove_product_from_sale_cat' );

function remove_product_from_sale_cat( $post_id ) {
    global $cat_name;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    if ( ! current_user_can( 'edit_product', $post_id ) ) {
        return $post_id;
    }
    if( get_post_status( $post_id ) == 'publish' && isset($_POST['_sale_price']) ) {
        $sale_price = $_POST['_sale_price'];

        if( $sale_price >= 0 && ! has_term( $cat_name, 'product_cat', $post_id ) ){
            wp_set_object_terms($post_id, $cat_name, 'product_cat', true );
        } elseif ( $sale_price == '' && has_term( $cat_name, 'product_cat', $post_id ) ) {
            wp_remove_object_terms( $post_id, $cat_name, 'product_cat' );
        }
    }
}