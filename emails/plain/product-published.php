<?php
/**
 * Product published Email. ( plain text )
 *
 * An email sent to the vendor when a new Product is published from pending.
 *
 * @class       Dokan_Email_Product_Published
 * @version     2.6.8
 * 
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$the_acc = get_user_by("login", $data['username']);

echo "= " . $email_heading . " =\n\n";

?><?php _e( 'Hello '.$the_acc->first_name, 'dokan-lite' ); echo " \n\n";?>

<?php _e( 'Your product '.$data['product-title'], 'dokan-lite' ); ?> <?php _e( 'has been approved! Congrats!', 'dokan-lite' ); echo " \n\n"; ?>

<?php _e( 'View product : '.$data['product_url'], 'dokan-lite' ); echo " \n\n"; ?>
<?php _e( 'To edit this product click here: '.$data['product_url']."/?edit_prod=1", 'dokan-lite' ); echo " \n\n"; ?> 
<?php

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
