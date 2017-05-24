<?php
/*
Plugin Name: POI maps
Plugin URI: http://ac.tox.ovh/
Description: Punkty na mapie
Author: Tomasz Gołkowski
Version: 2.0
Author URI: http://tox.ovh/
*/

function ac_poimaps_settings(){
    $variables = array(
        'ac_settings()->translate_domain' => 'ac_poi_maps',
        'plugin_dir' => plugins_url().'/ac_poi_maps'
    );
    $object = (object) $variables;
    return $object;
}

include_once 'loader.php';
load_modules('/src/functions/');
include_once 'option.php';
require('shortcode.php');


function ac_poi_maps_style() {
    wp_enqueue_style( 'ac_poi_maps', plugins_url().'/ac_poi_maps/css/style.css' );
    if(get_option('def_css') == 0 || get_option('def_css') == '' || get_option('def_css') == null){
        wp_enqueue_style( 'def_style', plugins_url().'/ac_poi_maps/css/map_elem.css' );
    }
}
add_action( 'wp_enqueue_scripts', 'ac_poi_maps_style' );


//admin js
function ac_poimaps_google_maps() {
    $plugins_url = plugins_url();
    $screen = get_current_screen();
    //var_dump($screen);
    $screen = $screen -> post_type;
    if ($screen == "ac_poimaps"){
        if(get_option('api') != ''){
            wp_enqueue_script('google_api', 'https://maps.googleapis.com/maps/api/js?key='.get_option('api'));
        }else{
            wp_enqueue_script('google_api', 'http://maps.google.com/maps/api/js?sensor=false' );
        }
        wp_register_script('ac_poimaps_google_maps', $plugins_url.'/ac_poi_maps/js/gm.js' );
        wp_enqueue_script('ac_poimaps_google_maps');				
    }
	
}
add_action( 'admin_enqueue_scripts', 'ac_poimaps_google_maps' );


/* wyświetlenie pełnej informacji o punkcie*/

add_filter("manage_edit-ac_poimaps_columns", "ac_poimaps_edit_columns");
function ac_poimaps_edit_columns($columns) {
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => __('Title', ac_poimaps_settings()->translate_domain),
        "miejsce" => __('Address', ac_poimaps_settings()->translate_domain)
    );
    if(get_option('wojewodztwa') == 1){
        $columns["wojewodztwo"]	= __('Region', ac_poimaps_settings()->translate_domain);
    }
    $columns["email"] = __('E-mail');
    $columns["phone"] = __('Phone:', ac_poimaps_settings()->translate_domain);
    if(get_option('category') == 1){
        $columns["category"] = __('Category', ac_poimaps_settings()->translate_domain);
    }
    $columns["icl_translations"] = $columns['icl_translations'];
    $columns['date'] = __('Date', ac_poimaps_settings()->translate_domain);

    return $columns;
}

add_action("manage_posts_custom_column",  "poimaps_custom_columns");
function poimaps_custom_columns($column) {
    global $post;		
    switch ($column) {
        case "miejsce":
            $custom = get_post_custom();
            echo $custom["adres"][0];
            break;
        case "wojewodztwo":
            $term_list = wp_get_post_terms($post->ID, 'ac_poimaps_category_region');
            //var_dump($term_list[0]->name);
            echo $term_list[0]->name;
            break;
        case "phone":
            $custom = get_post_custom();
            echo $custom["phone"][0];
            break;
        case "email":
            $custom = get_post_custom();
            echo $custom["email"][0];
            break;
        case "category":
            $term_list = wp_get_post_terms($post->ID, 'ac_poimaps_category');
            //var_dump($term_list[0]->name);
            echo $term_list[0]->name;
            break;
    }		
}
function poi_load_text_domain(){
    load_plugin_textdomain('ac_poi_maps', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
}

add_action('plugins_loaded', 'poi_load_text_domain');


// filtr
function restrict_poi() {
    global $typenow;
    $show = __('Show All'); 
    $post_type = 'ac_poi_maps'; // change HERE
    $taxonomy = 'ac_poimaps_category'; // change HERE
    if ($typenow == $post_type) {
        $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
        $info_taxonomy = get_taxonomy($taxonomy);
        wp_dropdown_categories(array(
            'show_option_all' => __("$show {$info_taxonomy->label}", 'ac_poi_maps'),
            'taxonomy' => $taxonomy,
            'name' => $taxonomy,
            'orderby' => 'name',
            'selected' => $selected,
            'show_count' => true,
            'hide_empty' => true,
        ));
    };
}

add_action('restrict_manage_posts', 'restrict_poi');

function poi_convert_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'ac_poi_maps'; // change HERE
	$taxonomy = 'ac_poimaps_category'; // change HERE
	$q_vars = &$query->query_vars;
	if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}

add_filter('parse_query', 'poi_convert_id_to_term_in_query');


