<?php

// set icon
function ikona(){
    $plugins_url = plugins_url();
    if(get_option('ico') == ''){
        $ikona = $plugins_url."/ac_poi_maps/images/map_icon.png";
    }else{
        $ikona = get_option('ico');
    }
    return $ikona;
}
// scripts
function ac_poi_maps_script_shortcode(){
    $plugins_url = plugins_url();

    if(get_option('api') != ''){
        wp_enqueue_script('google_api_single', 'https://maps.googleapis.com/maps/api/js?key='.get_option('api').'&sensor=false');
    }else{
        wp_enqueue_script('google_api_single', 'http://maps.google.com/maps/api/js?sensor=false' );
    }
    wp_enqueue_script('google_api_cluster', $plugins_url.'/ac_poi_maps/js/markerclusterer.js' );
    wp_register_script('skrypt_gm_single_poi', $plugins_url.'/ac_poi_maps/js/ac_poi_maps.js', array(
        'google_api_single', 'jquery', 'google_api_cluster'
    ));
    wp_localize_script('skrypt_gm_single_poi', 'styl_mapy', array(
        'var_style' => json_decode(get_option('style'))
    ));

    wp_enqueue_script( 'skrypt_gm_single_poi' );
}

// marker description
function ac_poi_maps_marker_description($data){
    $string = '';
    if($data['email'][0] ? $string .= 'e-mail: '.$data['email'][0].'<br>' : '');
    if($data['phone'][0] ? $string .= 'phone: '.$data['phone'][0].'<br>' : '');
    if($data['www'][0] ? $string .= 'www: '.$data['www'][0].'<br>' : '');
    return $string;
}

// single map
function ac_poi_map_single_function($atts){
    $plugins_url = plugins_url();
    ac_poi_maps_script_shortcode();
    $id = $atts['id'];
    $post_content = get_post($id);
    $custom = get_post_custom($atts['id']);
    $decription_marker = ac_poi_maps_marker_description($custom);
    $zoom = $atts['zoom'];	
    if($zoom == ''){$zoom = 13;}
    $bw = $atts['bw'];
    $popup = $atts['popup'];
    if($popup == 'on'){$popup = 1;}else{$popup = 0;}
    if($bw == 'true'){$bw = 1;}else{$bw = 0;}
    if($custom['marker'][0] == ''){ $marker_ico = 'null'; }else{ $marker_ico = $custom['marker'][0]; }
    $map = "<div id='ac_poi_map_single_".$id."' class='ac_poi_map_single ac_poi_map'
    data-map='single'
    data-popup='".$popup."'
    data-title='".$post_content->post_title."'
    data-content='".$decription_marker."'
    data-icon='".$marker_ico."'
    data-bw='".$bw."'
    data-zoom='".$zoom."'
    data-url='".ikona()."'
    data-lat='".$custom['latFld'][0]."'
    data-lng='".$custom['lngFld'][0]."' >";
    if($atts['title'] == 'on'){
            $map .= "<h3>".__('Map', ac_poimaps_settings()->translate_domain)." ".$post_content->post_title."</h3>";
    }
    $map .="<div id='single_".$id."' class='ac_poi_map_single_map ac_poi_map_main'></div>";
    $map .="</div>";
    return $map;
}

add_shortcode('map_single', 'ac_poi_map_single_function');

