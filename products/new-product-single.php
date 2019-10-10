<?php
global $post;
global $wpdb;

$from_shortcode = false;

if (!isset($post->ID) && !isset($_GET['product_id'])) {
    wp_die(__('Access Denied, No product found', 'dokan-lite'));
}

if (isset($post->ID) && $post->ID && $post->post_type == 'product') {

    if ($post->post_author != get_current_user_id()) {
        wp_die(__('Access Denied', 'dokan-lite'));
    }

    $post_id = $post->ID;
    $post_title = $post->post_title;
    $post_content = $post->post_content;
    $post_excerpt = $post->post_excerpt;
    $post_status = $post->post_status;
    $product = wc_get_product($post_id);
}

if (isset($_GET['product_id'])) {
    $post_id = intval($_GET['product_id']);
    $post = get_post($post_id);
    $post_title = $post->post_title;
    $post_content = $post->post_content;
    $post_excerpt = $post->post_excerpt;
    $post_status = $post->post_status;
    $product = wc_get_product($post_id);
    $from_shortcode = true;
}

$_regular_price = get_post_meta($post_id, '_regular_price', true);
$_sale_price = get_post_meta($post_id, '_sale_price', true);
$is_discount = !empty($_sale_price) ? true : false;
$_sale_price_dates_from = get_post_meta($post_id, '_sale_price_dates_from', true);
$_sale_price_dates_to = get_post_meta($post_id, '_sale_price_dates_to', true);

$_sale_price_dates_from = !empty($_sale_price_dates_from) ? date_i18n('Y-m-d', $_sale_price_dates_from) : '';
$_sale_price_dates_to = !empty($_sale_price_dates_to) ? date_i18n('Y-m-d', $_sale_price_dates_to) : '';
$show_schedule = false;

if (!empty($_sale_price_dates_from) && !empty($_sale_price_dates_to)) {
    $show_schedule = true;
}

$_featured = get_post_meta($post_id, '_featured', true);
$_downloadable = get_post_meta($post_id, '_downloadable', true);
$_virtual = get_post_meta($post_id, '_virtual', true);
$_stock = get_post_meta($post_id, '_stock', true);
$_stock_status = get_post_meta($post_id, '_stock_status', true);

$_enable_reviews = $post->comment_status;
$is_downloadable = ( 'yes' == $_downloadable ) ? true : false;
$is_virtual = ( 'yes' == $_virtual ) ? true : false;
$_sold_individually = get_post_meta($post_id, '_sold_individually', true);

$terms = wp_get_object_terms($post_id, 'product_type');
$product_type = (!empty($terms) ) ? sanitize_title(current($terms)->name) : 'simple';
$variations_class = ($product_type == 'simple' ) ? 'dokan-hide' : '';
$_visibility = ( version_compare(WC_VERSION, '2.7', '>') ) ? $product->get_catalog_visibility() : get_post_meta($post_id, '_visibility', true);
$visibility_options = dokan_get_product_visibility_options();


$author_id = $wpdb->get_var("SELECT `post_author` FROM `" . $wpdb->prefix . "posts` WHERE `ID`='" . $post_id . "'");

if (!$from_shortcode) {
    get_header();
}
?>

<?php
/**
 *  dokan_dashboard_wrap_before hook
 *
 *  @since 2.4
 */
do_action('dokan_dashboard_wrap_before', $post, $post_id);
?>
<style>
    #vendor_menu{
        display: none !important;
    }
    #signup_footer{
        display: none !important;
    }
</style>

