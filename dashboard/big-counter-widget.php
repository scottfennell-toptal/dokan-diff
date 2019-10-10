<?php
/**
 *  Dashboard Widget Template
 *
 *  Dashboard Big Counter widget template
 *
 *  @since 2.4
 *
 *  @author weDevs <info@wedevs.com>
 *
 *  @package dokan
 */
global $wpdb;
$uid = get_current_user_id();

if (date("j") < 15) {
    $day = 15;
    $day1 = 1;
    $mo = 0;
    $mo1 = 0;
} else {
    $day = 1;
    $day1 = 15;
    $mo = 1;
    $mo1 = 0;
}
$cycle_0_time = mktime(0, 0, 0, date("n") + $mo, $day, date("Y"));
$cycle_0 = date("n/j/Y", $cycle_0_time);

$cycle_1_time = mktime(0, 0, 0, date("n") + $mo1, $day1, date("Y"));
$cycle_1 = date("n/j/Y", $cycle_1_time);

$cycle_2_time = mktime(0, 0, 0, date("n") + $mo - 1, $day, date("Y"));
$cycle_2 = date("n/j/Y", $cycle_2_time);
?>
<style>

</style>

<div>
    <div class="stat mt-3 mt-md-0 mb-5">
        <div class="stat__item">
            <p><?php _e('Sales', 'dokan-lite'); ?> <?php echo $cycle_1; ?> - <?php echo $cycle_0; ?></p>
            <?php
            $tot = 0;
            $all_orders = dokan_get_seller_orders_by_date(date('Y-m-d', $cycle_1_time), date('Y-m-d', $cycle_0_time), get_current_user_id(), dokan_withdraw_get_active_order_status());
            foreach ($all_orders as $ord) {
                $order = new WC_Order($ord->order_id);
                $tot += $order->get_total() - $order->get_total_refunded();
            }
            ?>
            <h2 class="stat__title"><?php echo wc_price($tot); ?></h2>
        </div>
        <?php 
          $ammnt = round(dokan_get_seller_balance(get_current_user_id(), false), 2);
          $withholding = round(dokan_get_seller_withholding(get_current_user_id(), false), 2);
        ?>
        <div class="stat__item">
            <div>
              <?php _e('To be paid on ', 'dokan-lite'); ?><?php echo $cycle_0; ?>
              <i class="bootstrap-themes-icon-info position-relative text-gray-thin">
                <div class="card card--withholding-breakdown">
                  <div class="card-body">
                    <ul>
                      <li>Commission: <?php echo wc_price($ammnt); ?></li>
                      <li>US Royalty Withholding: <span class="text-danger">-<?php echo wc_price($withholding); ?></span></li>
                      <li>Earnings: <?php echo wc_price($ammnt - $withholding); ?></li>
                    </ul>
                  </div>
                </div>
              </i>
          </div>
            <h2 class="stat__title"><?php echo wc_price($ammnt - $withholding); ?></h2>
        </div>
        <div class="stat__item">
            <p><?php _e('Lifetime Earnings', 'dokan-lite'); ?></p>
            <h2 class="stat__title"><?php echo wc_price($wpdb->get_var("SELECT SUM(`net_amount`) FROM `wp_dokan_orders` WHERE `seller_id`='" . get_current_user_id() . "' AND `order_status`='wc-completed'")) ?></h2>
        </div>


    </div>
</div>

