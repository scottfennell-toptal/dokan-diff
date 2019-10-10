<?php
/**
 * Product published Email.
 *
 * An email sent to the vendor when a new Product is published from pending.
 *
 * @class       Dokan_Email_Product_Published
 * @version     2.6.8
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$the_acc = get_user_by("login", $data['username']);

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p>
    <?php _e( 'Hello '.$the_acc->first_name, 'dokan-lite' ); ?>
<p/>
<p>
    <?php echo sprintf( __( 'Your product <a href="%s">%s</a> has been approved! Congrats!', 'dokan-lite' ), $data['product_url'], $data['product-title'] ); ?>
</p>
<p>
    <?php echo sprintf( __( 'To edit this product <a href="%s">click here</a>', 'dokan-lite' ), $data['product_url']."/?edit_prod=1" ); ?>
</p>
<?php

do_action( 'woocommerce_email_footer', $email );
