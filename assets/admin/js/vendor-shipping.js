/* global wcmb_vendor_shipping_script_data, ajaxurl */
(function ($, script_data, wp, ajaxurl) {
    $(function () {
        var app = app || {
            build: function () {
                this.init();
                this.bindEvents();
            },

            init: function () {
                this.table_shipping_zone_settings = '.wcmb-shipping-zone-settings';
                this.table_shipping_zone_methods = '.wcmb-shipping-zone-methods';
                this.modify_shipping_methods = '.modify-shipping-methods';
                this.modal_add_shipping_method = '.wcmb-modal-add-shipping-method-modal';
                this.show_shipping_methods = this.table_shipping_zone_settings + ' .wcmb-shipping-zone-show-method';
                this.add_shipping_methods = this.modal_add_shipping_method + ' .wcmb-shipping-zone-add-method';
                this.edit_shipping_methods = this.table_shipping_zone_settings + ' .edit-shipping-method';
                this.delete_shipping_method = this.table_shipping_zone_settings + ' .delete-shipping-method';
                this.method_status = this.table_shipping_zone_methods + ' .method-status';
                this.limit_zone_location = this.table_shipping_zone_settings + ' #limit_zone_location';
            },

            bindEvents: function () {
                /* events */
                $( document.body ).on( 'click', this.show_shipping_methods, this.showShippingMethods);
                $( document.body ).on( 'wc_backbone_modal_response', this.addShippingMethod );
                $( document.body ).on( 'wc_backbone_modal_response', this.updateShippingMethod );
                $( document.body ).on( 'click', this.edit_shipping_methods, this.editShippingMethod);
                $( document.body ).on( 'click', this.delete_shipping_method, this.deleteShippingMethod);
                $( document.body ).on( 'change', this.method_status, this.toggleShippingMethod);
                $( document.body ).on( 'change', this.limit_zone_location, this.limitZoneLocation);
                $( document.body ).on( 'change', '.wc-shipping-zone-method-selector select', this.onChangeShippingMethodSelector );
                this.limitZoneLocation();
            },

            showShippingMethods: function (event) {
                event.preventDefault();
                var zoneId = $('#zone_id').val();
                $( this ).WCBackboneModal({
                        template : 'wcmb-modal-add-shipping-method',
                        variable : {
                                zone_id : zoneId
                        }
                });

                $( '.wc-shipping-zone-method-selector select' ).change();
            },
            
            onChangeShippingMethodSelector: function() {
                    var description = $( this ).find( 'option:selected' ).data( 'description' );
                    $( this ).parent().find( '.wc-shipping-zone-method-description' ).remove();
                    $( this ).after( '<div class="wc-shipping-zone-method-description">' + description + '</div>' );
                    $( this ).closest( 'article' ).height( $( this ).parent().height() );
            },

            addShippingMethod: function (event, target, posted_data) {
                if ( 'wcmb-modal-add-shipping-method' === target ) {
                    event.preventDefault();
                    var appObj = this;
                    var zoneId = posted_data.zone_id,
                        shippingMethod = posted_data.wcmb_shipping_method;
                    if (zoneId == '') {
                        // alert(wcmb_dashboard_messages.shiping_zone_not_found);
                    } else if (shippingMethod == '') {
                        // alert(wcmb_dashboard_messages.shiping_method_not_selected);
                    } else {
                        var data = {
                            action: 'wcmb-add-shipping-method',
                            zoneID: zoneId,
                            method: shippingMethod
                        };

                        $(this.add_shipping_methods).block({
                            message: null,
                            overlayCSS: {
                                background: '#fff',
                                opacity: 0.6
                            }
                        });

                        // $('#wcmb_settings_save_button').click();

                        var ajaxRequest = $.ajax({
                            method: 'post',
                            url: ajaxurl,
                            data: data,
                            success: function (response) {
                                if (response.success) {
                                    location.reload();
//                                    $('#wcmb_shipping_method_add_container').hide();
//                                    this.modifyShippingMethods(undefined, zoneId);
                                } else {

                                }
                            },
                        });
                    }
                }
                
            },

            editShippingMethod: function (event) {
                event.preventDefault();
                $( '.wcmb-shipping-zone-methods' ).block({
                        message: null,
                        overlayCSS: {
                                background: '#fff',
                                opacity: 0.6
                        }
                });
				
                var instanceId = $(event.currentTarget).data('instance_id'),
                        methodId = $(event.currentTarget).data('method_id'),
                        zoneId = $(event.currentTarget).data('zone_id'),
                        data = {
                            action: 'wcmb-configure-shipping-method',
                            zoneId: zoneId,
                            instanceId: instanceId,
                            methodId: methodId
                        };
                $('#method_id_selected').val(methodId);
                $('#instance_id_selected').val(instanceId);
                
                var ajaxRequest = $.ajax({
                    method: 'post',
                    url: ajaxurl,
                    data: data,
                    success: function (response) {
                        if(response){
                            $( '.wcmb-shipping-zone-methods' ).unblock();
                            /* make popup */
                            $( this ).WCBackboneModal({
                                    template : 'wcmb-modal-update-shipping-method',
                                    variable : {
                                        methodId : methodId,
                                        instanceId : instanceId,
                                        config_settings : response
                                    }
                            });
                        }
                    },
                });
                

            },

            updateShippingMethod: function (event, target, posted_data) {
                if ( 'wcmb-modal-update-shipping-method' === target ) {
                    event.preventDefault();
                    var methodID = posted_data.method_id,
                        instanceId = posted_data.instance_id,
                        zoneId = posted_data.zone_id,
                        data = {
                            action: 'wcmb-update-shipping-method',
                            zoneID: zoneId,
                            posted_data: posted_data,
                            args: {
                                instance_id: instanceId,
                                zone_id: zoneId,
                                method_id: methodID,
                                settings: {}
                            }
                        };
                    if (methodID == 'free_shipping') {
                        data.args.settings.title = posted_data.method_title;
                        data.args.settings.description = posted_data.method_description;
                        data.args.settings.cost = 0;
                        data.args.settings.tax_status = 'none';
                        data.args.settings.min_amount = posted_data.minimum_order_amount;
                    }
                    if (methodID == 'local_pickup') {
                        data.args.settings.title = posted_data.method_title;
                        data.args.settings.description = posted_data.method_description;
                        data.args.settings.cost = posted_data.method_cost;
                        data.args.settings.tax_status = posted_data.method_tax_status;
                    }
                    if (methodID == 'flat_rate') {
                        data.args.settings.title = posted_data.method_title;
                        data.args.settings.description = posted_data.method_description;
                        data.args.settings.cost = posted_data.method_cost;
                        data.args.settings.tax_status = posted_data.method_tax_status;
                        data.args.settings['class_cost_' + posted_data.shipping_class_id] = posted_data.shipping_class_cost;
                        data.args.settings.calculation_type = posted_data.calculation_type;
                    }
                    
                    var ajaxRequest = $.ajax({
                        method: 'post',
                        url: ajaxurl,
                        data: data,
                        success: function (response) {
                            if (response.success) {
                                location.reload();
                                //appObj.modifyShippingMethods(undefined, zoneId);
                            } else {
                                alert(resp.data);
                            }
                        },
                    });
                    
                }
            },

            deleteShippingMethod: function (event) {
                event.preventDefault();

                var appObj = this;

                if (confirm(script_data.i18n.deleteShippingMethodConfirmation)) {
                    var currentTarget = $(event.target).is(this.delete_shipping_method) ? event.target : $(event.target).closest(this.delete_shipping_method),
                            instance_id = $(event.target).attr('data-instance_id'),
                            zoneId = $('#zone_id').val();
                    var data = data = {
                        action: 'wcmb-delete-shipping-method',
                        zoneID: zoneId,
                        instance_id: instance_id
                    };

                    if (zoneId == '') {
                        // alert( wcmb_dashboard_messages.shiping_zone_not_found );
                    } else if (instance_id == '') {
                        // alert( wcmb_dashboard_messages.shiping_method_not_found );
                    } else {
                        // $('#wcmb_settings_save_button').click();

                        var ajaxRequest = $.ajax({
                            method: 'post',
                            url: ajaxurl,
                            data: data,
                            success: function (response) {
                                if (response.success) {
                                    location.reload();
                                    //appObj.modifyShippingMethods(undefined, zoneId);
                                } else {
                                    alert(resp.data);
                                }
                            },
                        });
                    }
                }
            },
            
            limitZoneLocation: function (event) {
                if ($('#limit_zone_location').is(':checked')) {
                    $('.hide_if_zone_not_limited').show();
                } else {
                    $('.hide_if_zone_not_limited').hide();
                }
            },
            
            toggleShippingMethod: function (event) {
                event.preventDefault();

                var checked = $(event.target).is(':checked'),
                        value = $(event.target).val(),
                        zoneId = $('#zone_id').val();

                var data = {
                    action: 'wcmb-toggle-shipping-method',
                    zoneID: zoneId,
                    instance_id: value,
                    checked: checked,
                };

                if (zoneId == '') {
                    // alert( wcmb_dashboard_messages.shiping_zone_not_found );
                } else if (value == '') {
                    // alert( wcmb_dashboard_messages.shiping_method_not_found );
                } else {
                    $('.wcmb-container').block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });

                    var ajaxRequest = $.ajax({
                        method: 'post',
                        url: wcmb_vendor_shipping_script_data.ajaxurl,
                        data: data,
                        success: function (response) {
                            if (response.success) {
                                $('.wcmb-container').unblock();
                            } else {
                                $('.wcmb-container').unblock();
                                alert(response.data);
                            }
                        },
                    });
                }
            },
        };

        $(app.build.bind(app));
    });
})(jQuery, wcmb_vendor_shipping_script_data, wp, ajaxurl);

