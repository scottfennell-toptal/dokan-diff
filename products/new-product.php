<?php
/**
 *  dokan_new_product_wrap_before hook
 *
 *  @since 2.4
 */
do_action('dokan_new_product_wrap_before');
?>
<style>
    #vendor_menu{
        display: none !important;
    }
    #signup_footer{
        display: none !important;
    }
</style>
</div>

<div class="dokan-dashboard-wrap">

    <?php
    /**
     *  dokan_dashboard_content_before hook
     *  dokan_before_new_product_content_area hook
     *
     *  @hooked get_dashboard_side_navigation
     *
     *  @since 2.4
     */
    do_action('dokan_dashboard_content_before');
    do_action('dokan_before_new_product_content_area');
    ?>


    <div class="dokan-dashboard-content">

        <?php
        /**
         *  dokan_before_new_product_inside_content_area hook
         *
         *  @since 2.4
         */
        do_action('dokan_before_new_product_inside_content_area');
        ?>


        <div class="dokan-new-product-area">
            <?php if (Dokan_Template_Products::$errors) { ?>
                <div class="dokan-alert dokan-alert-danger">
                    <a class="dokan-close" data-dismiss="alert">&times;</a>

                    <?php foreach (Dokan_Template_Products::$errors as $error) { ?>

                        <strong><?php _e('Error!', 'dokan-lite'); ?></strong> <?php echo $error ?>.<br>

                    <?php } ?>
                </div>
            <?php } ?>

            <?php if (isset($_GET['created_product'])): ?>
                <div class="dokan-alert dokan-alert-success">
                    <a class="dokan-close" data-dismiss="alert">&times;</a>
                    <strong><?php _e('Success!', 'dokan-lite'); ?></strong>
                    <?php echo sprintf(__('You have successfully created <a href="%s"><strong>%s</strong></a> product', 'dokan-lite'), dokan_edit_product_url(intval($_GET['created_product'])), get_the_title(intval($_GET['created_product']))); ?>
                </div>
            <?php endif ?>

            <?php
            $can_sell = apply_filters('dokan_can_post', true);

            if ($can_sell) {
                $posted_img = dokan_posted_input('feat_image_id');
                $posted_img_url = $hide_instruction = '';
                $hide_img_wrap = 'dokan-hide';

                if (!empty($posted_img)) {
                    $posted_img = empty($posted_img) ? 0 : $posted_img;
                    $posted_img_url = wp_get_attachment_url($posted_img);
                    $hide_instruction = 'dokan-hide';
                    $hide_img_wrap = '';
                }
                if (dokan_is_seller_enabled(get_current_user_id())) {
                    ?>
                    <script>
                        function readURL(input, target) {
                            if (input.files && input.files[0]) {
                                var reader = new FileReader();
                                reader.onload = function (e) {
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
                                console.log(input.files[0].name);
                                $(target).html(input.files[0].name);
                                $(target).attr("data-size", Math.round(parseInt(input.files[0].size) / 1000));
                            }
                        }

                        jQuery(document).ready(function () {
                            jQuery("#preview_screenshot").change(function () {
                                readURL(this, '.upload-image__cover__overlay');
                            });
                            jQuery("#theme_file").change(function () {
                                var ext = this.value.substr(this.value.length - 3);
                                switch (ext) {
                                    case 'zip':
                                        readName(this, '#theme_file_label');
                                        jQuery("#theme_input_feedback").hide();
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
                                jQuery("#add_product_new").val(1);
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
                                    jQuery("#test_tinymce").val(jQuery("#test_tinymce_ifr").contents().find('body').html());
                                    jQuery("#thumb_img_body").val(jQuery(".upload-image__cover__overlay").css("background-image"));

                                    e.preventDefault();

                                    jQuery.ajax({
                                        type: "POST",
                                        url: "<?php echo get_bloginfo("url"); ?>/my-account/vendor/preview-theme/",
                                        data: jQuery('#theme_form').serialize(),
                                        success: function (data) {
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
                        <img class="img-fluid mx-auto d-block mb-3" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/elements/bootstrap-logo.svg" alt="">
                        <h1 class="mb-1 text-center">Submit a new theme</h1>
                        <p class="fs-14 text-gray text-center mb-5">Please re-read our <a href="https://themes.zendesk.com/hc/en-us/articles/360001014312">"Before you build a theme"</a> article before submitting!</p>

                        <!-- To Mike: We should set `target="themePreviewTarget"` on this form so the returned HTML will appear in the iFrame! It's supported and will work without any extra code! -->
                        <form autocomplete="off" class="dokan-form-container" id="theme_form" method="post" enctype="multipart/form-data">
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

                                    });
                                </script>
                                <div class="">
                                    <div class="form-group">
                                        <label for="InputThemeName">Theme name</label>
                                        <span class="form-sublink" id="post-title_count" style="opacity: 0;">0/40</span>
                                        <input autocomplete="off" maxlength="50" class="required form-control" name="post_title" id="post-title" type="text" value="<?php echo dokan_posted_input('post_title'); ?>">
                                        <div class="invalid-feedback"><?php _e("Please input a theme name"); ?></div>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="InputCategory">Category</label>
                                        <?php
                                        $selected_cat = dokan_posted_input('product_cat');
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
                                            'selected' => $selected_cat,
                                        );

                                        wp_dropdown_categories(apply_filters('dokan_product_cat_dropdown_args', $category_args));
                                        ?>
                                        <div class="invalid-feedback"><?php _e("Please input a theme category"); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="InputSinglePrice">Standard License price</label><small class="form-text" id="singlePrice">Price for a <a href="<?php echo get_bloginfo("url"); ?>/licenses#fullStandardLicense">Standard License</a>. At least $<?php echo get_option("min_reg_price"); ?> and we don't suggest going over $99.</small>
                                        <input autocomplete="off" type="number" min="<?php echo get_option("min_reg_price"); ?>" class="required fixed_price form-control" name="_regular_price" placeholder="0.00" value="<?php echo dokan_posted_input('_regular_price') ?>" min="0" step="any">
                                        <div class="invalid-feedback"><?php _e("Please input a price for the theme"); ?></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="InputExtendedPrice">Extended Use price</label><small class="form-text" id="extendedPrice">Price for an <a href="<?php echo get_bloginfo("url"); ?>/licenses#fullExtendedLicense">Extended License</a>. 10x - 40x the Standard License price is typical.</small>
                                        <input autocomplete="off" type="number" min="<?php echo get_option("min_ext_price"); ?>" class="fixed_price required form-control" name="_regular_price_extended" placeholder="0.00" value="<?php echo dokan_posted_input('_regular_price_extended') ?>" min="0" step="any">
                                        <div class="invalid-feedback"><?php _e("Please input an extended price for the theme"); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="InputCompatability">Compatability</label><small class="form-text" id="compatabilityPrice">Which version of Bootstrap is your theme built on?</small>
                                        <select autocomplete="off" class="required form-control" id="bootstrap_ver" name="bootstrap_ver" aria-describedby="extendedPrice">
                                            <?php
                                            $version_list = get_option("bootstrap_versions");
                                            foreach ($version_list as $ver) {
                                                if ($ver == dokan_posted_input('bootstrap_ver')) {
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
                                        <input autocomplete="off" maxlength="25" class="required form-control" id="theme_version" name="theme_version" type="text" aria-describedby="themeNumber" placeholder="">
                                        <div class="invalid-feedback"><?php _e("Please input the theme version"); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="InputDescription">Description</label><small class="form-text" id="DescriptionHelp">Describe your theme using rich text. Don't worry, you'll get to preview in the next step before submitting your theme.</small>
                                        <script>
                                            jQuery(document).ready(function () {
                                                tinymce.init({
                                                    selector: '#test_tinymce',
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
                                                // Need to figure out how to reliably target the window, this ID changes
                                                // jQuery(document).on('click','.mce-i-image', function() {
                                                //   jQuery('#mceu_65-inp').prop('readonly', true).blur();
                                                // })
                                            });

                                        </script>
                                        <textarea autocomplete="off" id="test_tinymce" name="post_content"><?php echo $post_content; ?></textarea>
                                        <div style="display:none;">
                                            <?php wp_editor($post_content, 'post_content22', array('editor_height' => 100, 'quicktags' => false, 'media_buttons' => true, 'teeny' => true, 'editor_class' => 'post_content222')); ?>
                                        </div>
                                        <div class="invalid-feedback"><?php _e("Please input the theme description"); ?></div>


                                    </div>
                                    <div class="form-group">
                                        <label for="InputExclusive">Is this theme a Bootstrap Theme exclusive?</label><small class="form-text" id="exclusiveHelp">Is this only being sold on this website? Remember, exclusives get better commissions!</small>
                                        <select autocomplete="off" class="form-control" id="exclusive" name="exclusive" aria-describedby="extendedPrice">
                                            <option value="1">Yes, it's exclusive</option>
                                            <option value="0">No, I do/will sell this theme elsewhere</option>
                                        </select>

                                    </div>
                                    <div class="form-group">
                                        <label for="InputLiveURL">Live preview URL</label><small class="form-text" id="LiveURLHelp">Provide the URL to the live preview of your theme. The preview must be the current version of your uploaded theme. Your preview cannot remove our preview frame, contain advertisements, referral links, or a link to buy the theme elsewhere. We also discourage source obfuscation as it will adversely affect your sales.</small>
                                        <input autocomplete="off" class="required check_url form-control" id="preview_url" name="preview_url" type="text" aria-describedby="LiveURLHelp" placeholder="">
                                        <div class="invalid-feedback"><?php _e("Please provide a live preview URL"); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Upload a preview screenshot</label><small class="form-text" id="">Our suggested dimensions are 1200px * 900px. Anything larger will be cropped to 4:3 to fit our thumbnails/previews. Please use a clean screenshot – no additional marketing text or imagery is allowed.</small>
                                        <div class="upload-image">
                                            <div class="upload-image__item upload-image__cover-holder upload-image-placeholder upload-image__cover-holder--xl">
                                                <div class="upload-image__cover__overlay"><span class="btn btn-brand btn-sm btn-file d-none d-md-inline-block">Upload screenshot 
                                                        <input name="preview_screenshot" id="preview_screenshot" type="file"></span></div>
                                            </div>

                                        </div>
                                        <div class="invalid-feedback" id="cover_input_feedback"><?php _e("Please upload a screenshot"); ?></div>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label for="">Upload your theme .zip</label><small class="form-text" id="">Maximum file size is 125MB. Please review our <a href="https://themes.zendesk.com/hc/en-us/articles/360000034531">help center guide on zip structure</a>.</small>
                                        <div class="d-flex align-items-center"><span class="btn btn-brand btn-file mr-3">Upload .zip 
                                                <input name="theme_file" accept=".zip" id="theme_file" type="file"></span><span class="fs-12 text-gray-soft" id="theme_file_label">No file selected</span></div>
                                        <div class="invalid-feedback" id="theme_input_feedback"><?php _e("Please upload a valid zip theme file"); ?></div>
                                    </div>

                                </div>
                            </div>

                            <?php do_action('dokan_new_product_form'); ?>

                            <hr class="my-5">

                            <?php wp_nonce_field('dokan_add_new_product', 'dokan_add_new_product_nonce'); ?>
                            <button type="submit" js-action="openThemePreview" class="btn btn-brand btn-block btn-lg mb-4">
                                <i class="loading-icon bootstrap-themes-icon-spin6"></i>
                                Preview Theme
                            </button>
                            <!-- To Mike: ^ This button had a "value='create_new22'", but wasn't sure what that was? Removed it for now...  -->
                            <input type="hidden" id="thumb_img_body" name="thumb_img_body" value="<?php echo $feat_image_url; ?>" />
                            <input type="hidden" id="add_product_new" name="add_product_new" value="0" />

                        </form>
                    </div>

                    <!-- Uploading state for when the body has the .POST-upload class -->
                    <div class="theme-preview__loading justify-content-center align-items-center">
                        <div>
                            <i class="loading-icon bootstrap-themes-icon-spin6 mb-2"></i>
                            <h5 class="mb-0">Uploading Theme</h5>
                            <p class="text-gray-soft">Please don't leave or refresh!</p>
                        </div>
                    </div>

                    <!-- The theme preview -->
                    <div class="theme-preview">
                        <div id="themePreviewTarget" name="themePreviewTarget" class="iframe-preview iframe-preview--product-preview"></div>
                        <nav class="navbar navbar-preview fixed-top">
                            <div class="container-fluid d-flex justify-content-between align-items-center w-100">
                                <a class="my-lg-0 text-gray-soft" js-action="closeThemePreview" href="#"><i class="bootstrap-themes-icon-left-open-1"></i> Back to edit</a>
                                <div class="form-inline">
                                    <button type="button" id="submit_theme_button" class="btn btn-brand">Submit for review</button>
                                    <input type="hidden" name="publish_theme" value="<?php echo $_GET["prod_id"]; ?>" />
                                </div>
                            </div>
                        </nav>
                    </div>





                <?php } else { ?>

                    <?php dokan_seller_not_enabled_notice(); ?>

                <?php } ?>

            <?php } else { ?>

                <?php do_action('dokan_can_post_notice'); ?>

            <?php } ?>
        </div>

        <?php
        /**
         *  dokan_after_new_product_inside_content_area hook
         *
         *  @since 2.4
         */
        do_action('dokan_after_new_product_inside_content_area');
        ?>

    </div> <!-- #primary .content-area -->

    <?php
    /**
     *  dokan_dashboard_content_after hook
     *  dokan_after_new_product_content_area hook
     *
     *  @since 2.4
     */
    do_action('dokan_dashboard_content_after');
    do_action('dokan_after_new_product_content_area');
    ?>

</div><!-- .dokan-dashboard-wrap -->

<?php
/**
 *  dokan_new_product_wrap_after hook
 *
 *  @since 2.4
 */
do_action('dokan_new_product_wrap_after');
?>
