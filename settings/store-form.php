<?php
/**
 * Dokan Dashboard Settings Store Form Template
 *
 * @since 2.4
 */
$profile_info = get_user_meta(get_current_user_id(), "dokan_profile_settings", true);

$gravatar = isset($profile_info['gravatar']) ? absint($profile_info['gravatar']) : 0;
$banner = isset($profile_info['banner']) ? absint($profile_info['banner']) : 0;
$storename = isset($profile_info['store_name']) ? esc_attr($profile_info['store_name']) : '';
$support_link = isset($profile_info['support_link']) ? esc_attr($profile_info['support_link']) : '';
$public_support_link = isset($profile_info['public_support_link']) ? esc_attr($profile_info['public_support_link']) : '';

$store_body = isset($profile_info['store_description']) ? $profile_info['store_description'] : '';

$storename = str_replace("&amp;", "&", $storename);
$store_body = str_replace("&amp;", "&", $store_body);

//
//if (is_wp_error($validate)) {
//    $storename = $_POST['dokan_store_name'];
//    $map_location = $_POST['location'];
//    $map_address = $_POST['find_address'];
//
//    $address_street1 = $_POST['dokan_address']['street_1'];
//    $address_street2 = $_POST['dokan_address']['street_2'];
//    $address_city = $_POST['dokan_address']['city'];
//    $address_zip = $_POST['dokan_address']['zip'];
//    $address_country = $_POST['dokan_address']['country'];
//    $address_state = $_POST['dokan_address']['state'];
//}
//
//$dokan_appearance = dokan_get_option('store_header_template', 'dokan_appearance', 'default');


do_action('dokan_settings_before_form', $current_user, $profile_info);
?>

