<?php
global $woocommerce;
global $wpdb;

$seller_id = get_current_user_id();
$order_status = isset($_GET['order_status']) ? sanitize_key($_GET['order_status']) : 'all';
$paged = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;
$limit = 10;
$offset = ( $paged - 1 ) * $limit;
$order_date = isset($_GET['order_date']) ? sanitize_key($_GET['order_date']) : NULL;
$user_orders = get_marketplace() -> dokan_template_tags -> get_seller_orders($seller_id, array("wc-completed","wc-refunded"), $order_date, $limit, $offset);

if ($user_orders) {
    ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?php _e('Order', 'dokan-lite'); ?></th>
                    <th><?php _e('Date', 'dokan-lite'); ?></th>
                    <th><?php _e('Product', 'dokan-lite'); ?></th>
                    <th><?php _e('Total', 'dokan-lite'); ?></th>
                    <th><?php _e('Earned', 'dokan-lite'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($user_orders as $order) {
                    $the_order = new WC_Order($order->order_id);

                    $products = $wpdb->get_results("SELECT DISTINCT `wp_posts`.`ID`,`wp_woocommerce_order_items`.`order_item_id`,`wp_woocommerce_order_items`.`order_item_name`  FROM `wp_woocommerce_order_items`,`wp_woocommerce_order_itemmeta`,`wp_posts` WHERE `wp_woocommerce_order_itemmeta`.`meta_key`='_product_id' AND `wp_woocommerce_order_itemmeta`.`order_item_id`=`wp_woocommerce_order_items`.`order_item_id` AND `wp_woocommerce_order_itemmeta`.`meta_value` = `wp_posts`.`ID` AND `wp_woocommerce_order_items`.`order_id`='" . $order->order_id . "'");
                    $the_price = $wpdb->get_var("SELECT `meta_value` FROM `wp_woocommerce_order_itemmeta` WHERE `order_item_id`='" . $prod->order_item_id . "' AND `meta_key`='_line_total'");
                    $the_commision = $wpdb->get_var("SELECT `net_amount` FROM `wp_dokan_orders` WHERE `order_id`='" . $order->order_id . "'");
                    $the_withholding = $wpdb->get_var("SELECT `royalty_amount` FROM `wp_dokan_orders` WHERE `order_id`='" . $order->order_id . "'");
                    ?>
                    <tr >
                        <th data-title="<?php _e('Order', 'dokan-lite'); ?>" >
                            <?php echo '<a class="text-brand" href="' . wp_nonce_url(add_query_arg(array('order_id' => dokan_get_prop($the_order, 'id')), dokan_get_navigation_url('orders')), 'dokan_view_order') . '"><strong>' . sprintf(__('Order %s', 'dokan-lite'), esc_attr($the_order->get_order_number())) . '</strong></a>'; ?>
                        </th>
                        <td  data-title="<?php _e('Date', 'dokan-lite'); ?>" >
                            <?php
                            if ('0000-00-00 00:00:00' == dokan_get_date_created($the_order)) {
                                $t_time = $h_time = __('Unpublished', 'dokan-lite');
                            } else {
                                $t_time = get_the_time(__('Y/m/d g:i:s A', 'dokan-lite'), dokan_get_prop($the_order, 'id'));

                                $gmt_time = strtotime(dokan_get_date_created($the_order) . ' UTC');
                                $time_diff = current_time('timestamp', 1) - $gmt_time;

                                if ($time_diff > 0 && $time_diff < 24 * 60 * 60)
                                    $h_time = sprintf(__('%s ago', 'dokan-lite'), human_time_diff($gmt_time, current_time('timestamp', 1)));
                                else
                                    $h_time = get_the_time(__('Y/m/d', 'dokan-lite'), dokan_get_prop($the_order, 'id'));
                            }

                            echo get_the_time(__('m/d/Y', 'dokan-lite'), dokan_get_prop($the_order, 'id'));
                            ?>
                        </td>
                        <td  data-title="<?php _e('Product', 'dokan-lite'); ?>" >
                            <?php
                            $total_pr = 0;
                            foreach ($products as $prod) {
                                $prod_id = $prod->ID;
                                $_pf = new WC_Product_Factory();
                                $_prod = $_pf->get_product($prod_id);
                                if ($wpdb->get_var("SELECT `permission_id` FROM `" . $wpdb->prefix . "woocommerce_downloadable_product_permissions` WHERE `product_id`='" . $prod_id . "' AND `order_id`='" . $order->order_id . "'")) {

                                    $pr = $wpdb->get_var("SELECT `meta_value` FROM `" . $wpdb->prefix . "woocommerce_order_itemmeta` WHERE `meta_key`='_line_total' AND `order_item_id`='" . $prod->order_item_id . "'");

                                    if ($the_order->data["parent_id"]) {
                                        $subtotal = wc_get_order_item_meta($prod->order_item_id, "_line_subtotal", true);
                                        $parent_order_items = $wpdb->get_results("SELECT `order_item_id` FROM `" . $wpdb->prefix . "woocommerce_order_items` WHERE `order_id`='" . $the_order->data["parent_id"] . "' AND `order_item_name`='" . $prod->order_item_name . "'");
                                        foreach ($parent_order_items as $parent_order_item) {
                                            if ($subtotal == wc_get_order_item_meta($parent_order_item->order_item_id, "_line_subtotal", true)) {
                                                $parent_order_item_id = $parent_order_item->order_item_id;
                                            }
                                        }
//                                        $parent_order_item_id = $wpdb->get_var("SELECT `order_item_id` FROM `" . $wpdb->prefix . "woocommerce_order_items` WHERE `order_id`='" . $the_order->data["parent_id"] . "' AND `order_item_name`='" . $prod->order_item_name . "'");
                                        $type = $wpdb->get_var("SELECT `meta_value` FROM `" . $wpdb->prefix . "woocommerce_order_itemmeta` WHERE `meta_key`='License Type' AND `order_item_id`='" . $parent_order_item_id . "'");
                                    } else {
                                        $type = $wpdb->get_var("SELECT `meta_value` FROM `" . $wpdb->prefix . "woocommerce_order_itemmeta` WHERE `meta_key`='License Type' AND `order_item_id`='" . $prod->order_item_id . "'");
                                    }
                                    $total_pr += $pr;

                                    echo '<div class="order_item_prod"><div class="order_item_prod_price" style="">' . wc_price($pr) . '</div><a class="text-brand" href="' . get_the_permalink($prod_id) . '"><strong>' . get_the_title($prod_id) . '</strong></a><small>' . ucfirst($type) . '</small></div>';
                                }
                            }
                            ?>
                        </td>
                        <td  data-title="<?php _e('Price', 'dokan-lite'); ?>" >
                            <?php
                            if ($the_order->get_total_refunded()) {
                                echo '<del>' . wc_price($the_order->get_total()) . '</del> ' . wc_price($the_order->get_total() - $the_order->get_total_refunded());
                            } else {
                                echo wc_price($total_pr);
                            }
                            ?>
                        </td>
                        <td data-title="<?php _e('Earned', 'dokan-lite'); ?>" >
                            <?php echo wc_price($the_commision - $the_withholding); ?>
                            <i class="bootstrap-themes-icon-info position-relative text-gray-thin">
                              <div class="card card--withholding-breakdown">
                                <div class="card-body">
                                  <ul>
                                    <li>Commission: <?php echo wc_price($the_commision); ?></li>
                                    <li>US Royalty Withholding: <span class="text-danger">-<?php echo wc_price($the_withholding); ?></span></li>
                                    <li>Earnings: <?php echo wc_price($the_commision - $the_withholding); ?></li>
                                  </ul>
                                </div>
                              </div>
                            </i>
                        </td>


                        <td class="diviader"></td>
                    </tr>

                    <?php
                }
                ?>

            </tbody>

        </table>
    </div>
    <?php
    $order_count = get_marketplace() -> dokan_template_tags -> get_seller_orders_number($seller_id, array("wc-completed","wc-refunded"));
    $num_of_pages = ceil($order_count / $limit);
    $base_url = dokan_get_navigation_url('orders');
    if ($num_of_pages > 1) {
        echo '<div class="pagination-wrap">';
        $pages = paginate_links(array(
            'current' => $paged,
            'total' => $num_of_pages,
            'base' => $base_url . '%_%',
            'format' => '?pagenum=%#%',
            'add_args' => false,
            'type' => 'array',
            'prev_next' => TRUE,
            'prev_text' => '<span aria-hidden="true"><i class="bootstrap-themes-icon-left-open"></i></span><span class="sr-only">Previous</span>',
            'next_text' => '<span aria-hidden="true"><i class="bootstrap-themes-icon-right-open"></i></span><span class="sr-only">Next</span>',
        ));

        if (is_array($pages)) {
            $current_page = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
            echo '<nav><ul class="pagination justify-content-center">';
            foreach ($pages as $i => $page) {
                if ($current_page == 1 && $i == 0) {
                    echo "<li  class='page-item active'>$page</li>";
                } else {
                    if ($current_page != 1 && $current_page == $i) {
                        echo "<li class='page-item active'>$page</li>";
                    } else {
                        echo "<li class='page-item'>$page</li>";
                    }
                }
            }
            echo '</ul></nav>';
        }
    }
    ?>

<?php } else { ?>

    <div class="container container--xs mt-5">
        <h1 class="mb-1 text-center"><?php _e('No orders found', 'dokan-lite'); ?></h1>
        <div class="fs-14 text-gray text-center mb-5">
            <p>You do not currently have any orders</p>
        </div>

    </div>


<?php } ?>

<script>
    (function ($) {
        $(document).ready(function () {
            $('.datepicker').datepicker({
                dateFormat: 'yy-m-d'
            });
        });
    })(jQuery);
</script>