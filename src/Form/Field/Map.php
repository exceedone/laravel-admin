<?php

namespace Encore\Admin\Form\Field;

use Encore\Admin\Form\Field;

class Map extends Field
{
    /**
     * Column name.
     *
     * @var array<string, mixed>
     */
    protected $column = [];

    /**
     * Get assets required by this field.
     *
     * @return array<string, mixed>
     */
    public static function getAssets()
    {
        switch (config('admin.map_provider')) {
            case 'tencent':
                $js = '//map.qq.com/api/js?v=2.exp&key='.env('TENCENT_MAP_API_KEY');
                break;
            case 'google':
                $js = '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key='.env('GOOGLE_API_KEY');
                break;
            case 'yandex':
                $js = '//api-maps.yandex.ru/2.1/?lang=ru_RU';
                break;
            default:
                $js = '//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key='.env('GOOGLE_API_KEY');
        }

        return compact('js');
    }

    /**
     * @param mixed $column
     * @param mixed $arguments
     */
    public function __construct($column, $arguments)
    {
        // @phpstan-ignore-next-line The value is always castable to string at runtime
        $this->column['lat'] = (string) $column;
        // @phpstan-ignore-next-line The value is always an array at runtime (and 1 more mixed-type assumption on this line)
        $this->column['lng'] = (string) $arguments[0];

        // @phpstan-ignore-next-line $array is always array at runtime
        array_shift($arguments);

        $this->label = $this->formatLabel($arguments);
        $this->id = $this->formatId($this->column);

        /*
         * Google map is blocked in mainland China
         * people in China can use Tencent map instead(;
         */
        switch (config('admin.map_provider')) {
            case 'tencent':
                $this->useTencentMap();
                break;
            case 'google':
                $this->useGoogleMap();
                break;
            case 'yandex':
                $this->useYandexMap();
                break;
            default:
                $this->useGoogleMap();
        }
    }

    /**
     * @return void
     */
    public function useGoogleMap()
    {
        // Type assertion for PHPStan - maintain original behavior
        /** @var array{lat: string, lng: string} $idArray */
        $idArray = $this->id;
        $latId = $idArray['lat'];
        $lngId = $idArray['lng'];
        
        $this->script = <<<EOT
        (function() {
            function initGoogleMap(name) {
                var lat = $('#{$latId}');
                var lng = $('#{$lngId}');
    
                var LatLng = new google.maps.LatLng(lat.val(), lng.val());
    
                var options = {
                    zoom: 13,
                    center: LatLng,
                    panControl: false,
                    zoomControl: true,
                    scaleControl: true,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
    
                var container = document.getElementById("map_"+name);
                var map = new google.maps.Map(container, options);
    
                var marker = new google.maps.Marker({
                    position: LatLng,
                    map: map,
                    title: 'Drag Me!',
                    draggable: true
                });
    
                google.maps.event.addListener(marker, 'dragend', function (event) {
                    lat.val(event.latLng.lat());
                    lng.val(event.latLng.lng());
                });
            }
    
            initGoogleMap('{$latId}{$lngId}');
        })();
EOT;
    }

    /**
     * @return void
     */
    public function useTencentMap()
    {
        // Type assertion for PHPStan - maintain original behavior
        /** @var array{lat: string, lng: string} $idArray */
        $idArray = $this->id;
        $latId = $idArray['lat'];
        $lngId = $idArray['lng'];
        
        $this->script = <<<EOT
        (function() {
            function initTencentMap(name) {
                var lat = $('#{$latId}');
                var lng = $('#{$lngId}');
    
                var center = new qq.maps.LatLng(lat.val(), lng.val());
    
                var container = document.getElementById("map_"+name);
                var map = new qq.maps.Map(container, {
                    center: center,
                    zoom: 13
                });
    
                var marker = new qq.maps.Marker({
                    position: center,
                    draggable: true,
                    map: map
                });
    
                if( ! lat.val() || ! lng.val()) {
                    var citylocation = new qq.maps.CityService({
                        complete : function(result){
                            map.setCenter(result.detail.latLng);
                            marker.setPosition(result.detail.latLng);
                        }
                    });
    
                    citylocation.searchLocalCity();
                }
    
                qq.maps.event.addListener(map, 'click', function(event) {
                    marker.setPosition(event.latLng);
                });
    
                qq.maps.event.addListener(marker, 'position_changed', function(event) {
                    var position = marker.getPosition();
                    lat.val(position.getLat());
                    lng.val(position.getLng());
                });
            }
    
            initTencentMap('{$latId}{$lngId}');
        })();
EOT;
    }

    /**
     * @return void
     */
    public function useYandexMap()
    {
        // Type assertion for PHPStan - maintain original behavior
        /** @var array{lat: string, lng: string} $idArray */
        $idArray = $this->id;
        $latId = $idArray['lat'];
        $lngId = $idArray['lng'];
        
        $this->script = <<<EOT
        (function() {
            function initYandexMap(name) {
                ymaps.ready(function(){
        
                    var lat = $('#{$latId}');
                    var lng = $('#{$lngId}');
        
                    var myMap = new ymaps.Map("map_"+name, {
                        center: [lat.val(), lng.val()],
                        zoom: 18
                    }); 
    
                    var myPlacemark = new ymaps.Placemark([lat.val(), lng.val()], {
                    }, {
                        preset: 'islands#redDotIcon',
                        draggable: true
                    });
    
                    myPlacemark.events.add(['dragend'], function (e) {
                        lat.val(myPlacemark.geometry.getCoordinates()[0]);
                        lng.val(myPlacemark.geometry.getCoordinates()[1]);
                    });                
    
                    myMap.geoObjects.add(myPlacemark);
                });
    
            }
            
            initYandexMap('{$latId}{$lngId}');
        })();
EOT;
    }
}
