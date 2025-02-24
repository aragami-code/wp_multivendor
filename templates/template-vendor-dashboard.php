<?php
/**
 * The template for displaying vendor dashboard content
 
 */
if (!defined('ABSPATH')) {
    exit;
}
global $WCMb;
$dashboard_scheme = 'wcmb-color-scheme-'.get_wcmb_vendor_settings('vendor_color_scheme_picker', 'vendor', 'dashboard', 'outer_space_blue');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class($dashboard_scheme); ?>>
        
        <?php while (have_posts()) : the_post(); ?>
            <div id="wrapper" class="wcmb-wrapper">
                <?php the_content(); ?>
            </div>
            <?php
        endwhile;
        wp_reset_query();
        
        wp_footer();
        ?>
    </body>
</html>
