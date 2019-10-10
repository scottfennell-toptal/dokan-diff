<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


$store_user = get_userdata(get_query_var('author'));
$store_info = dokan_get_store_info($store_user->ID);
$map_location = isset($store_info['location']) ? esc_attr($store_info['location']) : '';

$meta_info = get_user_meta($store_user->ID, "dokan_profile_settings", true);
$theme_store_img = wp_get_attachment_image($store_info["gravatar"], "square_crop");

get_header();
?>
<?php do_action('woocommerce_before_main_content'); ?>
</div>
</section>
</main>
<?php if (isset($store_info['banner']) && !empty($store_info['banner'])) { ?>
    <section class="profile__hero" 
             style="background-image: url(<?php echo wp_get_attachment_url($store_info['banner']); ?>)"    
             alt="<?php echo html_entity_decode(isset($store_info['store_name']) ? esc_html($store_info['store_name']) : ''); ?>"
             title="<?php echo html_entity_decode(isset($store_info['store_name']) ? esc_html($store_info['store_name']) : ''); ?>"
             ></section>
         <?php } else { ?> 
    <section class="profile__hero"></section>
<?php } ?> 

<?php
$get_support = 0;

if (get_current_user_id()) {
    $current_user = wp_get_current_user();
    while (have_posts()) {
        the_post();
        if (wc_customer_bought_product($current_user->user_email, $current_user->ID, get_the_ID())) {
            $get_support = 1;
        }
    }
}
?>
<section class="section section--pt-0">
    <div class="container">
        <div class="profile">
            <div class="row">
                <div class="col-lg-8 mb-2">
                    <div class="d-flex">
                        <div class="profile__avatar">
                            <?php echo $theme_store_img; ?>
                        </div>
                        <div class="profile__description">
                            <h3 class="profile__description__title"><?php echo html_entity_decode($store_info['store_name']); ?></h3>
                            <p class="d-none d-sm-block"><?php echo html_entity_decode($store_info["store_description"]); ?></p>
                        </div>
                    </div>
                    <p class="d-sm-none"><?php echo html_entity_decode($store_info["store_description"]); ?></p>
                </div>
                <div class="col-lg-4 d-sm-flex align-items-sm-center justify-content-sm-start justify-content-lg-end mt-2">
                    <!-- Removing the social icons for now -->
                    <!-- <ul class="list-inline list-social mr-lg-3 ml-sm-3 mb-2 mb-sm-0 order-lg-1 order-sm-2">
                        <li class="list-inline-item"><a class="list-social__link bootstrap-themes-icon-facebook-squared" href="#"></a></li>
                        <li class="list-inline-item"><a class="list-social__link bootstrap-themes-icon-pinterest-squared" href="#"></a></li>
                        <li class="list-inline-item"><a class="list-social__link bootstrap-themes-icon-twitter" href="#"></a></li>
                    </ul> -->
                    <?php if (get_current_user_id() == $store_user->ID) { ?>
                        <a class="btn btn-brand d-block d-md-inline-block order-sm-1 order-lg-2" href="<?php echo get_bloginfo("url"); ?>/my-account/vendor/settings/store/">Edit your profile</a>
                    <?php } elseif ($get_support) { ?>
                        <a class="btn btn-brand d-block d-md-inline-block order-sm-1 order-lg-2" href="<?php echo $store_info["support_link"]; ?>">Contact for support</a>
                    <?php } elseif ($store_info["public_support_link"]) { ?>
                        <a class="btn btn-brand d-block d-md-inline-block order-sm-1 order-lg-2" href="<?php echo $store_info["public_support_link"]; ?>">Contact Seller</a>

                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="theme-cards-holder">
            <ul class="row">
                <?php while (have_posts()) : the_post(); ?>

                    <?php wc_get_template_part('content', 'product'); ?>

                <?php endwhile; // end of the loop.    ?>
            </ul>
        </div>
    </div>
</section>
<?php get_footer(); ?>
