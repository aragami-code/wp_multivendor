<?php
    global $WCMb;

    $is_method_taxable_array = array(
        'none'      => __( 'None', 'MB-multivendor' ),
        'taxable'   => __( 'Taxable' , 'MB-multivendor' )
    );

    $calculation_type = array(
        'class' => __( 'Per class: Charge shipping for each shipping class individually', 'MB-multivendor' ),
        'order' => __( 'Per order: Charge shipping for the most expensive shipping class', 'MB-multivendor' ),
    );
?>
<div class="collapse wcmb-modal-dialog" id="wcmb_shipping_method_edit_container">
    <div class="wcmb-modal">
        <div class="wcmb-modal-content">
            <section class="wcmb-modal-main" role="main">
                <header class="wcmb-modal-header page_collapsible modal_head" id="wcmb_shipping_method_edit_general_head">
                    <h1><?php _e( 'Edit Shipping Methods', 'MB-multivendor' ); ?></h1>
                    <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                        <span class="screen-reader-text"><?php _e( 'Close modal panel', 'MB-multivendor' ); ?></span>
                    </button>  
                </header>
                <article class="modal_body" id="wcmb_shipping_method_edit_form_general_body"> 
                    <input id="method_id_selected" class="form-control" type="hidden" name="method_id_selected"> 
                    <input id="instance_id_selected" class="form-control" type="hidden" name="instance_id_selected"> 
                    <div class="shipping_form" id="free_shipping">
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Method Title', 'MB-multivendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="method_title_fs" class="form-control" type="text" name="method_title" placholder="<?php _e( 'Enter method title', 'MB-multivendor' ); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Minimum order amount for free shipping', 'MB-multivendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="minimum_order_amount_fs" class="form-control" type="text" name="minimum_order_amount" placholder="<?php _e( '0.00', 'MB-multivendor' ); ?>">
                            </div>
                        </div>
                        <input type="hidden" id="method_description_fs" name="method_description" value="" />
                        <!--div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Description', 'MB-multivendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <textarea id="method_description_fs" class="form-control" name="method_description"></textarea>
                            </div>
                        </div-->
                    </div>
                    <!-- Local Pickup -->
                    <div class="shipping_form" id="local_pickup">
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Method Title', 'MB-multivendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="method_title_lp" class="form-control" type="text" name="method_title" placholder="<?php _e( 'Enter method title', 'MB-multivendor' ); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Cost', 'MB-multivendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="method_cost_lp" class="form-control" type="text" name="method_cost" placholder="<?php _e( '0.00', 'MB-multivendor' ); ?>">
                            </div>
                        </div>
                        <?php if( apply_filters( 'show_shipping_zone_tax', true ) ) { ?>
                            <div class="form-group">
                                <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Tax Status', 'MB-multivendor' ); ?></label>
                                <div class="col-md-9 col-sm-9">
                                    <select id="method_tax_status_lp" class="form-control" name="method_tax_status">
                                        <?php foreach( $is_method_taxable_array as $key => $value ) { ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <input type="hidden" id="method_description_lp" name="method_description" value="" />
                        <!--div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Description', 'MB-multivendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <textarea id="method_description_lp" class="form-control" name="method_description"></textarea>
                            </div>
                        </div-->
                    </div>
                    
                    <div class="shipping_form" id="flat_rate">
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Method Title', 'MB-multivendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="method_title_fr" class="form-control" type="text" name="method_title" placholder="<?php _e( 'Enter method title', 'MB-multivendor' ); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Cost', 'MB-multivendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <input id="method_cost_fr" class="form-control" type="text" name="method_cost" placholder="<?php _e( '0.00', 'MB-multivendor' ); ?>">
                            </div>
                        </div>
                        <?php if( apply_filters( 'show_shipping_zone_tax', true ) ) { ?>
                            <div class="form-group">
                                <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Tax Status', 'MB-multivendor' ); ?></label>
                                <div class="col-md-9 col-sm-9">
                                    <select id="method_tax_status_fr" class="form-control" name="method_tax_status">
                                        <?php foreach( $is_method_taxable_array as $key => $value ) { ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <input type="hidden" id="method_description_fr" name="method_description" value="" />
                        <!--div class="form-group">
                            <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Description', 'MB-multivendor' ); ?></label>
                            <div class="col-md-9 col-sm-9">
                                <textarea id="method_description_fr" class="form-control" name="method_description"></textarea>
                            </div>
                        </div-->
                    <?php

                        if (!apply_filters( 'hide_vendor_shipping_classes', false )) { ?>
                            <div class="wcmb_shipping_classes">
                                <hr>
                                <h2><?php _e('Shipping Class Cost', 'MB-multivendor'); ?></h2> 
                                <div class="description mb-15"><?php _e('These costs can be optionally entered based on the shipping class set per product( This cost will be added with the shipping cost above).', 'MB-multivendor'); ?></div>
                                <?php
                            
                                // $shipping_classes =  WC()->shipping->get_shipping_classes();
                                $shipping_classes =  get_vendor_shipping_classes();

                                if(empty($shipping_classes)) {
                                    echo '<div class="no_shipping_classes">' . __("No Shipping Classes set by Admin", 'MB-multivendor') . '</div>';
                                } else {
                                    foreach ($shipping_classes as $shipping_class ) {
                                        ?>
                                        <div class="form-group">
                                            <label for="" class="control-label col-sm-3 col-md-3"><?php printf( __( 'Cost of Shipping Class: "%s"', 'MB-multivendor' ), $shipping_class->name ); ?></label>
                                            <div class="col-md-9 col-sm-9">
                                                <input id="<?php echo $shipping_class->slug; ?>" class="form-control sc_vals" type="text" name="shipping_class_cost[]" placholder="<?php _e( 'N/A', 'MB-multivendor' ); ?>" data-shipping_class_id="<?php echo $shipping_class->term_id; ?>">
                                                <div class="description"><?php _e( 'Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'MB-multivendor' ) . '<br/><br/>' . _e( 'Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'MB-multivendor' ); ?></div>
                                            </div>
                                        </div>
                                        <?php 
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label for="" class="control-label col-sm-3 col-md-3"><?php _e( 'Calculation type', 'MB-multivendor' ); ?></label>
                                        <div class="col-md-9 col-sm-9">
                                            <select id="calculation_type" class="form-control" name="calculation_type">
                                                <?php foreach( $calculation_type as $key => $value ) { ?>
                                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                } ?>
                            </div>
                        <?php } ?>
                    </div> 
                    <?php do_action( 'wcmb_vendor_shipping_methods_edit_form_fields', get_current_user_id() ); ?>
                </article>
                <footer class="modal_footer" id="wcmb_shipping_method_edit_general_footer">
                    <div class="inner">
                        <button class="btn btn-default update-shipping-method" id="wcmb_shipping_method_edit_button"><?php _e( 'Save changes', 'MB-multivendor' ); ?></button>
                    </div>
                </footer> 
            </section>   
        </div>
    </div>
    <div class="wcmb-modal-backdrop modal-close"></div>
</div>