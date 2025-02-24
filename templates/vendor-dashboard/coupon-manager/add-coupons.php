<?php
/**

 */
defined( 'ABSPATH' ) || exit;
global $WCMb;

$post_status = get_post_status( $post->ID );
$title = in_array( $post_status, array( "publish", "draft" ) ) ? $post->post_title : '';
?>
<div class="col-md-12 add-coupon-wrapper">
    <?php do_action( 'before_wcmb_afm_add_coupon_form' ); ?>
    <form id="wcmb-afm-add-coupon" class="woocommerce form-horizontal" method="post">
        <?php do_action( 'wcmb_afm_add_coupon_form_start' ); ?>
        <div class="coupon-primary-info custom-panel">
            <div class="row">
                <div class="col-md-8"> 
                    <div class="form-group-wrapper">
                        <div class="form-group">
                            <label class="control-label col-md-12" for="post_title"><?php esc_html_e( 'Coupon code', 'woocommerce' ); ?></label>
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="post_title" id="post_title" value="<?php esc_attr_e( $title ); ?>">
                                <input type="hidden" name="post_ID" value="<?php esc_attr_e( $self->get_the_id() ); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-12" for="coupon_description"><?php esc_attr_e( 'Description (optional)', 'woocommerce' ); ?></label>
                            <div class="col-md-12">
                                <?php
                                $settings = array(
                                    'textarea_name' => 'coupon_description',
                                    'textarea_rows' => 10,
                                    'quicktags'     => array( 'buttons' => 'em,strong,link' ),
                                    'tinymce'       => array(
                                        'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                                        'theme_advanced_buttons2' => '',
                                    ),
                                    'editor_css'    => '<style>#wp-coupon_description-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
                                );
                                wp_editor( $coupon->get_description( 'edit' ), 'coupon_description', $settings );
                                ?>
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="col-md-4">

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default pannel-outer-heading">
                    <div class="panel-heading">
                        <h3 class="pull-left"><?php esc_html_e( 'Coupon data', 'woocommerce' ); ?></h3>
                    </div>
                    <div class="panel-body panel-content-padding form-group-wrapper">
                        <div id="woocommerce-coupon-data" class="add-coupon-info-holder">   
                            <!-- coupon Info Tab start -->
                            <div class="coupon-info-tab-wrapper" role="tabpanel">
                                <!-- Nav tabs start -->
                                <div class="coupon-tab-nav-holder">
                                    <ul class="nav nav-tabs" role="tablist" id="coupon_data_tabs">
                                        <?php $first_tab = true; ?>
                                        <?php foreach ( $self->get_coupon_data_tabs() as $key => $tab ) : ?>
                                            <li role="presentation" class="<?php esc_attr_e( $key ); ?>_options <?php esc_attr_e( $key ); ?>_tab <?php echo esc_attr( isset( $tab['class'] ) ? implode( ' ', (array) $tab['class'] ) : ''  ); ?> <?php
                                            if ( $first_tab ) {
                                                $first_tab = false;
                                                echo 'active';
                                            }
                                            ?>">
                                                <a href="#<?php esc_attr_e( $tab['target'] ); ?>" aria-controls="<?php echo $tab['target']; ?>" role="tab" data-toggle="tab"><span><?php echo esc_html( $tab['label'] ); ?></span></a>
                                            </li>
                                        <?php endforeach; ?>
                                        <?php do_action( 'wcmb_afm_coupon_write_panel_tabs' ); ?>
                                    </ul>
                                </div>
                                <!-- Nav tabs End -->

                                <!-- Tab content start -->
                                <div class="tab-content">
                                    <?php
                                    $WCMb->template->get_template( 'vendor-dashboard/coupon-manager/views/html-coupon-data-general.php', array( 'self' => $self, 'coupon' => $coupon, 'post' => $post ) );
                                    $WCMb->template->get_template( 'vendor-dashboard/coupon-manager/views/html-coupon-data-usage-restriction.php', array( 'self' => $self, 'coupon' => $coupon, 'post' => $post ) );
                                    $WCMb->template->get_template( 'vendor-dashboard/coupon-manager/views/html-coupon-data-usage-limit.php', array( 'self' => $self, 'coupon' => $coupon, 'post' => $post ) );
                                    ?>
                                    <?php do_action( 'wcmb_afm_coupon_tabs_content' ); ?>
                                </div>
                                <!-- Tab content End -->
                            </div>        
                            <!-- coupon Info Tab End -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="wcmb-action-container">
            <?php
            $primary_action = __( 'Submit', 'MB-multivendor' );    //default value
            if ( current_vendor_can( 'publish_shop_coupons' ) ) {
                if ( $edit_coupon && $post_status === 'publish' ) {
                    $primary_action = __( 'Update', 'MB-multivendor' );
                } else {
                    $primary_action = __( 'Publish', 'MB-multivendor' );
                }
            }
            ?>
            <input type="submit" class="btn btn-default" name="submit-data" value="<?php esc_attr_e( $primary_action ); ?>" id="wcmb_afm_coupon_submit" />
            <input type="submit" class="btn btn-default" name="draft-data" value="<?php esc_attr_e( 'Draft', 'MB-multivendor' ); ?>" id="wcmb_afm_coupon_draft" />
            <input type="hidden" name="status" value="<?php esc_attr_e( $post_status ); ?>">
            <?php wp_nonce_field( 'wcmb-afm-coupon', 'wcmb_afm_coupon_nonce' ); ?>
        </div>
        <?php do_action( 'wcmb_afm_add_coupon_form_end' ); ?>
    </form>
    <?php do_action( 'after_wcmb_afm_add_coupon_form' ); ?>
</div> 
