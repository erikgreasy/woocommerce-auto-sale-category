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
//          ADD TO SALE CAT           //
// ================================== //

add_action( 'save_post_product', 'handle_product_to_sale_cat' );

function handle_product_to_sale_cat( $post_id ) {

    // SET THIS TO YOUR SALE CATEGORY NAME
    $cat_name = 'Sale';


    // IF AUTOSAVE THEN -> DO NOTHING
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    // IF USER DONT HAVE PERMISSIONS TO EDIT PRODUCT -> DO NOTHING
    if ( ! current_user_can( 'edit_product', $post_id ) ) {
        return $post_id;
    }



    $product = new WC_Product_Variable( $post_id );

    // PRODUCT WITH VARIATIONS
    if( $product->has_child() ) {
        if (get_post_status( $post_id ) == 'publish') {

            if( $product->is_on_sale() ) {
                wp_set_object_terms($post_id, $cat_name, 'product_cat', true );
                
            } else {
                wp_remove_object_terms( $post_id, $cat_name, 'product_cat' );
        
            }
        }
        
    // PRODUCT WITHOUT VARIATIONS
    } else {

        if( get_post_status( $post_id ) == 'publish' && isset($_POST['_sale_price']) ) {
            $sale_price = $_POST['_sale_price'];
            
            // has sale price
            if( $sale_price > 0 && ! has_term( $cat_name, 'product_cat', $post_id ) ){
                wp_set_object_terms($post_id, $cat_name, 'product_cat', true );
            }
            // doesnt have sale price
            if ( $sale_price == '' && has_term( $cat_name, 'product_cat', $post_id ) ) {
                wp_remove_object_terms( $post_id, $cat_name, 'product_cat' );
            }
        }
    }

}
