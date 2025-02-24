<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/widget/store-location.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version     0.0.1
 */
extract( $instance );
global $WCMb;

?>
<div class="wcmb-store-location-wrapper">
<?php 
if(!empty($store_lat) && !empty($store_lng)) : ?>
    <div id="store-maps" class="store-maps" class="wcmb-gmap" style="height: 200px;"></div>
    <?php
    wp_add_inline_script( 'wcmb-gmaps-api', 
      '(function ($) {
        var myLatLng = {lat: '.$store_lat.', lng: '.$store_lng.'};
        var map = new google.maps.Map(document.getElementById("store-maps"), {
            zoom: 15,
            center: myLatLng
        });
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            title: "'.$location.'"
        });
    })(jQuery);');
endif; ?>
    <a href="<?php echo $gmaps_link ?>" target="_blank"><?php _e( 'Show in Google Maps', 'MB-multivendor' ) ?></a>
</div>
