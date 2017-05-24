<?php

add_action('init', 'poimaps_register');
function poimaps_register() {
    $plugins_url = plugins_url();
    $labels = array(

    );
    $args = array(
        'name' => __('Points on map', ac_poimaps_settings()->translate_domain),
        'label' => __('Points on map', ac_poimaps_settings()->translate_domain),
        'singular_label' => __('Point', ac_poimaps_settings()->translate_domain),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array( 'slug' => 'punkt' ), // rewrite url
        'query_var' => 'punkt',
        //'rewrite' => true,
        'supports' => array('title','thumbnail','editor', 'tags'),
        //'taxonomies' => array('post_tag'),
        'menu_icon' => 'dashicons-location-alt'
    );
    register_post_type( 'ac_poimaps' , $args );
}
//doanie kategorii
function create_poimaps_taxonomies() {
    register_taxonomy(
        'ac_poimaps_category',
        'ac_poimaps',
        array(
            'labels' => array(
                'name' => __('Category POI', ac_poimaps_settings()->translate_domain),
                'add_new_item' => __('Add POI Category', ac_poimaps_settings()->translate_domain),
                'new_item_name' => __('New POI category', ac_poimaps_settings()->translate_domain)

            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'rewrite' => array( 'slug' => 'ac_poi_maps' ), // rewrite url
            'hierarchical' => true
        )
    );
}

if(get_option('category') == 1){
    add_action( 'init', 'create_poimaps_taxonomies' );
}

function create_poimaps_taxonomies_region() {
    register_taxonomy(
        'ac_poimaps_category_region',
        'ac_poimaps',
        array(
            'labels' => array(
                'name' => __('Region POI', 'ac_poi_maps'),
                'add_new_item' => __('Add POI Region', ac_poimaps_settings()->translate_domain),
                'new_item_name' => __('New POI Region', ac_poimaps_settings()->translate_domain)

            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'rewrite' => array( 'slug' => 'ac_poi_maps_region' ), // rewrite url
            'hierarchical' => true
        )
    );
}

function change_default_title( $title ){
    $screen = get_current_screen();
    if ( 'ac_poimaps' == $screen->post_type ){
        $title = __('Point name', ac_poimaps_settings()->translate_domain);
    }
    return $title;
}

add_filter( 'enter_title_here', 'change_default_title' );


if(get_option('regions') == 1){
    add_action( 'init', 'create_poimaps_taxonomies_region' );
}