<div class="dokan-dashboard-wrap">

    <?php
    /**
     *  dokan_dashboard_content_before hook
     *  dokan_before_product_content_area hook
     *
     *  @hooked get_dashboard_side_navigation
     *
     *  @since 2.4
     */
    do_action('dokan_dashboard_content_before');
    do_action('dokan_before_product_content_area');
    ?>

    <div class="dokan-dashboard-content dokan-product-edit">

        <?php
        /**
         *  dokan_product_content_inside_area_before hook
         *
         *  @since 2.4
         */
        do_action('dokan_product_content_inside_area_before');
        ?>

        <?php
        $show_new = 1;
        if ((get_post_meta($post_id, "new_info", true) || get_post_meta($post_id, "new_info_save", true)) && ($author_id == get_current_user_id() || current_user_can('edit_others_pages'))) {

            $product = wc_get_product($post_id);
            $from_shortcode = true;

            $post_title = get_post_meta($post_id, "new_post_title", true);

            $product_cat = (get_post_meta($post_id, "new_product_cat", true));

            $post_content = get_post_meta($post_id, "new_post_content", true);

            $ext_price = get_post_meta(get_the_ID(), "new_regular_price_extended", true);
            $reg_price = get_post_meta(get_the_ID(), "new_regular_price", true);

            $bootstrap_ver = get_post_meta($post_id, "new_bootstrap_ver", true);

            $changelog = get_post_meta($post_id, "new_changelog", true);
            $current_ver = get_post_meta($post_id, "new_theme_version", true);
            $exclusive_val = get_post_meta($post_id, "new_exclusive", true);
            $preview_url = get_post_meta($post_id, "new_preview_url", true);

            $update_theme_send = get_post_meta($post_id, "update_theme_send", true);
            $update_theme_message = get_post_meta($post_id, "update_theme_message", true);

            $feat_image_id = get_post_meta($post_id, "new_thumbnail_id", true);
            if (!$feat_image_id) {
                $feat_image_id = get_post_thumbnail_id($post_id);
            }

            $theme_file_id = get_post_meta($post_id, "new_theme_file", true);
            if (!$theme_file_id) {
                $theme_file_id = get_post_meta($post_id, "theme_file", true);
            }
        } else {
            $post_title = get_the_title();

            $product_cats = wp_get_post_terms(get_the_ID(), 'product_cat');
            $product_cat = $product_cats[0]->term_id;

            $post_content = get_post($post_id)->post_content;

            $ext_price = get_post_meta(get_the_ID(), "_regular_price_extended", true);
            $reg_price = $product->get_price();

            $changelog = get_post_meta($post_id, "changelog", true);
            $bootstrap_ver = get_post_meta($post_id, "bootstrap_ver", true);
            $current_ver = get_post_meta($post_id, "theme_version", true);
            $exclusive_val = get_post_meta($post_id, "exclusive", true);
            $preview_url = get_post_meta($post_id, "preview_url", true);

            $update_theme_send = get_post_meta($post_id, "update_theme_send", true);
            $update_theme_message = get_post_meta($post_id, "update_theme_message", true);

            $feat_image_id = get_post_thumbnail_id($post_id);

            $theme_file_id = get_post_meta($post_id, "theme_file", true);
        }
        ?>

        <div class="product-edit-new-container product-edit-container">
            <?php if (Dokan_Template_Products::$errors) { ?>
                <div class="dokan-alert dokan-alert-danger">
                    <a class="dokan-close" data-dismiss="alert">&times;</a>

                    <?php foreach (Dokan_Template_Products::$errors as $error) { ?>
                        <strong><?php _e('Error!', 'dokan-lite'); ?></strong> <?php echo $error ?>.<br>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if (isset($_GET['message']) && $_GET['message'] == 'success') { ?>
                <div class="dokan-message">
                    <button type="button" class="dokan-close" data-dismiss="alert">&times;</button>
                    <strong><?php _e('Success!', 'dokan-lite'); ?></strong> <?php _e('The product has been saved successfully.', 'dokan-lite'); ?>

                    <?php if ($post->post_status == 'publish') { ?>
                        <a href="<?php echo get_permalink($post_id); ?>" target="_blank"><?php _e('View Product &rarr;', 'dokan-lite'); ?></a>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php
            $can_sell = apply_filters('dokan_can_post', true);

            if ($can_sell) {

                if (dokan_is_seller_enabled(get_current_user_id())) {
                    ?>
                    <script>
                        function readURL(input, target, input_id = "") {
                            if (input.files && input.files[0]) {
                                var reader = new FileReader();

                                reader.onload = function (e) {
                                    if (input_id) {
                                        jQuery("#" + input_id).val(e.target.result);
                                    }
                                    $(target).css('background-image', 'url(' + e.target.result + ')');
                                    var img = new Image();
                                    img.src = e.target.result;
                                    if (img.complete) { // was cached
                                        $(target).attr("data-width", img.width);
                                        $(target).attr("data-height", img.height);
                                    } else { // wait for decoding
                                        img.onload = function () {
                                            $(target).attr("data-width", img.width);
                                            $(target).attr("data-height", img.height);
                                        }
                                    }
                                }
                                reader.readAsDataURL(input.files[0]);
                        }
                        }

                        function readName(input, target) {
                            if (input.files && input.files[0]) {
                                jQuery(".has_zip_change").show();
                                jQuery(".no_zip_change").hide();
                                jQuery("#submit_theme_button").html("Submit for review");
                                $(target).html(input.files[0].name);
                                $(target).attr("data-size", Math.round(parseInt(input.files[0].size) / 1000));
                            }
                        }

                        jQuery(document).ready(function () {
                            jQuery("#preview_screenshot").change(function () {
                                readURL(this, '.upload-image__cover__overlay', "thumb_img_body");
                            });
                            jQuery("#theme_file").change(function () {
                                var ext = this.value.substr(this.value.length - 3);
                                switch (ext) {
                                    case 'zip':
                                        readName(this, '#theme_file_label');
                                        jQuery("#theme_input_feedback").hide();
                                        jQuery("#send_notif").show();
                                        break;
                                    default:
                                        jQuery('#theme_file_label').html("No file selected");
                                        jQuery("#theme_input_feedback").show();
                                        this.value = '';
                                }
                            });

                            // Function to validate the form
                            var validateForm = function () {
                                if (validate_form(jQuery("#theme_form"))) {
                                    return false;
                                } else {
                                    return true;
                                }
                            }

                            // Handling the opening/closing of the preview (and moving validation into a function here)
                            var scrollPosition = 0;

                            jQuery('[js-action="openThemePreview"]').on('click', function (e) {
                                e.preventDefault();
                                if (validateForm()) {
                                    jQuery('#theme_form').submit();
                                }
                            })

                            var is_submit = 0;

                            jQuery("#submit_theme_button").click(function () {
                                is_submit = 1;
                                jQuery("body").addClass("POST-uploading");
                                jQuery("#update_product_new").val(1);
                                jQuery('#theme_form').submit();
                            })

                            jQuery('[js-action="closeThemePreview"]').on('click', function (e) {
                                e.preventDefault();
                                $('body').removeClass('preview-active')
                                scrollPosition = $(document).scrollTop(scrollPosition);
                            })

                            jQuery('#theme_form').submit(function (e) {
                                if (!is_submit) {
                                    jQuery('[js-action="openThemePreview"]').addClass("loading");

                                    jQuery("#changelog").val(jQuery("#changelog_ifr").contents().find('body').html())
                                    jQuery("#test_tinymce").val(jQuery("#test_tinymce_ifr").contents().find('body').html())
                                    jQuery("#thumb_img_body").val(jQuery(".upload-image__cover__overlay").css("background-image"));
                                    e.preventDefault();

                                    jQuery.ajax({
                                        type: "POST",
                                        url: "<?php echo get_bloginfo("url"); ?>/my-account/vendor/preview-theme/",
                                        data: jQuery('#theme_form').serialize(),
                                        success: function (data) {
                                            //alert(data);
                                            jQuery('[js-action="openThemePreview"]').removeClass("loading");
                                            scrollPosition = $(document).scrollTop();
                                            jQuery("#themePreviewTarget").html(data);
                                            jQuery('body').addClass('preview-active');
                                        }
                                    })
                                }
                                // AJAX STUFF!
                            });
                        })

                    </script>
                    <div class="container container--xs">
                        <div class="dokan-new-product-area">
                            <img class="img-fluid mx-auto d-block mb-3" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/elements/bootstrap-logo.svg" alt="">
                            <h1 class="mb-1 text-center">Update your theme</h1>
                            <p class="fs-14 text-gray text-center mb-5">Please re-read our <a href="https://themes.zendesk.com/hc/en-us/articles/360001014312">"Before you build a theme"</a> article before submitting!</p>

                            <!-- To Mike: We should set `target="themePreviewTarget"` on this form so the returned HTML will appear in the iFrame! It's supported and will work without any extra code! -->
                            <form class="dokan-form-container" id="theme_form" method="post" enctype="multipart/form-data">
                                <div class="alert alert-danger" style="display:none; margin-bottom: 20px;"></div>

                                <div class="dokan-clearfix">
                                    <div class="content-half-part featured-image" style="display:none;">
                                        <div class="featured-image">
                                            <div class="dokan-feat-image-upload">
                                                <div class="instruction-inside <?php echo $hide_instruction ?>">
                                                    <input type="hidden" name="feat_image_id" class="dokan-feat-image-id" value="<?php echo $posted_img ?>">
                                                    <i class="fa fa-cloud-upload"></i>
                                                    <a href="#" class="dokan-feat-image-btn dokan-btn"><?php _e('Upload Product Image', 'dokan-lite'); ?></a>
                                                </div>

                                                <div class="image-wrap <?php echo $hide_img_wrap ?>">
                                                    <a class="close dokan-remove-feat-image">&times;</a>
                                                    <img src="<?php echo $posted_img_url ?>" alt="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="dokan-product-gallery">
                                            <div class="dokan-side-body" id="dokan-product-images">
                                                <div id="product_images_container">
                                                    <ul class="product_images dokan-clearfix">
                                                        <?php
                                                        if (isset($_POST['product_image_gallery'])) {
                                                            $product_images = $_POST['product_image_gallery'];
                                                            $gallery = explode(',', $product_images);

                                                            if ($gallery) {
                                                                foreach ($gallery as $image_id) {
                                                                    if (empty($image_id)) {
                                                                        continue;
                                                                    }

                                                                    $attachment_image = wp_get_attachment_image_src($image_id, 'thumbnail');
                                                                    ?>
                                                                    <li class="image" data-attachment_id="<?php echo $image_id; ?>">
                                                                        <img src="<?php echo $attachment_image[0]; ?>" alt="">
                                                                        <a href="#" class="action-delete" title="<?php esc_attr_e('Delete image', 'dokan-lite'); ?>">&times;</a>
                                                                    </li>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <li class="add-image add-product-images tips" data-title="<?php _e('Add gallery image', 'dokan-lite'); ?>">
                                                            <a href="#" class="add-product-images"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                        </li>
                                                    </ul>
                                                    <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="">
                                                </div>
                                            </div>
                                        </div> <!-- .product-gallery -->
                                    </div>
                                    <script>
                                        jQuery(document).ready(function () {
                                            jQuery("#update_theme_send").change(function () {
                                                if (jQuery(this).val() > 0) {
                                                    jQuery("#update_theme_message_div").show();
                                                    jQuery("#update_theme_message").addClass('required');
                                                } else {
                                                    jQuery("#update_theme_message_div").hide();
                                                    jQuery("#update_theme_message").removeClass('required');
                                                }
                                            });

                                            jQuery('#update_theme_send').change();

                                            jQuery("#post-title").on("keyup change", function () {
                                                var tex = jQuery(this).val();
                                                if (tex.length > 50) {
                                                    tex = tex.substring(0, 50);
                                                    jQuery(this).val(tex);
                                                }
                                                if (tex.length > 40) {
                                                    jQuery("#post-title_count").css("opacity", 1);
                                                }
                                                jQuery("#post-title_count").html(tex.length + "/50");
                                            });
                                            jQuery("#post-title").change();

        <?php if ($update_theme_send) { ?>
                                                jQuery("#send_notif").show();
        <?php } ?>
                                        });
                                    </script>
                                    <div class="">
                                        <div class="form-group">
                                            <label for="InputThemeName">Theme name</label>
                                            <span class="form-sublink" id="post-title_count" style="opacity: 0;">0/40</span>
                                            <input autocomplete="off" maxlength="50" class="required form-control" name="post_title" id="post-title" type="text" value="<?php echo $post_title; ?>">
                                            <div class="invalid-feedback"><?php _e("Please input a theme name"); ?></div>

                                        </div>
                                        <div class="form-group mb-4">
                                            <label for="InputCategory">Category</label>
                                            <?php
                                            $category_args = array(
                                                'show_option_none' => __('- Select a category -', 'dokan-lite'),
                                                'hierarchical' => 1,
                                                'hide_empty' => 0,
                                                'name' => 'product_cat',
                                                'id' => 'product_cat',
                                                'taxonomy' => 'product_cat',
                                                'title_li' => '',
                                                'class' => 'required product_cat form-control',
                                                'exclude' => '',
                                                'selected' => $product_cat,
                                            );

                                            wp_dropdown_categories(apply_filters('dokan_product_cat_dropdown_args', $category_args));
                                            ?>
                                            <div class="invalid-feedback"><?php _e("Please input a theme category"); ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="InputSinglePrice">Standard License price</label><small class="form-text" id="singlePrice">Price for a <a href="<?php echo get_bloginfo("url"); ?>/licenses#fullStandardLicense">Standard License</a>. At least $<?php echo get_option("min_reg_price"); ?> and we don't suggest going over $99.</small>
                                            <input autocomplete="off" min="<?php echo get_option("min_reg_price"); ?>" type="number" class="required fixed_price form-control" name="_regular_price" placeholder="0.00" value="<?php echo $reg_price; ?>" min="0" step="any">
                                            <div class="invalid-feedback"><?php _e("Please input a price for the theme"); ?></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="InputExtendedPrice">Extended License price</label><small class="form-text" id="extendedPrice">Price for an <a href="<?php echo get_bloginfo("url"); ?>/licenses#fullExtendedLicense">Extended License</a>. 10x - 40x the Standard License price is typical.</small>
                                            <input autocomplete="off" min="<?php echo get_option("min_ext_price"); ?>" type="number" class="required fixed_price form-control" name="_regular_price_extended" placeholder="0.00" value="<?php echo $ext_price; ?>" min="0" step="any">
                                            <div class="invalid-feedback"><?php _e("Please input an extended price for the theme"); ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="InputCompatability">Compatability</label><small class="form-text" id="compatabilityPrice">Which version of Bootstrap is your theme built on?</small>
                                            <select autocomplete="off" class="required form-control" id="bootstrap_ver" name="bootstrap_ver" aria-describedby="extendedPrice" >
                                                <?php
                                                $version_list = get_option("bootstrap_versions");
                                                foreach ($version_list as $ver) {
                                                    if ($ver == $bootstrap_ver) {
                                                        $ver_sel = 'selected="selected"';
                                                    } else {
                                                        $ver_sel = '';
                                                    }
                                                    echo '<option value="' . $ver . '" ' . $ver_sel . '>' . $ver . '</option>';
                                                }
                                                ?>

                                            </select>
                                            <div class="invalid-feedback"><?php _e("Please select the bootstrap version"); ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="InputThemeNumber">Your theme's version</label><small class="form-text" id="themeNumber">Your theme's current version number. Use 1.0 if you're just getting started.</small>
                                            <input autocomplete="off" maxlength="25" class="required form-control" id="theme_version" name="theme_version" type="text" aria-describedby="themeNumber" value="<?php echo $current_ver; ?>" placeholder="" />
                                            <div class="invalid-feedback"><?php _e("Please input the theme version"); ?></div>
                                        </div>

                                        <div class="form-group">
                                            <label for="InputDescription">Description</label><small class="form-text" id="DescriptionHelp">Describe your theme using rich text. Don't worry, you'll get to preview in the next step before submitting your theme.</small>
                                            <script>
                                                jQuery(document).ready(function () {
                                                    function activate_tiny(el_id) {
                                                        tinymce.init({
                                                            selector: '#' + el_id,
                                                            height: 150,
                                                            content_css: '<?php echo get_stylesheet_directory_uri(); ?>/add_theme.css',
                                                            object_resizing: false,
                                                            setup: function (ed) {
                                                                ed.onInit.add(function (ed) {
                                                                    $(ed.getBody()).find('img').resize(function (event) {

                                                                        $(event.target).css('width', parseInt(event.target.width * this.aspectratio));
                                                                    });
                                                                });
                                                            },
                                                            menubar: false,
                                                            plugins: [
                                                                'media lists hr paste link', // "image" is a valid option in here, but removing it for now to simplify things
                                                            ],
                                                            paste_as_text: true,
                                                            toolbar: 'bold italic link | styleselect hr | bullist numlist', // "image" goes between styleselect and hr if we ever re-include the plugin
                                                            paste_remove_spans: true,
                                                            image_dimensions: false,
                                                            style_formats: [
                                                                {title: 'Large Header', block: 'h2'},
                                                                {title: 'Small Header', block: 'h3'}
                                                            ],
                                                            valid_children: "+p[]",
                                                            inline_styles: true,
                                                            verify_html: true,
                                                            relative_urls: false,
                                                            remove_script_host: false,
                                                            convert_urls: true,
                                                            file_picker_types: 'image',
                                                            // and here's our custom image picker
                                                            file_picker_callback: function (cb, value, meta) {
                                                                var input = document.createElement('input');
                                                                input.setAttribute('type', 'file');
                                                                input.setAttribute('accept', 'image/*');

                                                                // Note: In modern browsers input[type="file"] is functional without 
                                                                // even adding it to the DOM, but that might not be the case in some older
                                                                // or quirky browsers like IE, so you might want to add it to the DOM
                                                                // just in case, and visually hide it. And do not forget do remove it
                                                                // once you do not need it anymore.

                                                                input.onchange = function () {
                                                                    var file = this.files[0];

                                                                    var reader = new FileReader();
                                                                    reader.onload = function () {
                                                                        // Note: Now we need to register the blob in TinyMCEs image blob
                                                                        // registry. In the next release this part hopefully won't be
                                                                        // necessary, as we are looking to handle it internally.
                                                                        var id = 'blobid' + (new Date()).getTime();
                                                                        var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                                                                        var base64 = reader.result.split(',')[1];
                                                                        var blobInfo = blobCache.create(id, file, base64);
                                                                        blobCache.add(blobInfo);

                                                                        // call the callback and populate the Title field with the file name
                                                                        cb(blobInfo.blobUri(), {title: file.name});
                                                                    };
                                                                    reader.readAsDataURL(file);
                                                                };

                                                                input.click();
                                                            }

                                                        });
                                                    }

                                                    activate_tiny("test_tinymce");
                                                    activate_tiny("changelog");


                                                    // Need to figure out how to reliably target the window, this ID changes
                                                    // jQuery(document).on('click','.mce-i-image', function() {
                                                    //   jQuery('#mceu_65-inp').prop('readonly', true).blur();
                                                    // })
                                                });

                                            </script>

                                            <textarea autocomplete="off" id="test_tinymce" name="post_content"><?php echo $post_content; ?></textarea>
                                            <div style="display:none;">
                                                <!-- To Mike: This query is taking like upwards of 16s to download on my super fast internet if there are any images in here...Even if the images are pretty small. Any idea what's going on here? -->
                                                <?php wp_editor($post_content, 'post_content22', array('editor_height' => 100, 'quicktags' => false, 'media_buttons' => true, 'teeny' => true, 'editor_class' => 'post_content222')); ?>
                                            </div>
                                            <div class="invalid-feedback"><?php _e("Please input the theme description"); ?></div>

                                        </div>
                                        <?php if ($post_status == "publish") { ?>
                                            <div class="form-group">
                                                <label for="changelog">Theme Changelog</label>
                                                <small class="form-text" id="themeNumber">Bullets or notes on the changes you've made. These will also be reviewed by the Bootstrap Team to direct us what to review more critically.</small>
                                                <textarea autocomplete="off" id="changelog" class="form-control" name="changelog"><?php echo $changelog; ?></textarea>

                                                <div class="invalid-feedback"><?php _e("Please input the changelog"); ?></div>
                                            </div>
                                        <?php } ?>
                                        <div class="form-group">
                                            <label for="InputExclusive">Is this theme a Bootstrap Theme exclusive?</label><small class="form-text" id="exclusiveHelp">Is this only being sold on this website? Remember, exclusives get better commissions!</small>
                                            <select autocomplete="off" class="form-control" id="exclusive" name="exclusive" aria-describedby="extendedPrice">
                                                <?php
                                                if ($exclusive_val) {
                                                    $exclusive_yes_sel = 'selected="selected"';
                                                    $exclusive_no_sel = '';
                                                } else {
                                                    $exclusive_yes_sel = '';
                                                    $exclusive_no_sel = 'selected="selected"';
                                                }
                                                ?>
                                                <option value = "1" <?= $exclusive_yes_sel; ?>>Yes, it's exclusive</option>
                                                <option value="0" <?= $exclusive_no_sel; ?>>No, I do/will sell this theme elsewhere</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="InputLiveURL">Live preview URL</label><small class="form-text" id="LiveURLHelp">Provide the URL to the live preview of your theme. The preview must be the current version of your uploaded theme. Your preview cannot remove our preview frame, contain advertisements, referral links, or a link to buy the theme elsewhere. We also discourage source obfuscation as it will adversely affect your sales.</small>
                                            <input autocomplete="off" class="required check_url form-control" id="preview_url" name="preview_url" type="text" aria-describedby="LiveURLHelp" value="<?php echo $preview_url; ?>" placeholder="" />
                                            <div class="invalid-feedback"><?php _e("Please provide a live preview URL"); ?></div>

                                        </div>
                                        <div class="form-group">
                                            <label for="">Upload a preview screenshot</label><small class="form-text" id="">Our suggested dimensions are 1200px * 900px. Anything larger will be cropped to 4:3 to fit our thumbnails/previews. Please use a clean screenshot – no additional marketing text or imagery is allowed.</small>
                                            <div class="upload-image">
                                                <?php
                                                $feat_image_url = wp_get_attachment_url($feat_image_id);
                                                ?>
                                                <div class="upload-image__item upload-image__cover-holder upload-image-placeholder upload-image__cover-holder--xl">
                                                    <div class="upload-image__cover__overlay" style="background-image:url(<?php echo $feat_image_url; ?>);">
                                                        <span class="btn btn-brand btn-sm btn-file d-none d-md-inline-block">Swap screenshot 
                                                            <input name="preview_screenshot" id="preview_screenshot" type="file">
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="invalid-feedback" id="cover_input_feedback"><?php _e("Please upload a screenshot"); ?></div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label for="">Upload your theme .zip</label><small class="form-text" id="">Maximum file size is 100MB. Please review our <a href="https://themes.zendesk.com/hc/en-us/articles/360000034531">help center guide on zip structure</a>.</small>
                                            <div class="d-flex align-items-center">
                                                <span class="btn btn-brand btn-file mr-3">
                                                    Upload .zip 
                                                    <input name="theme_file" accept=".zip" id="theme_file" type="file">
                                                </span>
                                                <?php
                                                if (is_wp_error($theme_file_id)) {
                                                    ?>
                                                    <span class="fs-12 text-gray-soft" id="theme_file_label">No file selected</span>
                                                    <?php
                                                } elseif ($theme_file_id) {
                                                    ?>
                                                    <span class="fs-12 text-gray-soft" id="theme_file_label"><a href="<?php echo wp_get_attachment_url($theme_file_id); ?>" target="_blank"><?php echo get_the_title($theme_file_id); ?></a></span>
                                                <?php } else { ?>
                                                    <span class="fs-12 text-gray-soft" id="theme_file_label">No file selected</span>
                                                <?php } ?>

                                            </div>
                                            <div class="invalid-feedback" id="theme_input_feedback"><?php _e("Please upload the theme file"); ?></div>

                                        </div>

                                        <?php if ($post_status == "publish") { ?>
                                          <div id="send_notif" style="display:none;">
                                              <hr class="my-5">
                                              <div class="form-group">
                                                  <label for="update_theme_send">Notify customers of this update?</label><small class="form-text">If you choose to notify customers, all previous purchasers of this theme will receive an email with a message from you and a link to download the update (once the update is approved)</small>
                                                  <select class="form-control" id="update_theme_send" name="update_theme_send">
                                                      <?php
                                                      if (!$update_theme_send) {
                                                          $update_theme_send = 0;
                                                      }
                                                      $update_theme_sends = array(0 => "No, don't notify customers", 1 => "Yes, please notify customers");
                                                      foreach ($update_theme_sends as $f => $v) {
                                                          if ($update_theme_send == $f) {
                                                              $sel = 'selected="selected"';
                                                          } else {
                                                              $sel = '';
                                                          }
                                                          ?>
                                                          <option value="<?php echo $f; ?>" <?php echo $sel; ?>><?php echo $v; ?></option>
                                                      <?php } ?>
                                                  </select>
                                                  <div class="invalid-feedback"><?php _e("Please select the notification"); ?></div>

                                              </div>
                                              <div class="form-group" id="update_theme_message_div" style="display:none;">
                                                  <label for="update_theme_message">Message to customers</label><small class="form-text">Your message should inform customers of the most important changes. Line breaks will be ignored, so keep your message simple.</small>
                                                  <textarea class="form-control" id="update_theme_message" name="update_theme_message"><?php echo $update_theme_message; ?></textarea>
                                                  <div class="invalid-feedback"><?php _e("Please provide a message to send to the theme customers"); ?></div>

                                              </div>
                                          </div>
                                        <?php } ?>

                                    </div>
                                </div>

                                <?php do_action('dokan_new_product_form'); ?>

                                <hr class="my-5">

                                <?php wp_nonce_field('dokan_add_new_product', 'dokan_add_new_product_nonce'); ?>
                                <input type="hidden" name="prod_id" value="<?php echo $post_id; ?>" />

                                <button type="submit" js-action="openThemePreview" class="btn btn-brand btn-block btn-lg mb-4">
                                    <i class="loading-icon bootstrap-themes-icon-spin6"></i>
                                    Preview the update
                                </button>
                                <!-- To Mike: ^ This button had a "value='create_new22'", but wasn't sure what that was? Removed it for now...  -->
                                <input type="hidden" id="thumb_img_body" name="thumb_img_body" value="<?php echo $feat_image_url; ?>" />
                                <input type="hidden" id="update_product_new" name="update_product_new" value="0" />

                            </form>
                        </div>
                    </div>

                    <!-- Uploading state for when the body has the .POST-upload class -->
                    <div class="theme-preview__loading justify-content-center align-items-center">
                        <div>
                            <i class="loading-icon bootstrap-themes-icon-spin6 mb-2"></i>
                            <h5 class="mb-0">Uploading Theme</h5>
                            <p class="text-gray-soft">Please don't leave or refresh!</p>
                        </div>
                    </div>

                    <!-- To Mike: These are dummy variables, but they show you what alerts/button labels we should show based on different cases -->
                    <?php
                    $hasPendingChanges = get_post_meta($post_id, "new_info", true);
                    $post_status = get_post($post_id)->post_status;
                    ?>

                    <!-- The theme preview -->
                    <div class="theme-preview">

                        <!-- To Mike: These are the conditions for different alerts. Change the variables above to see the different states -->
                        <div class="theme-preview__alert-wrapper">
                            <?php if ($hasPendingChanges || $post_status == "pending") { ?>
                                <div class="alert alert-warning text-center">This theme has unreviewed updates. Submitting changes will place this theme at the end of our review queue.</div>
                            <?php } else { ?>
                                <div style="display:none;" class="alert alert-warning has_zip_change text-center">Updating your theme’s .zip requires review before your changes will be published.</div>
                                <div class="alert alert-success no_zip_change text-center">Since you’re not updating your theme’s .zip, your changes will be published instantly.</div>
                            <?php } ?>
                        </div>

                        <div id="themePreviewTarget" name="themePreviewTarget" class="iframe-preview iframe-preview--product-preview"></div>
                        <nav class="navbar navbar-preview fixed-top">
                            <div class="container-fluid d-flex justify-content-between align-items-center w-100">
                                <a class="my-lg-0 text-gray-soft" js-action="closeThemePreview" href="#"><i class="bootstrap-themes-icon-left-open-1"></i> Back to edit</a>
                                <div class="form-inline">
                                    <?php
                                    if (!$hasPendingChanges && $post_status == "publish") {
                                        $submit_label = "Update instantly";
                                    } else {
                                        $submit_label = "Submit for review";
                                    }
                                    ?>
                                    <button type="button" class="btn btn-brand" id="submit_theme_button"><?php echo $submit_label; ?></button>
                                    <input type="hidden" name="publish_theme" value="<?php echo $_GET["prod_id"]; ?>" />
                                </div>
                            </div>
                        </nav>

                    </div>


                <?php } else { ?>
                    <div class="dokan-alert dokan-alert">
                        <?php echo dokan_seller_not_enabled_notice(); ?>
                    </div>
                <?php } ?>

            <?php } else { ?>

                <?php do_action('dokan_can_post_notice'); ?>

            <?php } ?>
        </div> <!-- #primary .content-area -->

        <?php
        /**
         *  dokan_product_content_inside_area_after hook
         *
         *  @since 2.4
         */
        do_action('dokan_product_content_inside_area_after');
        ?>
    </div>

    <?php
    /**
     *  dokan_dashboard_content_after hook
     *  dokan_after_product_content_area hook
     *
     *  @since 2.4
     */
    do_action('dokan_dashboard_content_after');
    do_action('dokan_after_product_content_area');
    ?>

</div><!-- .dokan-dashboard-wrap -->
<div class="dokan-clearfix"></div>

<?php
/**
 *  dokan_dashboard_content_before hook
 *
 *  @since 2.4
 */
do_action('dokan_dashboard_wrap_after', $post, $post_id);

wp_reset_postdata();

get_footer();
?>

