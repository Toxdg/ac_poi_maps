<?php
// create custom plugin settings menu
//
function ac_media_poi_js() {
    $plugins_url = plugins_url();
//    wp_enqueue_script('media-upload');
//    wp_enqueue_script('thickbox');
//    wp_enqueue_style('thickbox');
    wp_enqueue_media();
    wp_register_script('ac_media_poi', $plugins_url.'/ac_poi_maps/js/media.js',  array('jquery', 'media-editor'));
    wp_enqueue_script('ac_media_poi');
}
add_action('admin_enqueue_scripts', 'ac_media_poi_js');


add_action('admin_menu', 'plugin_poimaps_create_menu');

function plugin_poimaps_create_menu() {
	//create new top-level menu
	//add_menu_page('File settings Settings', 'Settings', 'administrator', __FILE__, 'file_settings_page',plugins_url('/img/icon.png', __FILE__));
	add_submenu_page( 'edit.php?post_type=ac_poimaps', 'Settings', __('Settings'), 'manage_options', __FILE__, 'ac_poimaps_settings_page' ); 	
}

add_action( 'admin_init', 'ac_poimaps_plugin_settings' );

function ac_poimaps_plugin_settings() {
    //register our settings
    register_setting( 'poimaps-settings-group', 'category' );
    register_setting( 'poimaps-settings-group', 'regions' );
    register_setting( 'poimaps-settings-group', 'poi_list' );
    register_setting( 'poimaps-settings-group', 'filtr' );
    register_setting( 'poimaps-settings-group', 'latFld' );
    register_setting( 'poimaps-settings-group', 'lngFld' );	
    register_setting( 'poimaps-settings-group', 'api' );
    register_setting( 'poimaps-settings-group', 'style' );
    register_setting( 'poimaps-settings-group', 'ico' );
    register_setting( 'poimaps-settings-group', 'def_css' );
    register_setting( 'poimaps-settings-group', 'ico_id' );
}

