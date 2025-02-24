<?php

/*
 
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMb;
do_action('before_wcmb_vendor_visitors_map');
?>
<div class="vendor_visitors_map">
    <div class="col-sm-4 col-md-3">
        <table id="visitor_data_stats" class="table table-bordered"></table>
    </div>
    <div class="col-sm-8 col-md-9">
        <div id="vmap" style="height: 270px;"></div>
    </div>
</div>
<?php 
do_action('after_wcmb_vendor_visitors_map');