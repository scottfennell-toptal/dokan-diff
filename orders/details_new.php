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
<div class="dokan-orders-details">
    <h2>Order #<?php echo $order->get_order_number(); ?></h2>
    <div class="row justify-content-between">
        <div class="col-xl-4 col-lg-5 mb-4 mb-lg-0 order-lg-2">
            <div class="card card--summary">
                <div class="card-body">
                    <?php
                    foreach ($order->get_items() as $item_id => $item) {
                        $product = apply_filters('woocommerce_order_item_product', $item->get_product(), $item);

                        wc_get_template('order/order-details-item-vendor.php', array(
                            'order' => $order,
                            'item_id' => $item_id,
                            'item' => $item,
                            'show_purchase_note' => $show_purchase_note,
                            'purchase_note' => $product ? $product->get_purchase_note() : '',
                            'product' => $product,
                        ));
                    }
                    ?>


                    <div class="card--summary__footer">
                        <p class="mb-0">Total</p>
                        <p><?php echo $order->get_formatted_order_total(); ?></p>
                    </div>
                    <div class="dokan-panel-body" id="woocommerce-order-items">
                        <hr />
                        <div class="wc-order-data-row wc-order-bulk-actions">

                            <p class="add-items">

                                <?php if (( $order->get_total() - $order->get_total_refunded() ) > 0) : ?>
                                    <button type="button" class="btn btn-brand refund-items"><?php _e('Issue Refund', 'dokan'); ?></button>
                                <?php endif; ?>
                            </p>
                            <div class="clear"></div>
                        </div>

                        <?php if (( $order->get_total() - $order->get_total_refunded() ) > 0) : ?>
                            <div class="wc-order-data-row wc-order-refund-items" style="display: none;">
                                <table class="wc-order-totals dokan-table dokan-table-strip">

                                    <tr>
                                        <td><?php _e('Amount already refunded', 'dokan'); ?>:</td>
                                        <td class="total">-<?php echo wc_price($order->get_total_refunded(), array('currency' => dokan_replace_func('get_order_currency', 'get_currency', $order))); ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php _e('Total available to refund', 'dokan'); ?>:</td>
                                        <td class="total"><?php echo wc_price($order->get_total() - $order->get_total_refunded(), array('currency' => dokan_replace_func('get_order_currency', 'get_currency', $order))); ?></td>
                                    </tr>
                                    <tr>
                                        <td><label for="refund_amount"><?php _e('Refund amount', 'dokan'); ?>:</label></td>
                                        <td class="total">
                                            <input type="text" class="form-control text" id="refund_amount" name="refund_amount"  />
                                            <div class="clear"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label for="refund_reason"><?php _e('Reason for refund (optional)', 'dokan'); ?>:</label></td>
                                        <td class="total">
                                            <input type="text" class="form-control text" id="refund_reason" name="refund_reason" />
                                            <div class="clear"></div>
                                        </td>
                                    </tr>
                                </table>
                                <div class="clear"></div>
                                <div class="refund-actions">
                                    <?php $refund_amount = '<span class="wc-order-refund-amount">' . wc_price(0, array('currency' => dokan_replace_func('get_order_currency', 'get_currency', $order))) . '</span>'; ?>

                                    <button type="button" class="btn btn-brand do-manual-refund tips" data-tip="<?php esc_attr_e('You will need to manually issue a refund through your payment gateway after using this.', 'dokan'); ?>"><?php printf(_x('Refund %s', 'Refund $amount', 'dokan'), $refund_amount); ?></button>
                                    <button type="button" class="btn btn-brand cancel-action"><?php _e('Cancel', 'dokan'); ?></button>
                                    <div class="clear"></div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php endif; ?>
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
</div>