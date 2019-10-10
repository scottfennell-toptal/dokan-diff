<?php
/**
 *  Dokan Dashboard Template
 *
 *  Dokan Main Dahsboard template for Fron-end
 *
 *  @since 2.4
 *
 *  @package dokan
 */
?>

<div class="dokan-dashboard-wrap">

    <?php
    /**
     *  dokan_dashboard_content_before hook
     *
     *  @hooked get_dashboard_side_navigation
     *
     *  @since 2.4
     */
    do_action('dokan_dashboard_content_before');
    ?>

    <div class="dokan-dashboard-content">

        <?php
        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked show_seller_dashboard_notice
         *
         *  @since 2.4
         */
        do_action('dokan_dashboard_content_inside_before');
        ?>

        <article class="dashboard-content-area">

            <?php
            /**
             *  dokan_dashboard_before_widgets hook
             *
             *  @hooked dokan_show_profile_progressbar
             *
             *  @since 2.4
             */
            //do_action('dokan_dashboard_before_widgets');

            $widgets = new Dokan_Template_Dashboard();
            $widgets->get_big_counter_widgets();
            ?>
            <?php
            global $wpdb;

            $results = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "dokan_orders` WHERE `seller_id`='" . get_current_user_id() . "' AND `order_status`='wc-completed'");
            foreach ($results as $result) {
                $order = wc_get_order($result->order_id);
                if ($order) {
                    $complete_date = get_post_meta($result->order_id, '_completed_date', true);
                    if ($complete_date) {
                        $order_array[strtotime(date("Ymd", strtotime($complete_date))) * 1000][$result->order_id] = ($order->get_total() - $order->get_total_refunded());
                    }
                }
            }

            $show_array = array("order_counts" => array(), "order_amounts" => array());

            for ($i = 0; $i < 15; $i++) {
                $the_time = mktime(0, 0, 0, date("n"), date("j") - $i, date("Y")) * 1000;
                $show_array["order_counts"][] = array('"' . $the_time . '"', count($order_array[$the_time]));
                $the_sum = array_sum($order_array[$the_time]);
                if (!$the_sum) {
                    $the_sum = 0;
                }
                $show_array["order_amounts"][] = array('"' . $the_time . '"', $the_sum);
            }
            ?>
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">

                    <div class="dashboard-widget sells-graph">
                        <div class="widget-title">
                            Sales value
                            <span class="fs-13 text-gray-soft">(Past 2 weeks)</span>
                        </div>

                        <div class="chart-container">
                            <div class="chart-val-placeholder main" style="width: 100%; height: 350px;"></div>
                        </div>

                        <script type="text/javascript">
                            jQuery(function ($) {

                                var order_data = jQuery.parseJSON('<?php echo str_replace('""', '"', stripslashes(json_encode($show_array))); ?>');
                                var isRtl = '0';
                                var series = [
                                    {
                                        label: "Sales total",
                                        data: order_data.order_amounts,
                                        shadowSize: 0,
                                        hoverable: true,
                                        points: {show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true},
                                        lines: {show: true, lineWidth: 2, fill: false},
                                        shadowSize: 0,
                                        prepend_tooltip: "&#36;"
                                    },
                                ];

                                var main_chart = jQuery.plot(
                                        jQuery('.chart-val-placeholder.main'),
                                        series,
                                        {
                                            legend: {
                                                show: false,
                                            },
                                            series: {
                                                lines: {show: true, lineWidth: 4, fill: false},
                                                points: {show: true}
                                            },
                                            grid: {
                                                borderColor: '#eee',
                                                color: '#aaa',
                                                borderWidth: 1,
                                                hoverable: true,
                                                show: true,
                                                aboveData: false,
                                            },
                                            xaxis: {
                                                color: '#aaa',
                                                position: "bottom",
                                                tickColor: 'transparent',
                                                mode: "time",
                                                timeformat: "%d %b",
                                                monthNames: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                                                tickLength: 1,
                                                minTickSize: [1, "day"],
                                                font: {
                                                    color: "#aaa"
                                                },
                                                transform: function (v) {
                                                    return (isRtl == '1') ? -v : v;
                                                },
                                                inverseTransform: function (v) {
                                                    return (isRtl == '1') ? -v : v;
                                                }
                                            },
                                            yaxes: [
                                                {
                                                    position: (isRtl == '1') ? "right" : "left",
                                                    min: 0,
                                                    minTickSize: 1,
                                                    tickDecimals: 0,
                                                    color: '#d4d9dc',
                                                    font: {color: "#aaa"}
                                                },
                                                {
                                                    position: (isRtl == '1') ? "right" : "left",
                                                    min: 0,
                                                    tickDecimals: 2,
                                                    alignTicksWithAxis: 1,
                                                    color: 'transparent',
                                                    font: {color: "#aaa"}
                                                }
                                            ],
                                            colors: ["#7952B3"]
                                        }
                                );

                                jQuery('.chart-placeholder').resize();
                            });

                        </script>
                    </div> <!-- .sells-graph -->
                </div>

                <div class="col-md-6">

                    <div class="dashboard-widget sells-graph">
                        <div class="widget-title">
                            Order count
                            <span class="fs-13 text-gray-soft">(Past 2 weeks)</span>
                        </div>

                        <div class="chart-container">
                            <div class="chart-count-placeholder main" style="width: 100%; height: 350px;"></div>
                        </div>

                        <script type="text/javascript">
                            jQuery(function ($) {

                                var order_data = jQuery.parseJSON('<?php echo str_replace('""', '"', stripslashes(json_encode($show_array))); ?>');
                                var isRtl = '0';
                                var series = [

                                    {
                                        label: "Number of orders",
                                        data: order_data.order_counts,
                                        shadowSize: 0,
                                        hoverable: true,
                                        points: {show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true},
                                        lines: {show: true, lineWidth: 2, fill: false},
                                        shadowSize: 0,
                                        append_tooltip: " sales"
                                    },
                                ];

                                var main_chart = jQuery.plot(
                                        jQuery('.chart-count-placeholder.main'),
                                        series,
                                        {
                                            legend: {
                                                show: false,
                                            },
                                            series: {
                                                lines: {show: true, lineWidth: 4, fill: false},
                                                points: {show: true}
                                            },
                                            grid: {
                                                borderColor: '#eee',
                                                color: '#aaa',
                                                borderWidth: 1,
                                                hoverable: true,
                                                show: true,
                                                aboveData: false,
                                            },
                                            xaxis: {
                                                color: '#aaa',
                                                position: "bottom",
                                                tickColor: 'transparent',
                                                mode: "time",
                                                timeformat: "%d %b",
                                                monthNames: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                                                tickLength: 1,
                                                minTickSize: [1, "day"],
                                                font: {
                                                    color: "#aaa"
                                                },
                                                transform: function (v) {
                                                    return (isRtl == '1') ? -v : v;
                                                },
                                                inverseTransform: function (v) {
                                                    return (isRtl == '1') ? -v : v;
                                                }
                                            },
                                            yaxes: [
                                                {
                                                    position: (isRtl == '1') ? "right" : "left",
                                                    min: 0,
                                                    minTickSize: 1,
                                                    tickDecimals: 0,
                                                    color: '#d4d9dc',
                                                    font: {color: "#aaa"}
                                                },
                                                {
                                                    position: (isRtl == '1') ? "right" : "left",
                                                    min: 0,
                                                    tickDecimals: 2,
                                                    alignTicksWithAxis: 1,
                                                    color: 'transparent',
                                                    font: {color: "#aaa"}
                                                }
                                            ],
                                            colors: ["#7952B3"]
                                        }
                                );

                                jQuery('.chart-placeholder').resize();
                            });

                        </script>
                    </div> <!-- .sells-graph -->
                </div>
            </div>







        </article><!-- .dashboard-content-area -->

        <?php
        /**
         *  dokan_dashboard_content_inside_after hook
         *
         *  @since 2.4
         */
        do_action('dokan_dashboard_content_inside_after');
        ?>


    </div><!-- .dokan-dashboard-content -->

    <?php
    /**
     *  dokan_dashboard_content_after hook
     *
     *  @since 2.4
     */
    do_action('dokan_dashboard_content_after');
    ?>

</div><!-- .dokan-dashboard-wrap -->
