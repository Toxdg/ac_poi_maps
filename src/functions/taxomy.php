<?php
// Add term page
function acpoi_taxonomy_add_new_meta_field() {
    // this will add the custom meta field to the add new term page
    ?>
    <label><?php _e( 'Address:', ac_poimaps_settings()->translate_domain ) ?></label><input type="text" name="adres"  id="adres" value="<?php echo esc_attr( $adres ); ?>" size="80" style="width:95%" placeholder="<?php _e('Address: street number, city', 'ac_poi_maps'); ?>" />
    <input type="button" name="check_adress" value="<?php _e('Check the address', ac_poimaps_settings()->translate_domain);?>" class="button-secondary set_position">
    <br>
    <input type="text" name="term_meta[latFld]" id="latFld" value="" style="">
    <input type="text" name="term_meta[lngFld]" id="lngFld" value="" style=""><br>
    <div id="map_canvas" style="width:95%; height:250px; background:#E3E3E3" data-latFld="<?php if(get_option('latFld') == ''){ echo '52.173931692568'; }else{ echo get_option('latFld');} ?>" data-lngFld="<?php if(get_option('lngFld') == ''){ echo '18.8525390625'; }else{ echo get_option('lngFld');} ?>"></div>
    <br>
    <?php
}

add_action( 'ac_poimaps_category_region_add_form_fields', 'acpoi_taxonomy_add_new_meta_field', 10, 2 );
// Edit term page
function acpoi_taxonomy_edit_meta_field($term) {
    // put the term ID into a variable
    $t_id = $term->term_id;

    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_term_meta($t_id, 'term_location', true);
    if($term_meta['latFld'] == '' || $term_meta['lngFld'] == ''){
        $term_meta['latFld'] = '50.243692022558044';
        $term_meta['lngFld'] = '19.0283203125';
    }
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for=""><?php _e( 'Region Position', ac_poimaps_settings()->translate_domain ); ?></label></th>
        <td>
            <label><?php _e( 'Address:', ac_poimaps_settings()->translate_domain ) ?></label><br>
            <input type="text" name="adres"  id="adres" value="<?php echo esc_attr( $adres ); ?>" size="80" style="width:95%" placeholder="<?php _e('Address: street number, city', ac_poimaps_settings()->translate_domain); ?>" />
            <input type="button" name="check_adress" value="<?php _e('Check the address', ac_poimaps_settings()->translate_domain);?>" class="button-secondary set_position">
            <br>
            <label><?php _e( 'lat:', ac_poimaps_settings()->translate_domain ) ?></label><br>
            <input type="text" name="term_meta[latFld]" id="latFld" value="<?php echo $term_meta['latFld'];?>">
            <br><label><?php _e( 'lng:', ac_poimaps_settings()->translate_domain ) ?></label><br>
            <input type="text" name="term_meta[lngFld]" id="lngFld" value="<?php echo $term_meta['lngFld'];?>">
            <div id="map_canvas" style="width:95%; height:250px; background:#E3E3E3" data-latFld="<?php echo $term_meta['latFld'];?>" data-lngFld="<?php echo $term_meta['lngFld'];?>"></div>
        </td>
    </tr>
    <?php
}
add_action( 'ac_poimaps_category_region_edit_form_fields', 'acpoi_taxonomy_edit_meta_field', 10, 2 );

// Save extra taxonomy fields callback function.
function save_acpoitaxonomy_custom_meta( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        update_term_meta($term_id, 'term_location', $_POST['term_meta']);
    }
}
add_action( 'edited_ac_poimaps_category_region', 'save_acpoitaxonomy_custom_meta', 10, 2 );
add_action( 'created_ac_poimaps_category_region', 'save_acpoitaxonomy_custom_meta', 10, 2 );