// category
function ac_poi_map_category_function($atts){
    $popup = $atts['popup'];
    if($popup == 'on'){$popup = 1;}else{$popup = 0;}
    $plugins_url = plugins_url();
    $id = $atts['id'];
    $zoom = $atts['zoom'];
    if($zoom == ''){$zoom = 13;}
    $bw = $atts['bw'];
    if($bw == 'true'){$bw = 1;}else{$bw = 0;}

    $cat_latFld = get_option('latFld');
    $cat_lngFld = get_option('lngFld');
    $term = get_term($id);
    $term_location = get_term_meta($id);
    $term_location = $term_location['term_location'];
    if(!is_null($term_location[0])){
        $tab_center = unserialize($term_location[0]);
        $cat_latFld = $tab_center['latFld'];
        $cat_lngFld = $tab_center['lngFld'];
    }
    //var_dump(unserialize($term_location));
    $map = "<div id='poi-map-category_".$id."' class='ac_poi_map_category ac_poi_map'
    data-zoom='".$zoom."'
    data-map='category'
    data-popup='".$popup."'
    data-url='".ikona()."'
    data-bw='".$bw."'
    data-latFld='".$cat_latFld."'
    data-lngFld='".$cat_lngFld."'>";

    if($atts['title'] == 'on'){
        $map .= "<h2>".__('Map', ac_poimaps_settings()->translate_domain).": ".$term->name."</h2>";
    }
    $map .="<div id='poi-map-single-category-show' class='ac_poi_map_category_map ac_poi_map_main'></div>";
    $lista_show = $atts['poi'];
    if(get_option('poi_list') != 0 || $lista_show == 'on'){
        $hide_list = 'ac_show';
    }elseif($lista_show == 'off'){
        $hide_list = 'ac_hide';
    }
    $args = array(
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'asc',
        'post_type'        => 'ac_poimaps',
        'tax_query' => array(
            array(
                'taxonomy' => $term->taxonomy,
                'terms'	   => $id
            )
        ),
        'post_status'      => 'publish'
    );
    $all_points = get_posts( $args );

    $map .="<ul id='list_post_".$id."' class='ac_poimaps_list_post_cat ".$hide_list."'>";
    foreach ( $all_points as $post_poi ) {
        $custom = get_post_custom($post_poi->ID);
        $content_post = get_post($post_poi->ID);
        $decription_marker = ac_poi_maps_marker_description($custom);

        $term_list = wp_get_post_terms($post_poi->ID, 'ac_poimaps_category', array("fields" => "ids"));
        $term_list = ac_category_list($term_list);

        $lacation_list = wp_get_post_terms($post_poi->ID, 'ac_poimaps_category_region');
        $lacation_list = ac_location_list($lacation_list);
        if($custom['marker'][0] == ''){ $marker_ico = 'null'; }else{ $marker_ico = $custom['marker'][0]; }
        $map .="<li
        data-title='".$content_post->post_name."'
        data-icon='".$marker_ico."'
        data-url='".ikona()."'
        data-lat='".$custom['latFld'][0]."'
        data-content='".$decription_marker."'
        data-lng='".$custom['lngFld'][0]."'
        data-country='".$lacation_list."'
        data-catid='".$term_list."'>";
        $map .= "<h3>".$content_post->post_name."</h3><div>";
        $content = $content_post->post_content;
        $content = apply_filters('the_content', $content);
        $map .= $content."</div></li>";
    }
    $map .="</ul>";

    $map .= "</div>";
    return $map;
}
add_shortcode('map_category', 'ac_poi_map_category_function');


function ac_category_list($list){
    $ile = count($list);
    $html = '';
    $i = 1;
    foreach ($list as $value) {
        $html .= $value;
        if($i < $ile){
            $html .= ',';
        }
        $i++;
    }
    return $html;
}

function ac_location_list($list){
    $ile = count($list);
    $html = '';
    $i = 1;
    foreach ($list as $value) {
        $html .= $value->slug;
        if($i < $ile){
            $html .= ',';
        }
        $i++;
    }
    return $html;
}

//full map

