/* global wcmb_vendor_shipping_script_data */
(function ($) {
    if (typeof wcmb_vendor_shipping_script_data === 'undefined') {
        return false;
    }
    var app = app || {
        build: function () {
            this.init();
            this.bindEvents();
        },

        init: function () {
            this.modify_shipping_methods = '.modify-shipping-methods';
            this.vendor_shipping_methods = '#vendor-shipping-methods';
            this.shipping_by_zone_holder = '#wcmb_settings_form_shipping_by_zone';
            this.shipping_zone_table = this.shipping_by_zone_holder + ' .shipping-zone-table';
            this.shipping_zone_list = '.shipping-zone-list';
            this.shipping_method_manage_form = '#wcmb_shipping_method_manage_form';
            this.show_shipping_methods = this.vendor_shipping_methods + ' .show-shipping-methods';
            this.add_shipping_methods = this.vendor_shipping_methods + ' .add-shipping-method';
            this.edit_shipping_method = this.vendor_shipping_methods + ' .edit-shipping-method';
            this.update_shipping_method = this.vendor_shipping_methods + ' .update-shipping-method';
            this.delete_shipping_method = this.vendor_shipping_methods + ' .delete-shipping-method';
            this.limit_zone_location = this.vendor_shipping_methods + ' #limit_zone_location';
            this.method_status = this.vendor_shipping_methods + ' .method-status';
            this.modal_close_link = '.modal-close-link';
            this.modal_dialog = '.wcmb-modal-dialog';
        },

        bindEvents: function () {
            $(this.modify_shipping_methods).on('click', this.modifyShippingMethods.bind(this));
            $(document).on('zone_settings_loaded', this.zoneLoadedEvents.bind(this));
            $( document.body ).on( 'change', '.wc-shipping-zone-method-selector select', this.onChangeShippingMethodSelector );
            /* delegate events */
            $(document).delegate(this.shipping_zone_list, 'click', this.goToShippingZones.bind(this));
            $(document).delegate(this.show_shipping_methods, 'click', this.showShippingMethods.bind(this));
            $(document).delegate(this.add_shipping_methods, 'click', this.addShippingMethod.bind(this));
            $(document).delegate(this.edit_shipping_method, 'click', this.editShippingMethod.bind(this));
            $(document).delegate(this.update_shipping_method, 'click', this.updateShippingMethod.bind(this));
            $(document).delegate(this.delete_shipping_method, 'click', this.deleteShippingMethod.bind(this));
            $(document).delegate(this.limit_zone_location, 'click', this.limitZoneLocation.bind(this));
            $(document).delegate(this.method_status, 'change', this.toggleShippingMethod.bind(this));
            $(document).delegate(this.modal_close_link, 'click', this.closeModal.bind(this));
        },

        modifyShippingMethods: function (event, zoneID) {
            var appObj = this;
            $('#wcmb_settings_form_shipping_by_zone').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            
            if (typeof event !== "undefined") {
                event.preventDefault();
                zoneID = $(event.currentTarget).data('zoneId');
            }

            let ajaxRequest = $.ajax({
                method: 'post',
                url: wcmb_vendor_shipping_script_data.ajaxurl,
                data: {
                    action: 'wcmb-get-shipping-methods-by-zone',
                    zoneID: zoneID,
                },
                success: function (response) {
                    $(appObj.vendor_shipping_methods).html(response.data.html).show();
                    $(appObj.shipping_zone_table).hide();
                },
                complete: function () {
                    $('#wcmb_settings_form_shipping_by_zone').unblock();
                    $(document).trigger('zone_settings_loaded');
                }
            });
        },

        zoneLoadedEvents: function (event) {
            this.limitZoneLocation(event);
        },

        goToShippingZones: function (event) {
            event.preventDefault();
            $(this.vendor_shipping_methods).html('').hide();
            $(this.shipping_zone_table).show();
            window.location.reload();
        },
        
        onChangeShippingMethodSelector: function() {
            var description = $( this ).find( 'option:selected' ).data( 'description' );
            $( this ).parents('.wc-shipping-zone-method-selector').find( '.wc-shipping-zone-method-description' ).html( '' );
            $( this ).parents('.wc-shipping-zone-method-selector').find( '.wc-shipping-zone-method-description' ).html( description );
        },

        showShippingMethods: function (event) {
            event.preventDefault();

            /* make popup */
            $('#wcmb_shipping_method_add_container').show();
            $('#wcmb_shipping_method_add_container ' + this.modal_dialog).show();
        },

        addShippingMethod: function (event) {
            event.preventDefault();

            var appObj = this;

            var zoneId = $('#zone_id').val(),
                    shippingMethod = $('#shipping_method option:selected').val();
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

                $('#wcmb_shipping_method_add_button').block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });

                // $('#wcmb_settings_save_button').click();

                var ajaxRequest = $.ajax({
                    method: 'post',
                    url: wcmb_vendor_shipping_script_data.ajaxurl,
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            $('#wcmb_shipping_method_add_container').hide();
                            appObj.modifyShippingMethods(undefined, zoneId);
                        } else {

                        }
                    },
                });
            }
        },

        editShippingMethod: function (event) {
            event.preventDefault();

            var $product_popup_width = '60%',
                    currentTarget = $(event.target).is(this.edit_shipping_method) ? event.target : $(event.target).closest(this.edit_shipping_method),
                    $parents = $(currentTarget).parents('.edit_del_actions');
            var instanceId = $parents.attr('data-instance_id'),
                    zoneId = $('#zone_id').val(),
                    methodId = $parents.attr('data-method_id'),
                    methodSettings = $.parseJSON($parents.attr('data-method-settings'));

            $('#wcmb_shipping_method_edit_container #method_id_selected').val(methodId);
            $('#wcmb_shipping_method_edit_container #instance_id_selected').val(instanceId);

            $('.shipping_form').hide();
            $('#' + methodId).show();
            if (methodId == 'free_shipping') {
                $('#free_shipping #method_title_fs').val(methodSettings.settings.title);
                methodSettings.settings.hasOwnProperty('min_amount')
                        ? $('#free_shipping #minimum_order_amount_fs').val(methodSettings.settings.min_amount)
                        : $('#free_shipping #minimum_order_amount_fs').val('0');
                $('#free_shipping #method_description_fs').val(methodSettings.settings.description);
            }
            if (methodId == 'local_pickup') {
                $('#local_pickup #method_title_lp').val(methodSettings.settings.title);
                $('#local_pickup #method_cost_lp').val(methodSettings.settings.cost);
                $('#local_pickup #method_tax_status_lp option[value=' + methodSettings.settings.tax_status + ']').attr('selected', 'selected');
                $('#local_pickup #method_description_lp').val(methodSettings.settings.description);
            }
            if (methodId == 'flat_rate') {
                $('#flat_rate #method_title_fr').val(methodSettings.settings.title);
                $('#flat_rate #method_cost_fr').val(methodSettings.settings.cost);
                $('#flat_rate #method_tax_status_fr option[value=' + methodSettings.settings.tax_status + ']').attr('selected', 'selected');
                $('#flat_rate #method_description_fr').val(methodSettings.settings.description);
                $('.sc_vals').each(function () {
                    var class_id = $(this).attr('data-shipping_class_id');
                    $(this).val(methodSettings.settings['class_cost_' + class_id]);
                });
                $('#flat_rate #calculation_type').val(methodSettings.settings.calculation_type).trigger('change');
            }

            /* make popup */
            $('#wcmb_shipping_method_edit_container').show();
        },

        updateShippingMethod: function (event) {
            event.preventDefault();

            var appObj = this;

            var methodID = $('#wcmb_shipping_method_edit_container #method_id_selected').val(),
                    instanceId = $('#wcmb_shipping_method_edit_container #instance_id_selected').val(),
                    zoneId = $('#zone_id').val(),
                    data = {
                        action: 'wcmb-update-shipping-method',
                        zoneID: zoneId,
                        args: {
                            instance_id: instanceId,
                            zone_id: zoneId,
                            method_id: methodID,
                            settings: {}
                        }
                    };

            if (methodID == 'free_shipping') {
                data.args.settings.title = $('#free_shipping #method_title_fs').val();
                data.args.settings.description = $('#free_shipping #method_description_fs').val();
                data.args.settings.cost = 0;
                data.args.settings.tax_status = 'none';
                data.args.settings.min_amount = $('#free_shipping #minimum_order_amount_fs').val();
            }
            if (methodID == 'local_pickup') {
                data.args.settings.title = $('#local_pickup #method_title_lp').val();
                data.args.settings.description = $('#local_pickup #method_description_lp').val();
                data.args.settings.cost = $('#local_pickup #method_cost_lp').val();
                data.args.settings.tax_status = $('#local_pickup #method_tax_status_lp option:selected').val();
            }
            if (methodID == 'flat_rate') {
                data.args.settings.title = $('#flat_rate #method_title_fr').val();
                data.args.settings.description = $('#flat_rate #method_description_fr').val();
                data.args.settings.cost = $('#flat_rate #method_cost_fr').val();
                data.args.settings.tax_status = $('#flat_rate #method_tax_status_fr option:selected').val();
                $('.sc_vals').each(function () {
                    data.args.settings['class_cost_' + $(this).attr('data-shipping_class_id')] = $(this).val();
                });
                data.args.settings.calculation_type = $('#flat_rate #calculation_type').val();
            }
            $('#wcmb_shipping_method_edit_button').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            // $('#wcmb_settings_save_button').click();

            var ajaxRequest = $.ajax({
                method: 'post',
                url: wcmb_vendor_shipping_script_data.ajaxurl,
                data: data,
                success: function (response) {
                    if (response.success) {
                        appObj.modifyShippingMethods(undefined, zoneId);
                    } else {
                        alert(resp.data);
                    }
                },
            });
        },

        deleteShippingMethod: function (event) {
            event.preventDefault();

            var appObj = this;

            if (confirm(wcmb_vendor_shipping_script_data.i18n.deleteShippingMethodConfirmation)) {
                var currentTarget = $(event.target).is(this.delete_shipping_method) ? event.target : $(event.target).closest(this.delete_shipping_method),
                        instance_id = $(currentTarget).parents('.edit_del_actions').attr('data-instance_id'),
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
                        url: wcmb_vendor_shipping_script_data.ajaxurl,
                        data: data,
                        success: function (response) {
                            if (response.success) {
                                appObj.modifyShippingMethods(undefined, zoneId);
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

            var appObj = this;

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

        closeModal: function (event) {
            event.preventDefault();

            var appObj = this;

            var modalDialog = $(event.target).parents(appObj.modal_dialog);

            if (modalDialog.length) {
                modalDialog.hide();
                $(this.modal_dialog).hide();
            }
        },
    };

    $(app.build.bind(app));

})(jQuery);