function ac_poimaps_settings_page() {
?>
<div class="wrap">
<h2><?php echo __('Settings');?> - POI Maps </h2>

<form method="post" action="options.php">
    <?php settings_fields( 'poimaps-settings-group' ); ?>
    <?php do_settings_sections( 'poimaps-settings-group' ); ?>
    <table class="form-table poi-settings">
    	<tr valign="top">
            <th scope="row"></th>
            <td><h2><?php echo __('Settings', ac_poimaps_settings()->translate_domain); ?></h2></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php echo __('Default style', ac_poimaps_settings()->translate_domain);?></th>
            <td><input type="checkbox" name="def_css" value="0"<?php if(get_option('def_css') == 0 || get_option('def_css') == ''){ echo 'checked'; } ?>><?php if(get_option('def_css') == 0){ echo 'on'; }else{ echo 'off';} ?></td>
        </tr>
        <tr valign="top">
            <th scope="row"><?php echo __('Category', ac_poimaps_settings()->translate_domain);?></th>
            <td><input type="checkbox" name="category" value="1"<?php if(get_option('category') == 1){ echo 'checked'; } ?>><?php if(get_option('category') == 1){ echo 'on'; }else{ echo 'off';} ?></td>
        </tr> 
  
       <tr valign="top">
            <th scope="row"><?php echo __('Province', ac_poimaps_settings()->translate_domain);?></th>
            <td><input type="checkbox" name="regions" value="1"<?php if(get_option('regions') == 1){ echo 'checked'; } ?>><?php if(get_option('regions') == 1){ echo 'on'; }else{ echo 'off';} ?></td>
        </tr> 
        <tr valign="top">
            <th scope="row"><?php echo __('List POI', ac_poimaps_settings()->translate_domain);?><br><?php echo __('*force show', ac_poimaps_settings()->translate_domain);?></th>
            <td><input type="checkbox" name="poi_list" value="1"<?php if(get_option('poi_list') == 1){ echo 'checked'; } ?>><?php if(get_option('poi_list') == 1){ echo 'on'; }else{ echo 'off';} ?></td>
        </tr> 
        <tr valign="top">
            <th scope="row"><?php echo __('Filter', ac_poimaps_settings()->translate_domain);?></th>
            <td><input type="checkbox" name="filtr" value="1"<?php if(get_option('filtr') == 1){ echo 'checked'; } ?>><?php if(get_option('filtr') == 1){ echo 'on'; }else{ echo 'off';} ?></td>
        </tr> 
        <tr valign="top">
            <th scope="row"><?php echo __('Api Key', ac_poimaps_settings()->translate_domain);?></th>
            <td><input type="text" name="api" value="<?php echo get_option('api');?>"></td>
        </tr> 
        <tr valign="top">
            <th scope="row"><?php echo __('Style', ac_poimaps_settings()->translate_domain);?><br><a href="https://snazzymaps.com" target="_blank"><?php echo __('Get Joson', ac_poimaps_settings()->translate_domain);?></a></th>
            <td><textarea name="style" style="width:30% !important; height: 200px;" ><?php echo get_option('style');?></textarea></td>
        </tr> 
        <tr valign="top">
            <th scope="row"><?php echo __('Map Icon', ac_poimaps_settings()->translate_domain);?></th>
            <td><a href="#" id="set-ico-button" class="button upload_image_button"><?php echo __('Set icon', ac_poimaps_settings()->translate_domain);?></a><a href="#" id="reset_ico" class="button"><?php echo __('Remove icon', ac_poimaps_settings()->translate_domain);?></a><br>
                <input type="text" name="ico" id="icon_input" value="<?php echo get_option('ico');?>">
                <input type="text" name="ico_id" id="ico_id" value="<?php echo get_option('ico_id');?>" style="opacity:0; display: none;">
            </td>
        </tr> 
        <tr valign="top">
            <th scope="row"></th>
            <td><h2><?php echo __('Map Center', ac_poimaps_settings()->translate_domain); ?></h2> <a href="http://web4you.com.pl/11.html" target="_blank"><?php echo __('check', ac_poimaps_settings()->translate_domain); ?></a></td>
        </tr> 
        <tr valign="top">
            <th scope="row">latFld</th>
            <td><input type="text" name="latFld" id="latFld" value="<?php if(get_option('latFld') == ''){ echo '52.173931692568'; }else{ echo get_option('latFld');} ?>"></td>
        </tr>
        <tr valign="top">
            <th scope="row">lngFld</th>
            <td><input type="text" name="lngFld" id="lngFld" value="<?php if(get_option('lngFld') == ''){ echo '18.8525390625'; }else{ echo get_option('lngFld');} ?>"></td>
        </tr>
        <tr valign="top">
            <th scope="row"></th>
            <td><a href="#" id="set-geo-button" class="button upload_image_button"><?php echo __('Set geolocation', ac_poimaps_settings()->translate_domain);?></a></td>
        </tr>
        <tr valign="top">
            <th scope="row"></th>
            <td><h2><?php echo __('Shortcodes', ac_poimaps_settings()->translate_domain); ?></h2></td>
        </tr>  
        <tr valign="top">
            <th scope="row">Shortcode</th>
            <td>
                <table>
                    <tr><td><b><?php echo __('Full view', ac_poimaps_settings()->translate_domain);?>:</b> </td><td>[map_full title='off' zoom='5' bw='false' popup='on' poi="on"]</td></tr>
                    <tr><td><b><?php echo __('Single Point', ac_poimaps_settings()->translate_domain);?>:</b> </td><td>[map_single id='ID_POST' title='off' zoom='13' bw='false' popup='on']</td></tr>
                    <tr><td><b><?php echo __('Single category POI', ac_poimaps_settings()->translate_domain);?>:</b> </td><td>[map_category id='CATEGORY_ID' title='on' zoom='8' bw='false' popup='off' poi="off"]</td></tr>
                    <tr><td>*title<br>*bw<br>*poi<br>*popup</td><td>on / off<br>true / false<br>on / off<br>on / off</td></tr>
                </table>
            </td>
        </tr>  
          
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>