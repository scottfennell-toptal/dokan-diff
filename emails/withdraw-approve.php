<?php
/**
 * New Withdraw request Email.
 *
 * An email sent to the admin when a new withdraw request is created by vendor.
 *
 * @class       Dokan_Email_Withdraw_Approved
 * @version     2.6.8
 * 
 */
if (!defined('ABSPATH')) {
    exit;
}

$the_acc = get_user_by("login", $data['username']);

$email_heading = "You earned ".$data['amount'];
do_action('woocommerce_email_header', $email_heading, $email);
?>
<p>
    <?php _e('Hi ' . $the_acc->first_name, 'dokan-lite'); ?>
</p>
<p>
    <?php _e('We’re sending you ' . $data['amount'] . " via " . $data['method'] . ". Congrats!", 'dokan-lite'); ?>
</p>
<p>
    <?php _e('It may take time for the payment to process, but if you have any issues, please contact us.', 'dokan-lite'); ?>
</p>
<p>
    <?php _e('Thanks so much for selling with us!', 'dokan-lite'); ?>
</p>

<?php
do_action('woocommerce_email_footer', $email);