<form method="post" id="store_form2" enctype="multipart/form-data" action="" class="dokan-form-horizontal">
    <div class="alert alert-danger" style="display:none; margin-bottom: 20px;"></div>

    <script>
        jQuery(document).ready(function () {
            jQuery("#dokan_store_name").keyup(function () {
                jQuery("#store_name_label").html(jQuery(this).val());
            });
            jQuery("#dokan_store_body").on("keyup change", function () {
                var tex = jQuery(this).val();
                if (tex.length > 120) {
                    tex = tex.substring(0, 120);
                    jQuery(this).val(tex);
                }
                jQuery("#dokan_store_body_count").html(tex.length + "/120");
                jQuery("#store_description_label").html(tex);
            });
            jQuery("#dokan_store_body").change();

            jQuery("#dokan_store_name").on("keyup change", function () {
                var tex = jQuery(this).val();
                if (tex.length > 40) {
                    tex = tex.substring(0, 40);
                    jQuery(this).val(tex);
                }
                if (tex.length > 30) {
                    jQuery("#dokan_store_name_count").css("opacity", 1);
                }
                jQuery("#dokan_store_name_count").html(tex.length + "/40");
            });
            jQuery("#dokan_store_name").change();

            jQuery("#store_form2").submit(function (e) {
                var er = validate_form(jQuery("#store_form2"));
                if (er) {
                    e.stopPropagation();
                    e.preventDefault();
                    return false;
                }
            });


        });

    </script>
    <?php wp_nonce_field('dokan_store_settings_nonce'); ?>
    <?php
    if (is_page("upgrade")) {
        ?>
        <div class="row mt-5" style="text-align:left;">
            <div class="col-lg-12 mb-3 mb-md-0">
                <div class="form-group hide_span">
                    <label  for="dokan_store_name"><?php _e('Store Name', 'dokan-lite'); ?></label>
                    <span class="form-sublink" id="dokan_store_name_count" style="opacity: 0;">0/40</span>
                    <input id="dokan_store_name" maxlength="40" value="<?php echo $storename; ?>" name="dokan_store_name" placeholder="" class="required form-control" type="text">
                    <div class="invalid-feedback"><?php _e("Please enter a valid Store Name"); ?></div>

                </div>
                <div class="form-group hide_span">
                    <label for="dokan_store_body"><?php _e('Store bio', 'dokan-lite'); ?></label>
                    <span class="form-sublink" id="dokan_store_body_count">0/120</span>
                    <textarea id="dokan_store_body" value="<?php echo $store_body; ?>" name="dokan_store_description" class="required form-control"><?php echo $store_body; ?></textarea>
                    <div class="invalid-feedback"><?php _e("Please enter a company bio"); ?></div>
                </div>
                <!--address-->
                <div class="form-group hide_span">
                    <label  for="setting_phone"><?php _e('Support link', 'dokan-lite'); ?></label>
                    <small class="form-text" id="emailSupport">Where customers will contact you for support. Can be a link to a support site (i.e. https://themes.zendesk.com/hc/en-us) or a support email address mailto link (i.e. mailto:themes@getbootstrap.com).</small>
                    <input id="dokan_support_link" value="<?php echo $support_link; ?>" name="dokan_support_link" class="required check_url form-control input-md" type="text">
                    <div class="invalid-feedback"><?php _e("Please provide a valid support URL or mailto: address"); ?></div>

                </div>
                <div class="form-group hide_span">
                    <label  for="setting_phone"><?php _e('Public contact link', 'dokan-lite'); ?></label>
                    <small class="form-text" id="emailPublicSupport">(Optional) Where customers will contact you for pre-sale questions. Can be a link to a support site (http://...) or a support email address mailto link (mailto:name@domain.com).</small>
                    <input id="dokan_public_support_link" value="<?php echo $public_support_link; ?>" name="dokan_public_support_link" class=" check_url form-control input-md" type="text">
                    <div class="invalid-feedback"><?php _e("Please provide a valid support URL or mailto: address"); ?></div>

                </div>

            </div>
            <div class="col-lg-12 d-none d-lg-block">
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

                    jQuery(document).ready(function () {
                        jQuery("#upload_avatar").change(function () {
                            readURL(this, '.upload-image__avatar');
                        });
                        jQuery("#upload_cover").change(function () {
                            readURL(this, '.upload-image__cover-holder .upload-image__cover__overlay');
                        });
                    })

                </script>
                <?php $gravatar_url = $gravatar ? wp_get_attachment_url($gravatar) : ''; ?>
                <?php $banner_url = $banner ? wp_get_attachment_url($banner) : ''; ?>
                <div class="form-group">
                    <label for="InputSupportEmail">Upload avatar and cover images</label>
                    <div class="form-text mb-3">Cover images should be a be at least 1200px * 375px. Avatars should be square and at least 300px * 300px.</div>
                    <div class="upload-image">
                        <div class="upload-image__item upload-image__cover-holder upload-image-placeholder">
                            <div class="upload-image__cover__overlay" >
                                <span class="btn btn-brand btn-sm btn-file d-none d-md-block">
                                    Upload cover 
                                    <input id="upload_cover" name="upload_cover"  type="file">
                                </span>
                            </div>
                        </div>
                        <div class="upload-image__item upload-image__avatar-holder">
                            <div class="d-flex align-items-center mb-3 mb-md-0">
                                <div class="upload-image__avatar-inner">
                                    <div class="upload-image__avatar upload-image-placeholder" >
                                        <span class="btn btn-brand btn-sm btn-block btn-file d-none d-md-block">
                                            Upload avatar 
                                            <input id="upload_avatar" name="upload_avatar" type="file">
                                        </span>
                                    </div>
                                </div>
                                <div class="upload-image__description">
                                    <h4 id="store_name_label"><?php echo $storename; ?></h4>
                                    <p class="d-none d-md-block" id="store_description_label"><?php echo $store_body; ?></p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="invalid-feedback" id="cover_input_feedback"><?php _e("Please upload a cover"); ?></div>
                    <div class="invalid-feedback" id="avatar_input_feedback"><?php _e("Please upload an avatar"); ?></div>

                </div>
            </div>

            <?php do_action('dokan_settings_form_bottom', $current_user, $profile_info); ?>
            <div class="col-lg-12 d-none d-lg-block">
                <div class="form-group">

                    <div class="ajax_prev dokan-text-left" style="width:100%; clear: both; padding-top: 20px;">
                        <input type="submit" name="dokan_upgrade_store_settings" class="btn btn-brand btn-block btn-lg mb-4" value="<?php esc_attr_e('Sign up', 'dokan-lite'); ?>">
                    </div>
                </div>
            </div>
        </div>

        <?php
    } else {
        ?>
        <div class="row mt-5" style="text-align:left;">
            <div class="col-lg-6 mb-3 mb-md-0">
                <div class="form-group hide_span">
                    <label  for="dokan_store_name"><?php _e('Store Name', 'dokan-lite'); ?></label>
                    <span class="form-sublink" id="dokan_store_name_count" style="opacity: 0;">0/40</span>
                    <small class="form-text" id="emailSupport">Please Note: changing your store name will change your store URL </small>
                    <input id="dokan_store_name" maxlength="40" value="<?php echo $storename; ?>" name="dokan_store_name" placeholder="" class="required form-control" type="text">
                    <div class="invalid-feedback"><?php _e("Please enter a valid Store Name"); ?></div>
                </div>
                <div class="form-group hide_span">
                    <label for="dokan_store_body"><?php _e('Store bio', 'dokan-lite'); ?></label>
                    <span class="form-sublink" id="dokan_store_body_count">0/120</span>
                    <textarea id="dokan_store_body" value="<?php echo $store_body; ?>" name="dokan_store_description" class="required form-control"><?php echo $store_body; ?></textarea>
                    <div class="invalid-feedback"><?php _e("Please enter a company bio"); ?></div>
                </div>
                <!--address-->
                <div class="form-group hide_span">
                    <label  for="setting_phone"><?php _e('Support link', 'dokan-lite'); ?></label>
                    <small class="form-text" id="emailSupport">Where customers will contact you for support. Can be a link to a support site (i.e. https://themes.zendesk.com/hc/en-us) or a support email address mailto link (i.e. mailto:themes@getbootstrap.com).</small>
                    <input id="dokan_support_link" value="<?php echo $support_link; ?>" name="dokan_support_link" class="required check_url form-control input-md" type="text">
                    <div class="invalid-feedback"><?php _e("Please provide a valid support URL or mailto: address"); ?></div>

                </div>

                <div class="form-group hide_span">
                    <label  for="setting_phone"><?php _e('Public contact link', 'dokan-lite'); ?></label>
                    <small class="form-text" id="emailPublicSupport">(Optional) Where customers will contact you for pre-sale questions. Can be a link to a support site (http://...) or a support email address mailto link (mailto:name@domain.com).</small>
                    <input id="dokan_public_support_link" value="<?php echo $public_support_link; ?>" name="dokan_public_support_link" class=" check_url form-control input-md" type="text">
                    <div class="invalid-feedback"><?php _e("Please provide a valid support URL or mailto: address"); ?></div>

                </div>


                <?php do_action('dokan_settings_form_bottom', $current_user, $profile_info); ?>

            </div>
            <div class="col-lg-6 d-none d-lg-block">
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

                    jQuery(document).ready(function () {
                        jQuery("#upload_avatar").change(function () {
                            readURL(this, '.upload-image__avatar');
                        });
                        jQuery("#upload_cover").change(function () {
                            readURL(this, '.upload-image__cover-holder .upload-image__cover__overlay');
                        });
                    })

                </script>
                <?php $gravatar_url = $gravatar ? wp_get_attachment_url($gravatar) : ''; ?>
                <?php $banner_url = $banner ? wp_get_attachment_url($banner) : ''; ?>
                <div class="form-group">
                    <label for="InputSupportEmail">Upload avatar and cover images</label>
                    <div class="form-text mb-3">Cover images should be a be at least 1200px * 375px. Avatars should be square and at least 300px * 300px.</div>
                    <div class="upload-image">
                        <div class="upload-image__item upload-image__cover-holder upload-image-placeholder">
                            <div class="upload-image__cover__overlay" style="background-image:url(<?php echo $banner_url; ?>);">
                                <span class="btn btn-brand btn-sm btn-file d-none d-md-block">
                                    Swap cover 
                                    <input id="upload_cover" name="upload_cover"  type="file">
                                </span>
                            </div>
                        </div>
                        <div class="upload-image__item upload-image__avatar-holder">
                            <div class="d-flex align-items-center mb-3 mb-md-0">
                                <div class="upload-image__avatar-inner">
                                    <div class="upload-image__avatar upload-image-placeholder" style="background-image:url(<?php echo $gravatar_url; ?>);">
                                        <span class="btn btn-brand btn-sm btn-block btn-file d-none d-md-block">
                                            Swap avatar 
                                            <input id="upload_avatar" name="upload_avatar" type="file">
                                        </span>
                                    </div>
                                </div>
                                <div class="upload-image__description">
                                    <h4 id="store_name_label"><?php echo $storename; ?></h4>
                                    <p class="d-none d-md-block" id="store_description_label"><?php echo $store_body; ?></p>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="invalid-feedback" id="cover_input_feedback"><?php _e("Please upload a cover"); ?></div>
                    <div class="invalid-feedback" id="avatar_input_feedback"><?php _e("Please upload an avatar"); ?></div>

                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="ajax_prev dokan-text-left" style="width:100%;">
                <input type="submit" name="dokan_update_store_settings" class="btn btn-brand btn-block btn-lg" value="<?php esc_attr_e('Save changes', 'dokan-lite'); ?>">
            </div>
        </div>
        <?php
    }
    ?>




</form>

<?php do_action('dokan_settings_after_form', $current_user, $profile_info); ?>

<style>
    .dokan-settings-content .dokan-settings-area .dokan-banner {
        width: <?php echo $banner_width . 'px'; ?>;
        height: <?php echo $banner_height . 'px'; ?>;
    }

    .dokan-settings-content .dokan-settings-area .dokan-banner .dokan-remove-banner-image {
        height: <?php echo $banner_height . 'px'; ?>;
    }
</style>
<script type="text/javascript">

            (function ($) {
                var dokan_address_wrapper = $('.dokan-address-fields');
                var dokan_address_select = {
                    init: function () {

                        dokan_address_wrapper.on('change', 'select.country_to_state', this.state_select);
                    },
                    state_select: function () {
                        var states_json = wc_country_select_params.countries.replace(/&quot;/g, '"'),
                                states = $.parseJSON(states_json),
                                $statebox = $('#dokan_address_state'),
                                input_name = $statebox.attr('name'),
                                input_id = $statebox.attr('id'),
                                input_class = $statebox.attr('class'),
                                value = $statebox.val(),
                                selected_state = '<?php echo $address_state; ?>',
                                input_selected_state = '<?php echo $address_state; ?>',
                                country = $(this).val();

                        if (states[ country ]) {

                            if ($.isEmptyObject(states[ country ])) {

                                $('div#dokan-states-box').slideUp(2);
                                if ($statebox.is('select')) {
                                    $('select#dokan_address_state').replaceWith('<input type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required />');
                                }

                                $('#dokan_address_state').val('N/A');

                            } else {
                                input_selected_state = '';

                                var options = '',
                                        state = states[ country ];

                                for (var index in state) {
                                    if (state.hasOwnProperty(index)) {
                                        if (selected_state) {
                                            if (selected_state == index) {
                                                var selected_value = 'selected="selected"';
                                            } else {
                                                var selected_value = '';
                                            }
                                        }
                                        options = options + '<option value="' + index + '"' + selected_value + '>' + state[ index ] + '</option>';
                                    }
                                }

                                if ($statebox.is('select')) {
                                    $('select#dokan_address_state').html('<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options);
                                }
                                if ($statebox.is('input')) {
                                    $('input#dokan_address_state').replaceWith('<select type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required ></select>');
                                    $('select#dokan_address_state').html('<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options);
                                }
                                $('#dokan_address_state').removeClass('dokan-hide');
                                $('div#dokan-states-box').slideDown();

                            }
                        } else {


                            if ($statebox.is('select')) {
                                input_selected_state = '';
                                $('select#dokan_address_state').replaceWith('<input type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required="required"/>');
                            }
                            $('#dokan_address_state').val(input_selected_state);

                            if ($('#dokan_address_state').val() == 'N/A') {
                                $('#dokan_address_state').val('');
                            }
                            $('#dokan_address_state').removeClass('dokan-hide');
                            $('div#dokan-states-box').slideDown();
                        }
                    }
                }

                $(function () {
                    dokan_address_select.init();

                    $('#setting_phone').keydown(function (e) {
                        // Allow: backspace, delete, tab, escape, enter and .
                        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 91, 107, 109, 110, 187, 189, 190]) !== -1 ||
                                // Allow: Ctrl+A
                                        (e.keyCode == 65 && e.ctrlKey === true) ||
                                        // Allow: home, end, left, right
                                                (e.keyCode >= 35 && e.keyCode <= 39)) {
                                    // let it happen, don't do anything
                                    return;
                                }

                                // Ensure that it is a number and stop the keypress
                                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                                    e.preventDefault();
                                }
                            });
<?php
$locations = explode(',', $map_location);
$def_lat = isset($locations[0]) ? $locations[0] : 90.40714300000002;
$def_long = isset($locations[1]) ? $locations[1] : 23.709921;
?>
                    var def_zoomval = 12;
                    var def_longval = '<?php echo $def_long; ?>';
                    var def_latval = '<?php echo $def_lat; ?>';

                    try {
                        var curpoint = new google.maps.LatLng(def_latval, def_longval),
                                geocoder = new window.google.maps.Geocoder(),
                                $map_area = $('#dokan-map'),
                                $input_area = $('#dokan-map-lat'),
                                $input_add = $('#dokan-map-add'),
                                $find_btn = $('#dokan-location-find-btn');

                        $find_btn.on('click', function (e) {
                            e.preventDefault();

                            geocodeAddress($input_add.val());
                        });

                        var gmap = new google.maps.Map($map_area[0], {
                            center: curpoint,
                            zoom: def_zoomval,
                            mapTypeId: window.google.maps.MapTypeId.ROADMAP
                        });

                        var marker = new window.google.maps.Marker({
                            position: curpoint,
                            map: gmap,
                            draggable: true
                        });

                        window.google.maps.event.addListener(gmap, 'click', function (event) {
                            marker.setPosition(event.latLng);
                            updatePositionInput(event.latLng);
                        });

                        window.google.maps.event.addListener(marker, 'drag', function (event) {
                            updatePositionInput(event.latLng);
                        });

                    } catch (e) {
                        console.log('Google API not found.');
                    }

                    autoCompleteAddress();

                    function updatePositionInput(latLng) {
                        $input_area.val(latLng.lat() + ',' + latLng.lng());
                    }

                    function updatePositionMarker() {
                        var coord = $input_area.val(),
                                pos, zoom;

                        if (coord) {
                            pos = coord.split(',');
                            marker.setPosition(new window.google.maps.LatLng(pos[0], pos[1]));

                            zoom = pos.length > 2 ? parseInt(pos[2], 10) : 12;

                            gmap.setCenter(marker.position);
                            gmap.setZoom(zoom);
                        }
                    }

                    function geocodeAddress(address) {
                        geocoder.geocode({'address': address}, function (results, status) {
                            if (status == window.google.maps.GeocoderStatus.OK) {
                                updatePositionInput(results[0].geometry.location);
                                marker.setPosition(results[0].geometry.location);
                                gmap.setCenter(marker.position);
                                gmap.setZoom(15);
                            }
                        });
                    }

                    function autoCompleteAddress() {
                        if (!$input_add)
                            return null;

                        $input_add.autocomplete({
                            source: function (request, response) {
                                // TODO: add 'region' option, to help bias geocoder.
                                geocoder.geocode({'address': request.term}, function (results, status) {
                                    response(jQuery.map(results, function (item) {
                                        return {
                                            label: item.formatted_address,
                                            value: item.formatted_address,
                                            latitude: item.geometry.location.lat(),
                                            longitude: item.geometry.location.lng()
                                        };
                                    }));
                                });
                            },
                            select: function (event, ui) {

                                $input_area.val(ui.item.latitude + ',' + ui.item.longitude);

                                var location = new window.google.maps.LatLng(ui.item.latitude, ui.item.longitude);

                                gmap.setCenter(location);
                                // Drop the Marker
                                setTimeout(function () {
                                    marker.setValues({
                                        position: location,
                                        animation: window.google.maps.Animation.DROP
                                    });
                                }, 1500);
                            }
                        });
                    }

                });
            })(jQuery);
</script>
