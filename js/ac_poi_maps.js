;(function($){
    $(document).ready(function(){
        $( window ).load(function() {
            $('.ac_poi_map').each(function($e){
                if($(this).data('map') == 'single'){
                    init_ac_single_map($(this));
                }else if($(this).data('map') == 'category'){
                    init_ac_category_map($(this));
                }else{
                    init_ac_all_map($(this));
                }
            });
            /*
             *
             *
             * single map
             *
             *
             *
             * */
            function init_ac_single_map($elem){
                var lat = $elem.data('lat');
                var lng = $elem.data('lng');
                var url_data = $elem.data( 'url');
                var ico_data = $elem.data( 'icon');
                var zoom = $elem.data( 'zoom');
                var bw = $elem.data( 'bw');
                var popup = $elem.data( 'popup');
                if(ico_data != 'null'){url_data = ico_data;}
                var center = new google.maps.LatLng(lat, lng);
                if( bw == 1 ){
                    var mapOptions = {
                        'center': center,
                        'zoom': zoom,
                        'mapTypeId': google.maps.MapTypeId.ROADMAP,  // typ mapy
                        'styles': [ // style z josona
                            { "stylers": [
                                { "saturation": -100 }
                            ] }
                        ]
                    };
                }else{
                    var mapOptions = {
                        'center': center,
                        'zoom': zoom,
                        'mapTypeId': google.maps.MapTypeId.ROADMAP,  // typ mapy
                        'scrollwheel': false,
                        'styles': styl_mapy.var_style
                    };
                }
                var map = new google.maps.Map($elem.find('.ac_poi_map_single_map').get(0),
                    mapOptions);
                var markerOptions = {
                    'position': center,
                    'icon': url_data,
                    'map': map
                };


                var marker = new google.maps.Marker(markerOptions);
                    // funkacja klikniecia w marker
                    (function(marker){
                        google.maps.event.addListener(marker, "click", function(event) {
                            if(popup == 1){
                                make_desc($elem);
                            }
                        });
                    })(marker);
                    //tworzymy infowindow
                    function make_desc(ind){
                        size_marker_icon(ind);
                    }
                    // dodaje marker do tablicy markerow
                    function size_marker_icon($ind){
                        var ico = $ind.data('icon');
                        if($ind.data('icon') != null){
                            ico = ico;
                        }else{
                            ico = $ind.data('url');
                        }
                        var img = new Image();
                        var img_h = 0;
                        img.src = ico;
                        img.onload = function() {
                            img_h = this.height;
                            //return img_h;
                            var title = $ind.data('title');
                            var desc = $ind.data('content');
                            infowindow = new google.maps.InfoWindow({
                                content: '<div style="margin:0 0 0px 0px;" class="ac_poi_map-info-box">\n\<div>'+title+'</div><div>'+desc+'</div></div>',
                                pixelOffset: new google.maps.Size(0,img_h),
                                height:300
                            });
                            infowindow.open(map,marker);
                        }
                    }
                    $(window).on('resizemap resize',function() {
                        google.maps.event.trigger(map, "resize");
                        map.setCenter(center);
                    });
            }
            /*
             *
             *
             * Category map
             *
             *
             *
             * */
            function init_ac_category_map($elem){
                var $liczba_cat = 0;
                var zoom = $elem.data( 'zoom');
                var markers = [];
                var url_data = $elem.data( 'url');
                var bw = $elem.data( 'bw');
                var $latFld_set = $elem.data( 'latfld');
                var $lngFld_set = $elem.data( 'lngfld');
                var popup = $elem.data( 'popup');
                // tu mod
                var szer_cat = 0;
                var dlug_cat = 0;
                var ile_cat = 0;
                var list_wrapper = $elem.find('.ac_poimaps_list_post_cat > li');
                list_wrapper.each(function(){
                    szer_cat = szer_cat + $(this).data( 'lat');
                    dlug_cat = dlug_cat + $(this).data( 'lng');
                    ile_cat = ile_cat + 1;
                });
                if(ile_cat > 1) {
                        $latFld_set = szer_cat / ile_cat;
                        $lngFld_set = dlug_cat / ile_cat;
                }
                // tu koniec

                //console.log($latFld_set+' '+$lngFld_set+' '+bw);
                var center = new google.maps.LatLng($latFld_set, $lngFld_set); // wspolrzedne polozenia
                if( bw == 1 ){
                    var options = {
                        'center': center,
                        'zoom': zoom,
                        'mapTypeId': google.maps.MapTypeId.ROADMAP,  // typ mapy
                        'styles': [ // style z josona
                            { "stylers": [
                                { "saturation": -100 }
                            ] }
                        ]
                    };
                }else{
                    var options = {
                        'center': center,
                        'zoom': zoom,
                        'mapTypeId': google.maps.MapTypeId.ROADMAP,  // typ mapy
                        'scrollwheel': false,
                        'styles': styl_mapy.var_style
                    };
                }

                var map = new google.maps.Map($elem.find('.ac_poi_map_category_map').get(0), options);
                // generowanie listy markerow
                list_wrapper.each(function(){
                    var szer = $(this).data( 'lat');
                    var dlug = $(this).data( 'lng');
                    var ico_data = $(this).data( 'icon');
                    if(ico_data != 'null'){url_data = ico_data;}
                    // jesli sa wspolrzedne geograficzne
                    if (szer != '' && dlug !=''){
                        var latLng = new google.maps.LatLng(szer, dlug);
                        var marker = new google.maps.Marker({
                            'position': latLng,
                            anchor: new google.maps.Point(19,10),
                            icon: url_data
                        });

                        // funkacja klikniecia w marker
                        (function(marker){
                            google.maps.event.addListener(marker, "click", function(event) {
                                if(popup == 1){
                                    ind = markers.indexOf( this );
                                    make_desc(ind);
                                }

                            });
                        })(marker);

                        // wyswietlanie opisu punktu na mapie
                        function make_desc(ind){
                            size_marker_icon(ind);
                        }
                        // dodaje marker do tablicy markerow
                        function size_marker_icon($ind){
                            var ico = list_wrapper.eq($ind).data('icon');
                            if(list_wrapper.eq($ind).data('icon') != null){
                                ico = ico;
                            }else{
                                ico = list_wrapper.eq($ind).data('url');
                            }
                            var img = new Image();
                            var img_h = 0;
                            img.src = ico;
                            img.onload = function() {
                                img_h = this.height;
                                //return img_h;
                                var title = list_wrapper.eq(ind).data('title');
                                var desc = list_wrapper.eq(ind).data('content');
                                infowindow = new google.maps.InfoWindow({
                                    content: '<div style="margin:0 0 0px 0px;" class="ac_poi_map-info-box">\n\<div>'+title+'</div><div>'+desc+'</div></div>',
                                    pixelOffset: new google.maps.Size(0,img_h),
                                    height:300
                                });
                                infowindow.open(map,marker);
                            }
                        }
                        markers.push(marker);

                    }else{
                        //cos poszlo nie tak
                        console.log('error point');
                    }


                });
                // wlaczenie grupowania markerow
                var markerCluster;
                var markerCluster = new MarkerClusterer(map, markers, { ignoreHidden: true });

            }
            /*
            *
            *
            * full map
            *
            *
            *
            * */
            function init_ac_all_map($elem){
                var zoom = $elem.data( 'zoom');
                var url_data = $elem.data( 'url');
                var bw = $elem.data( 'bw');
                var $latFld_set = $elem.data( 'latfld');
                var $lngFld_set = $elem.data( 'lngfld');
                var popup = $elem.data( 'popup');
                var list_wrapper = $elem.find('.ac_poimaps_full_list_post > li');
                var center = new google.maps.LatLng($latFld_set, $lngFld_set); // wspolrzedne polozenia
                if( bw == 1 ){
                    var options = {
                        'center': center,
                        'zoom': zoom,
                        'mapTypeId': google.maps.MapTypeId.ROADMAP,  // typ mapy
                        'styles': [ // style z josona
                            { "stylers": [
                                { "saturation": -100 }
                            ] }
                        ],
                    };
                }else{
                    var options = {
                        'zoom': zoom,
                        'center': center,
                        'mapTypeId': google.maps.MapTypeId.ROADMAP,
                        'scrollwheel': false,
                        'styles': styl_mapy.var_style
                    };
                }
                var map = new google.maps.Map($elem.find('.ac_poi_map_full_map').get(0), options);
                var markers = [];
                var markers_all = [];
                // generowanie listy markerow
                list_wrapper.each(function(){
                    var szer = $(this).data( 'lat');
                    var dlug = $(this).data( 'lng');
                    var ico_data = $(this).data( 'icon');
                    if(ico_data != 'null'){url_data = ico_data;}
                    // jesli sa wspolrzedne geograficzne
                    if (szer != '' && dlug !=''){
                        var latLng = new google.maps.LatLng(szer, dlug);
                        var marker = new google.maps.Marker({
                            'position': latLng,
                            anchor: new google.maps.Point(19,10),
                            icon: url_data
                        });
                        // funkacja klikniecia w marker
                        (function(marker){
                            google.maps.event.addListener(marker, "click", function(event) {
                                if(popup == 1){
                                    ind = markers.indexOf( this );
                                    make_desc(ind);
                                }

                            });
                        })(marker);

                        // wyswietlanie opisu punktu na mapie
                        function make_desc(ind){
                            size_marker_icon(ind);
                        }
                        // dodaje marker do tablicy markerow
                        function size_marker_icon($ind){
                            var ico = list_wrapper.eq($ind).data('icon');
                            if(list_wrapper.eq($ind).data('icon') != null){
                                ico = ico;
                            }else{
                                ico = list_wrapper.eq($ind).data('url');
                            }
                            var img = new Image();
                            var img_h = 0;
                            img.src = ico;
                            img.onload = function() {
                                img_h = this.height;
                                //return img_h;
                                var title = list_wrapper.eq(ind).data('title');
                                var desc = list_wrapper.eq(ind).data('content');
                                infowindow = new google.maps.InfoWindow({
                                    content: '<div style="margin:0 0 0px 0px;" class="ac_poi_map-info-box">\n\<div>'+title+'</div><div>'+desc+'</div></div>',
                                    pixelOffset: new google.maps.Size(0,img_h),
                                    height:300
                                });
                                infowindow.open(map,marker);
                            }
                        }
                        // wyswietlanie opisu punktu na mapie
                        markers.push(marker);
                    }else{
                        //cos poszlo nie tak
                        console.log('error point');
                    }
                });
                markers_all = markers;

                // wlaczenie grupowania markerow
                var markerCluster;
                var markerCluster = new MarkerClusterer(map, markers, { ignoreHidden: true });

                google.maps.event.addListener(map, 'click', function(event) {
                    //markerCluster = new MarkerClusterer(map, markers, { ignoreHidden: true });
                    //markerCluster.clearMarkers();
                });
                //
                //filtr
                //
                var $category = $elem.find('.cat_select');
                var $region = $elem.find('.woj_select');
                reset_filter();
                function reset_filter(){
                    $category.prop('selectedIndex',0);
                    $region.prop('selectedIndex',0);
                }
                $region.change(function() {
                    var var_id = $category.find("option:selected").val();
                    var var_woj = $region.find(" > option:selected").val();
                    wybrane(var_id, var_woj);
                });
                $category.change(function() {
                    var var_id = $category.find("option:selected").val();
                    var var_woj = $region.find(" > option:selected").val();
                    wybrane(var_id, var_woj);
                });
                function wybrane(id, woj){
                    markers = []; // resetuje zawartosc markers
                    // sprawdzanie współrzednych woj
                    $latFld_set = $region.find(" > option:selected").data('lnt');
                    $lngFld_set = $region.find(" > option:selected").data('lng');
                    /*
                     * petla
                     * */
                    var $ile_pkt = 0;
                    var $sum_szer = 0;
                    var $sum_dl = 0;
                    var $lista_pkt = [];
                    list_wrapper.each(function(){
                        var $this = $(this);
                        var szer = $(this).data( 'lat');
                        var dlug = $(this).data( 'lng');
                        var ico_data = $(this).data( 'icon');
                        if(ico_data != 'null'){url_data = ico_data;}
                        // jesli sa wspolrzedne geograficzne
                        var cat_id = $( this ).data( 'catid');
                        var region_name = $( this ).data( 'country');
                        var array_region = region_name.split(',');
                        var array_category = cat_id.split(',');

                        if($.inArray(woj, array_region) != -1 && $.inArray(id, array_category) != -1){
                            //console.log('punkt pasuje');
                        }else{
                            //console.log('punkt nie pasuje');
                        }
                        if($.inArray(woj, array_region) != -1 && $.inArray(id, array_category) != -1){
                            $lista_pkt.push($this);
                            $sum_szer = $sum_szer + szer;
                            $sum_dl = $sum_dl + dlug;
                            $ile_pkt++;
                            if (szer != '' && dlug !=''){
                            var latLng = new google.maps.LatLng(szer, dlug);
                            var marker = new google.maps.Marker({
                                'position': latLng,
                                anchor: new google.maps.Point(19,10),
                                icon: url_data
                            });
                            // funkacja klikniecia w marker
                            (function(marker){
                                google.maps.event.addListener(marker, "click", function(event) {
                                    if(popup == 1){
                                        ind = markers.indexOf( this );
                                        make_desc(ind);
                                    }

                                });
                            })(marker);

                            // wyswietlanie opisu punktu na mapie
                            function make_desc(ind){
                                size_marker_icon(ind);
                            }

                            function size_marker_icon($ind){
                                var ico = list_wrapper.eq($ind).data('icon');
                                if(list_wrapper.eq($ind).data('icon') != null){
                                    ico = ico;
                                }else{
                                    ico = list_wrapper.eq($ind).data('url');
                                }
                                var img = new Image();
                                var img_h = 0;
                                img.src = ico;
                                img.onload = function() {
                                    img_h = this.height;
                                    //return img_h;
                                    var title = $lista_pkt[ind].data('title');
                                    var desc = $lista_pkt[ind].data('content');
                                    infowindow = new google.maps.InfoWindow({
                                        content: '<div style="margin:0 0 0px 0px;" class="ac_poi_map-info-box">\n\<div>'+title+'</div><div>'+desc+'</div></div>',
                                        pixelOffset: new google.maps.Size(0,img_h),
                                        height:300
                                    });
                                    infowindow.open(map,marker);
                                }
                            }
                            // dodaje marker do tablicy markerow
                            markers.push(marker);
                            }
                        }else{
                            //cos poszlo nie tak
                            console.log('error point');
                        }
                    });


                    //wyrysowanei mapy
                    $sum_szer = $sum_szer / $ile_pkt;
                    $sum_dl = $sum_dl / $ile_pkt;
                    if($latFld_set != '' && $lngFld_set != ''){
                        $sum_szer = $latFld_set;
                        $sum_dl = $lngFld_set;
                    }
                    markerCluster.map.setCenter(new google.maps.LatLng( $sum_szer, $sum_dl ) );
                    /*
                    *
                    * ZOOM
                    *
                    * */

                    if(woj == 0){
                        markerCluster.map.setZoom(5);
                    }else{
                        markerCluster.map.setZoom(6);
                    }
                    markerCluster.clearMarkers();
                    markerCluster = new MarkerClusterer(map, markers, { ignoreHidden: true });
                }

                /*
                *
                * end full map
                *
                * */
            }

        });
    });
})(jQuery);