function map_full( $atts ){
    $popup = $atts['popup'];
    if($popup == 'on'){$popup = 1;}else{$popup = 0;}
    $plugins_url = plugins_url();
    $label_cat = __('Category', ac_poimaps_settings()->translate_domain);
    $label_woj = __('Province', ac_poimaps_settings()->translate_domain);
    $zoom = $atts['zoom'];
    if($zoom == ''){$zoom = 6;}
    $bw = $atts['bw'];
    if($bw == 'true'){$bw = 1;}else{$bw = 0;}

    ac_poi_maps_script_shortcode();
    if(get_option('filtr') == 1){
        $hide_select = 'ac_show';
    }else{
        $hide_select = 'ac_hide';
    }
    $map = "<div id='ac_poi_map_full_".rand(1000,9999)."'  class='ac_poi_map_full ac_poi_map'
    data-map='full'
    data-url='".ikona()."'
    data-popup='".$popup."'
    data-bw='".$bw."'
    data-zoom='".$zoom."'
    data-latFld='".get_option('latFld')."'
    data-lngFld='".get_option('lngFld')."'>";
    if($atts['title'] == 'on'){
        $map .= "<h2>".__('Map', ac_poimaps_settings()->translate_domain)."</h2>";
    }
    $map .="<div id='' class='poi-select-box ".$hide_select."'>";
    $map .="<div>";
    if(get_option('regions') == 1){
        $map .="<label>".$label_cat.":</label><br>";
    }
    $map .="<select class='select_box cat_select' name='cat' id='cat'>";
    $map .="<option value='0' class=''>".__('All', 'ac_poi_maps')."</option>";
    $args = array(
        'orderby' => 'name',
        'parent' => 0,
        'hide_empty' => 1,
        'taxonomy' => 'ac_poimaps_category'
    );
    $categories = get_categories( $args );

    $lista_main = array();
    foreach ( $categories as $category ) {
        //var_dump($category);
        if($_POST["cat"] == $category->term_id){
            $selected="selected='selected' ";
        }else{
            $selected = '';
        }
        $map .= "<option ".$selected." value=".$category->term_id." class='".$category->slug."'>".$category->name."</option>";
        $lista_main[] = $category->term_id;
    }
    $map .="</select>";
    $map .="</div><div>";
    if(get_option('regions') == 1){
        $args = array(
            'orderby' => 'name',
            'parent' => 0,
            'hide_empty' => 1,
            'taxonomy' => 'ac_poimaps_category_region'
        );

        $all_points_position = get_categories( $args );
        $map .="<label>".$label_woj.":</label><br>";
        $map .="<select class='select_box woj_select'>";
        $map .="<option value='0' class='' data-lnt='".get_option('latFld')."' data-lng='".get_option('lngFld')."'>".__('All', 'ac_poi_maps')."</option>";
        foreach ( $all_points_position as $post_poi ) {
            $term_meta = get_term_meta($post_poi->term_id, 'term_location', true);
            $lat = $term_meta["latFld"];
            $lng = $term_meta["lngFld"];
            $map .="<option value='".$post_poi->category_nicename."' data-lnt='".$lat."' data-lng='".$lng."'>".$post_poi->name."</option>";
        }
        $map .="</select>";
    }
    $map .="</div>";
    $map .="</div>";
    //mapa wlasciwa
    $map .="<div id='' class='ac_poi_map_full_map ac_poi_map_main'></div>";
    $lista_show = $atts['poi'];
    if(get_option('poi_list') != 0 || $lista_show == 'on'){
        $hide_list = 'ac_show';
    }elseif($lista_show == 'off'){
        $hide_list = 'ac_hide';
    }
    $args2 = array(
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'asc',
        'post_type'        => 'ac_poimaps',
        'post_status'      => 'publish'
    );
    $all_points = get_posts( $args2 );
    //var_dump($all_points);
    $map .="<ul id='' class='ac_poimaps_full_list_post ".$hide_list."'>";
    foreach ( $all_points as $post_poi ) {
        $custom = get_post_custom($post_poi->ID);
        $content_post = get_post($post_poi->ID);
        $decription_marker = ac_poi_maps_marker_description($custom);
        $term_list = wp_get_post_terms($post_poi->ID, 'ac_poimaps_category', array("fields" => "ids"));
        $term_list = ac_category_list($term_list);
        $lacation_list = wp_get_post_terms($post_poi->ID, 'ac_poimaps_category_region');
        $lacation_list = ac_location_list($lacation_list);
        if($custom['marker'][0] == ''){ $marker_ico = 'null'; }else{ $marker_ico = $custom['marker'][0]; }
        $map .="<li
        data-title='".$content_post->post_name."'
        data-icon='".$marker_ico."'
        data-url='".ikona()."'
        data-lat='".$custom['latFld'][0]."'
        data-content='".$decription_marker."'
        data-lng='".$custom['lngFld'][0]."'
        data-country='0,".$lacation_list."'
        data-catid='0,".$term_list."'>";
        $map .= "<h3>".$content_post->post_name."</h3><div>";
        $content = $content_post->post_content;
        $content = apply_filters('the_content', $content);
        $map .= $content."</div></li>";
    }
    $map .="</ul>";
    $map .= "</div>";
    return $map;
}
add_shortcode( 'map_full', 'map_full' );