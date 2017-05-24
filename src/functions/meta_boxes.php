<?php

add_action("admin_init", "ac_poimaps_info");
function ac_poimaps_info() {
    add_meta_box("prodInfo-meta",  __( 'Place:', ac_poimaps_settings()->translate_domain) , "poimaps_meta_options", "ac_poimaps", "normal", "low"); // tu mozna ustawic pozycje blokow
    add_meta_box("prodInfo-meta2",  __( 'Contact:', ac_poimaps_settings()->translate_domain) , "poimaps_meta_options2", "ac_poimaps", "side", "low"); // tu mozna ustawic pozycje blokow
    add_meta_box("prodInfo-meta4",  __( 'Marker:', ac_poimaps_settings()->translate_domain) , "poimaps_meta_options4", "ac_poimaps", "side", "low"); // tu mozna ustawic pozycje blokow
    add_meta_box("prodInfo-meta3",  'Shortcode' , "poimaps_meta_options3", "ac_poimaps", "side", "low"); // tu mozna ustawic pozycje blokow
}
function poimaps_meta_options4() {
    global $post;

    $custom = get_post_custom($post->ID);?>
    <?php $marker = $custom["marker"][0]; ?>
    <label><?php _e( 'Marker:', ac_poimaps_settings()->translate_domain ) ?></label><input type="text" name="marker"  id="marker" class="marker_input" value="<?php echo esc_attr( $marker ); ?>" size="80" style="width:99%" placeholder="<?php _e('marker url(.png)', ac_poimaps_settings()->translate_domain);?>" />
    <a href="#" id="set-ico-button" class="button upload_image_button"><?php echo __('Set icon', ac_poimaps_settings()->translate_domain);?></a><a href="#" id="reset_ico" class="button"><?php echo __('Remove icon', 'poi_maps');?></a>
<?php }

function poimaps_meta_options3() {
    global $post;?>
    <label><?php __('Point Shortcode', ac_poimaps_settings()->translate_domain);?></label><input type="text" value="[map_single id='<?php echo $post->ID ?>' title='off' zoom='13' bw='false' popup='off']" size="80" style="width:99%" />
    <?php
}

function poimaps_meta_options2() {
    global $post;

    $custom = get_post_custom($post->ID);?>
    <?php $email = $custom["email"][0]; ?>
    <label><?php _e( 'E-mail:', ac_poimaps_settings()->translate_domain ) ?></label><input type="text" name="email"  id="email" value="<?php echo esc_attr( $email ); ?>" size="80" style="width:99%" placeholder="<?php _e('your@mail.com', ac_poimaps_settings()->translate_domain);?>" />
    <?php $phone = $custom["phone"][0]; ?>
    <label><?php _e( 'Phone:', ac_poimaps_settings()->translate_domain ) ?></label><input type="text" name="phone"  id="phone" value="<?php echo esc_attr( $phone ); ?>" size="80" style="width:99%" placeholder="+48 32 356 36 36" />
    <?php $www = $custom["www"][0]; ?>
    <label><?php _e( 'www:', ac_poimaps_settings()->translate_domain ) ?></label><input type="text" name="www"  id="www" value="<?php echo esc_attr( $www ); ?>" size="80" style="width:99%" placeholder="http://yoursite.com" />

<?php }

function poimaps_meta_options() {
    global $post;

    $custom = get_post_custom($post->ID);?>
    <?php $adres = $custom["adres"][0]; ?>
    <label><?php _e( 'Address:', ac_poimaps_settings()->translate_domain ) ?></label><input type="text" name="adres"  id="adres" value="<?php echo esc_attr( $adres ); ?>" size="80" style="width:99%" placeholder="<?php _e('Address: street number, city', ac_poimaps_settings()->translate_domain); ?>" />
    <input type="button" name="check_adress" value="<?php _e('Check the address', 'ac_poi_maps');?>" class="button-secondary set_position">
    <br>
    <br><br><br>
    <?php $latFld = $custom["latFld"][0]; ?>
    <label><?php _e( 'Latitude', ac_poimaps_settings()->translate_domain ) ?>:</label><input type="text" name="latFld"  id="latFld" value="<?php echo esc_attr( $latFld ); ?>" size="80" style="width:99%" />

    <?php $lngFld = $custom["lngFld"][0]; ?>
    <label><?php _e( 'Longitude', ac_poimaps_settings()->translate_domain ) ?>:</label><input type="text" name="lngFld" id="lngFld"  value="<?php echo esc_attr( $lngFld ); ?>" size="80" style="width:99%" />
    <div style="width: 100%; height: 30px;"></div>
    <p class="description">
        <?php _e('Latitude and longitude should be given in decimal form.', ac_poimaps_settings()->translate_domain);?>
    </p>
    <div id="map_canvas" style="width:99%; height:250px; background:#E3E3E3" data-latFld="<?php echo esc_attr( $latFld ); ?>" data-lngFld="<?php echo esc_attr( $lngFld ); ?>"></div>

<?php }



add_action('save_post', 'save_acpoimaps_data');
function save_acpoimaps_data() {
    global $post;
    update_post_meta($post->ID, "marker", $_POST["marker"]);
    update_post_meta($post->ID, "adres", $_POST["adres"]);

    update_post_meta($post->ID, "email", $_POST["email"]);
    update_post_meta($post->ID, "phone", $_POST["phone"]);

    update_post_meta($post->ID, "www", $_POST["www"]);

    if($_POST["latFld"] != ''  &&  $_POST["lngFld"] != '' ){
        update_post_meta($post->ID, "latFld", $_POST["latFld"]);
        update_post_meta($post->ID, "lngFld", $_POST["lngFld"]);
    }else{
        update_post_meta($post->ID, "latFld", '52.173931692568');
        update_post_meta($post->ID, "lngFld", '18.8525390625');
    }
    update_post_meta($post->ID, "exerpt", $_POST["exerpt"]);
    update_post_meta($post->ID, "exerpt2", $_POST["exerpt2"]);
}
