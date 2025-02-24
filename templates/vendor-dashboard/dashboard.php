<?php
/*

 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
?>
<div class="col-md-12">
    <?php do_action('wcmb_dashboard_widget', 'full'); ?>
</div>

<div class="col-md-8">
    <?php do_action('wcmb_dashboard_widget', 'normal'); ?>
</div>

<div class="col-md-4">
    <?php do_action('wcmb_dashboard_widget', 'side'); ?>
</div>