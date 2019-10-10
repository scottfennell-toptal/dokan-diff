<?php
global $woocommerce, $current_user, $wpdb;

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if (!dokan_is_seller_has_order($current_user->ID, $order_id)) {
    echo '<div class="dokan-alert dokan-alert-danger">' . __('This is not yours, I swear!', 'dokan-lite') . '</div>';
    return;
}

$statuses = wc_get_order_statuses();
$order = new WC_Order($order_id);
?>
<h2 class="mt-5">Order #<?php echo $order->get_order_number(); ?></h2>
<div class="row justify-content-between">
    <div class="col-xl-4 col-lg-5 mb-4 mb-lg-0 order-lg-2">
        <div class="card card--summary">
            <div class="card-body">
                <?php
                $the_disc = 0;

                foreach ($order->get_items() as $item_id => $item) {
                    $product = apply_filters('woocommerce_order_item_product', $item->get_product(), $item);
                    $item_subtotal = wc_get_order_item_meta($item_id, "_line_subtotal", true);
                    $item_total = wc_get_order_item_meta($item_id, "_line_total", true);
                    if ($item_total < $item_subtotal) {
                        $the_disc += ($item_subtotal - $item_total);
                    }
                    wc_get_template('order/order-details-item-vendor.php', array(
                        'order' => $order,
                        'item_id' => $item_id,
                        'item' => $item,
                        'show_purchase_note' => $show_purchase_note,
                        'purchase_note' => $product ? $product->get_purchase_note() : '',
                        'product' => $product,
                    ));
                }

                $refunded = 0;

                if ($the_disc > 0) {
                    ?>
                    <div class="card--summary__footer">
                        <p class="mb-0"><?php _e('Discount', 'dokan'); ?></p>
                        <p>-<?php echo wc_price($the_disc); ?></p>
                    </div>
                    <?php
                }

                if ($order->get_total_refunded()) {
                    $refunded += $order->get_total_refunded()
                    ?>
                    <div class="card--summary__footer">
                        <p class="mb-0"><?php _e('Refund', 'dokan'); ?></p>
                        <p>-<?php echo wc_price($order->get_total_refunded()); ?></p>
                    </div>
                <?php } ?>

                <div class="card--summary__footer">
                    <p class="mb-0">Total</p>
                    <p><?php echo wc_price($order->get_total() - $refunded); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-7">
        <div class="theme-description__list mb-4">
            <div class="theme-description__list__item"><span class="theme-description__item__title">Order date</span><span><?php echo wc_format_datetime($order->get_date_created()); ?></span></div>
            <div class="theme-description__list__item"><span class="theme-description__item__title">Payment type</span><span><?php echo wp_kses_post($order->get_payment_method_title()); ?></span></div>

            <?php if ($order->get_formatted_billing_full_name()) : ?>
                <div class="theme-description__list__item"><span class="theme-description__item__title">Customer name</span><span><?php echo esc_html($order->get_formatted_billing_full_name()); ?></span></div>
            <?php endif; ?>
            <?php if ($order->get_billing_email()) : ?>
                <div class="theme-description__list__item"><span class="theme-description__item__title">Customer email</span><span><?php echo esc_html($order->get_billing_email()); ?></span></div>
            <?php endif; ?>

            <?php if ($order->get_billing_phone()) : ?>
                <div class="theme-description__list__item"><span class="theme-description__item__title">Customer phone</span><span><?php echo esc_html($order->get_billing_phone()); ?></span></div>

            <?php endif; ?>

            <?php if ($order->get_customer_note()) : ?>
                <div class="theme-description__list__item"><span class="theme-description__item__title">Customer notes</span><span><?php echo esc_html($order->get_customer_note()); ?></span></div>

            <?php endif; ?>
            <!--                  
                          <div class="theme-description__list__item"><span class="theme-description__item__title">Customer name</span><span>Dave Gamache</span></div>
                          <div class="theme-description__list__item"><span class="theme-description__item__title">Theme seller</span><span><a href="/profile">Tres Amibros</a> (<a href="#">Request support</a>)</span></div>
            -->
        </div>
    </div>
</div>