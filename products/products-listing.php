<?php global $post; ?>

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

    <div class="dokan-dashboard-content dokan-product-listing">

        <?php
        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action('dokan_dashboard_content_inside_before');
        do_action('dokan_before_listing_product');
        ?>

        <article class="dokan-product-listing-area">


            <div class="theme-cards-holder mt-5">
                <?php dokan_product_dashboard_errors(); ?>


                <?php
                $pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;

                $post_statuses = array('publish', 'draft', 'pending');
                $args = array(
                    'post_type' => 'product',
                    'post_status' => $post_statuses,
                    'posts_per_page' => 10,
                    'author' => get_current_user_id(),
                    'orderby' => 'post_date',
                    'order' => 'DESC',
                    'paged' => $pagenum,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_type',
                            'field' => 'slug',
                            'terms' => apply_filters('dokan_product_listing_exclude_type', array()),
                            'operator' => 'NOT IN',
                        ),
                    ),
                );

                if (isset($_GET['post_status']) && in_array($_GET['post_status'], $post_statuses)) {
                    $args['post_status'] = $_GET['post_status'];
                }

                if (isset($_GET['date']) && $_GET['date'] != 0) {
                    $args['m'] = $_GET['date'];
                }

                if (isset($_GET['product_cat']) && $_GET['product_cat'] != -1) {
                    $args['tax_query'][] = array(
                        'taxonomy' => 'product_cat',
                        'field' => 'id',
                        'terms' => (int) $_GET['product_cat'],
                        'include_children' => false,
                    );
                }

                if (isset($_GET['product_search_name']) && !empty($_GET['product_search_name'])) {
                    $args['s'] = $_GET['product_search_name'];
                }


                $original_post = $post;
                $product_query = new WP_Query(apply_filters('dokan_product_listing_query', $args));

                if ($product_query->have_posts()) {
                    echo ' <div class="row">';
                    while ($product_query->have_posts()) {
                        $product_query->the_post();
                        $view_class = ($post->post_status == 'publish' );

                        $product = wc_get_product($post->ID);
                        ?>
                        <div class="col-md-4">
                            <div class="theme-card">
                                <div class="theme-card__body">
                                    <a class="d-block" href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail("smaller_crop"); ?>
                                    </a>

                                </div>
                                <div class="theme-card__footer">
                                    <div class="theme-card__footer__item"><a class="theme-card__title mr-1" href="<?php the_permalink(); ?>"><?php echo ($post->post_title); ?></a>

                                        <?php
                                        if (($post->post_status == 'publish')) {
                                            echo '<p class="theme-card__info text-success">Live';
                                            if (get_post_meta($post->ID, "new_info", true)) {
                                                echo '<span class="text-gray-soft"> (Update Pending)</span>';
                                            }
                                            echo '</p>';
                                        } else {
                                            echo '<p class="theme-card__info text-warning">Pending</p>';
                                        }
                                        ?>

                                    </div>
                                    <div class="theme-card__footer__item">
                                        <p class="theme-card__price"><?php echo $product->get_price_html(); ?></p>
                                        <ul class="list-dotted">
                                            <li class="list-dotted__item"><a class="list-dotted__link" href="<?php echo get_bloginfo("url") . "/my-account/vendor/products/?action=edit&product_id=" . ($post->ID); ?>"><?php _e('Edit', 'dokan-lite'); ?></a></li>
        <!--                                                <li class="list-dotted__item"><a class="list-dotted__link" onclick="return confirm('Are you sure?');" href="<?php echo wp_nonce_url(add_query_arg(array('action' => 'dokan-delete-product', 'product_id' => $post->ID), dokan_get_navigation_url('products')), 'dokan-delete-product'); ?>"><?php _e('Delete', 'dokan-lite'); ?></a></li>-->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    echo '</div>';
                } else {
                    ?>
                    <h1 class="mb-1 text-center">No themes yet</h1>
                    <div class="fs-14 text-gray text-center">
                        <p>You haven't <a href="<?php echo get_bloginfo("url"); ?>/my-account/vendor/new-product/">submitted a theme</a> yet.</p>
                    </div>
                    <?php
                }
                ?>
            </div>
    </div>

    <?php
    wp_reset_postdata();

    $pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;
    $base_url = dokan_get_navigation_url('products');

    if ($product_query->max_num_pages > 1) {
        echo '<div class="pagination-wrap">';
        $page_links = paginate_links(array(
            'current' => $pagenum,
            'total' => $product_query->max_num_pages,
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
</article>
<div class="dokan-dahsboard-product-listing-wrapper" style="display:none;">
    <table class="dokan-table dokan-table-striped product-listing-table">
        <thead>
            <tr>
                <th><?php _e('Image', 'dokan-lite'); ?></th>
                <th><?php _e('Name', 'dokan-lite'); ?></th>
                <th><?php _e('Status', 'dokan-lite'); ?></th>
                <th><?php _e('SKU', 'dokan-lite'); ?></th>
                <th><?php _e('Stock', 'dokan-lite'); ?></th>
                <th><?php _e('Price', 'dokan-lite'); ?></th>
                <th><?php _e('Type', 'dokan-lite'); ?></th>
                <th><?php _e('Views', 'dokan-lite'); ?></th>
                <th><?php _e('Date', 'dokan-lite'); ?></th>
            </tr>
        </thead>
        <tbody>

            <?php
            if ($product_query->have_posts()) {
                while ($product_query->have_posts()) {
                    $product_query->the_post();
                    $tr_class = ($post->post_status == 'pending' ) ? ' class="danger"' : '';
                    $view_class = ($post->post_status == 'pending' ) ? 'dokan-hide' : '';
                    $product = wc_get_product($post->ID);
                    ?>
                    <tr<?php echo $tr_class; ?>>
                        <td data-title="<?php _e('Image', 'dokan-lite'); ?>">
                            <a href="<?php echo dokan_edit_product_url($post->ID); ?>"><?php echo $product->get_image(); ?></a>
                        </td>
                        <td data-title="<?php _e('Name', 'dokan-lite'); ?>">
                            <p><a href="<?php echo dokan_edit_product_url($post->ID); ?>"><?php echo $product->get_title(); ?></a></p>

                            <div class="row-actions">
                                <span class="edit"><a href="<?php echo dokan_edit_product_url($post->ID); ?>"><?php _e('Edit', 'dokan-lite'); ?></a> | </span>
                                <span class="delete"><a onclick="return confirm('Are you sure?');" href="<?php echo wp_nonce_url(add_query_arg(array('action' => 'dokan-delete-product', 'product_id' => $post->ID), dokan_get_navigation_url('products')), 'dokan-delete-product'); ?>"><?php _e('Delete Permanently', 'dokan-lite'); ?></a>  </span>
                                <span class="view <?php echo $view_class ?>"> | <a href="<?php echo get_permalink(dokan_get_prop($product, 'id')); ?>" rel="permalink"><?php _e('View', 'dokan-lite'); ?></a></span>
                                <?php do_action('dokan_product_listin_row_action', $product); ?>
                            </div>
                        </td>
                        <td class="post-status" data-title="<?php _e('Status', 'dokan-lite'); ?>">
                            <label class="dokan-label <?php echo dokan_get_post_status_label_class($post->post_status); ?>"><?php echo dokan_get_post_status($post->post_status); ?></label>
                        </td>
                        <td data-title="<?php _e('SKU', 'dokan-lite'); ?>">
                            <?php
                            if ($product->get_sku()) {
                                echo $product->get_sku();
                            } else {
                                echo '<span class="na">&ndash;</span>';
                            }
                            ?>
                        </td>
                        <td data-title="<?php _e('Stock', 'dokan-lite'); ?>">
                            <?php
                            if ($product->is_in_stock()) {
                                echo '<mark class="instock">' . __('In stock', 'dokan-lite') . '</mark>';
                            } else {
                                echo '<mark class="outofstock">' . __('Out of stock', 'dokan-lite') . '</mark>';
                            }

                            if ($product->managing_stock()) :
                                echo ' &times; ' . $product->get_stock_quantity();
                            endif;
                            ?>
                        </td>
                        <td data-title="<?php _e('Price', 'dokan-lite'); ?>">
                            <?php
                            if ($product->get_price_html()) {
                                echo $product->get_price_html();
                            } else {
                                echo '<span class="na">&ndash;</span>';
                            }
                            ?>
                        </td>
                        <td data-title="<?php _e('Type', 'dokan-lite'); ?>">
                            <?php
                            if (dokan_get_prop($product, 'product_type', 'get_type') == 'grouped'):
                                echo '<span class="product-type tips grouped" title="' . __('Grouped', 'dokan-lite') . '"></span>';
                            elseif (dokan_get_prop($product, 'product_type', 'get_type') == 'external'):
                                echo '<span class="product-type tips external" title="' . __('External/Affiliate', 'dokan-lite') . '"></span>';
                            elseif (dokan_get_prop($product, 'product_type', 'get_type') == 'simple'):

                                if ($product->is_virtual()) {
                                    echo '<span class="product-type tips virtual" title="' . __('Virtual', 'dokan-lite') . '"></span>';
                                } elseif ($product->is_downloadable()) {
                                    echo '<span class="product-type tips downloadable" title="' . __('Downloadable', 'dokan-lite') . '"></span>';
                                } else {
                                    echo '<span class="product-type tips simple" title="' . __('Simple', 'dokan-lite') . '"></span>';
                                } elseif (dokan_get_prop($product, 'product_type', 'get_type') == 'variable'):
                                echo '<span class="product-type tips variable" title="' . __('Variable', 'dokan-lite') . '"></span>';
                            else:
                                // Assuming that we have other types in future
                                echo '<span class="product-type tips ' . dokan_get_prop($product, 'product_type', 'get_type') . '" title="' . ucfirst(dokan_get_prop($product, 'product_type', 'get_type')) . '"></span>';
                            endif;
                            ?>
                        </td>
                        <td data-title="<?php _e('Views', 'dokan-lite'); ?>">
                            <?php echo (int) get_post_meta($post->ID, 'pageview', true); ?>
                        </td>
                        <td class="post-date" data-title="<?php _e('Date', 'dokan-lite'); ?>">
                            <?php
                            if ('0000-00-00 00:00:00' == $post->post_date) {
                                $t_time = $h_time = __('Unpublished', 'dokan-lite');
                                $time_diff = 0;
                            } else {
                                $t_time = get_the_time(__('Y/m/d g:i:s A', 'dokan-lite'));
                                $m_time = $post->post_date;
                                $time = get_post_time('G', true, $post);

                                $time_diff = time() - $time;

                                if ($time_diff > 0 && $time_diff < 24 * 60 * 60) {
                                    $h_time = sprintf(__('%s ago', 'dokan-lite'), human_time_diff($time));
                                } else {
                                    $h_time = mysql2date(__('Y/m/d', 'dokan-lite'), $m_time);
                                }
                            }

                            echo '<abbr title="' . dokan_date_time_format($t_time) . '">' . apply_filters('post_date_column_time', dokan_date_time_format($h_time, true), $post, 'date', 'all') . '</abbr>';
                            echo '<div class="status">';
                            if ('publish' == $post->post_status) {
                                _e('Published', 'dokan-lite');
                            } elseif ('future' == $post->post_status) {
                                if ($time_diff > 0) {
                                    echo '<strong class="attention">' . __('Missed schedule', 'dokan-lite') . '</strong>';
                                } else {
                                    _e('Scheduled', 'dokan-lite');
                                }
                            } else {
                                _e('Last Modified', 'dokan-lite');
                            }
                            ?>
                            </div>
                        </td>
                        <td class="diviader"></td>
                    </tr>

                <?php } ?>

            <?php } else { ?>
                <tr>
                    <td colspan="7"><?php _e('No product found', 'dokan-lite'); ?></td>
                </tr>
            <?php } ?>

        </tbody>

    </table>
</div>
<?php
/**
 *  dokan_dashboard_content_before hook
 *
 *  @hooked get_dashboard_side_navigation
 *
 *  @since 2.4
 */
do_action('dokan_dashboard_content_inside_after');
do_action('dokan_after_listing_product');
?>

</div><!-- #primary .content-area -->

<?php
/**
 *  dokan_dashboard_content_after hook
 *
 *  @since 2.4
 */
do_action('dokan_dashboard_content_after');
?>

</div><!-- .dokan-dashboard-wrap -->

