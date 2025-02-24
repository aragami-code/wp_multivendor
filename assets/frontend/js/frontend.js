/* global front_end_param */

jQuery(document).ready(function ($) {
    $('#report_abuse').click(function (e) {
        e.preventDefault();
        $('#report_abuse_form').simplePopup();
    });

    $('.submit-report-abuse').on('click', function (e) {
        var inpObjName = document.getElementById("report_abuse_name");
        if (inpObjName.checkValidity() === false) {
            $('#report_abuse_name').next('span').html(inpObjName.validationMessage);
        } else {
            $('#report_abuse_name').next('span').html('');
        }
        var inpObjEmail = document.getElementById("report_abuse_email");
        if (inpObjEmail.checkValidity() === false) {
            $('#report_abuse_email').next('span').html(inpObjEmail.validationMessage);
        } else {
            $('#report_abuse_email').next('span').html('');
        }
        var inpObjMessage = document.getElementById("report_abuse_msg");
        if (inpObjMessage.checkValidity() === false) {
            $('#report_abuse_msg').next('span').html(inpObjMessage.validationMessage);
        } else {
            $('#report_abuse_msg').next('span').html('');
        }
        e.preventDefault();
        var data = {
            action: 'send_report_abuse',
            product_id: $('.report_abuse_product_id').val(),
            name: $('.report_abuse_name').val(),
            email: $('.report_abuse_email').val(),
            msg: $('.report_abuse_msg').val(),
        };
        if (inpObjName.checkValidity() && inpObjEmail.checkValidity() && inpObjMessage.checkValidity()) {
            $.post(frontend_js_script_data.ajax_url, data, function (responsee) {
                $('.simplePopupClose').click();
                $('#report_abuse').css({'box-shadow': 'none', 'cursor': 'default', 'color': '#686868'});
                $('#report_abuse').attr('href', 'javascript:void(0)');
                $('#report_abuse').off('click');
                $('#report_abuse').text(front_end_param.report_abuse_msg);
            });
        }
    });

    $('#wcmb_widget_vendor_search .search_keyword').on('input', function () {

        var vendor_search_data = {
            action: 'vendor_list_by_search_keyword',
            s: $(this).val(),
            vendor_search_nonce: $('#wcmb_vendor_search_nonce').val()
        }

        $.post(frontend_js_script_data.ajax_url, vendor_search_data, function (response) {
            $('#wcmb_widget_vendor_list').html('');
            $('#wcmb_widget_vendor_list').html(response);

        });

    });

    $('#vendor_sort_type').change(function () {
        if ($(this).val() == 'category') {
            $('#vendor_sort_category').show();
        } else {
            $('#vendor_sort_category').hide();
        }
    }).change();
    
    /* Delete Product */
    $('.wcmb_fpm_delete').each(function() {
        $(this).click(function(event) {
            event.preventDefault();
            var rconfirm = confirm(frontend_js_script_data.messages.confirm_dlt_pro);
            if(rconfirm) deleteProduct($(this));
            return false;
        });
    });
	
    function deleteProduct(item) {
        $('.woocommerce').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
        var data = {
            action : 'delete_fpm_product',
            proid : item.data('proid')
        }	
        $.ajax({
            type: 'POST',
            url: frontend_js_script_data.ajax_url,
            data: data,
            success: function(response) {
                if(response) {
                    $response_json = $.parseJSON(response);
                    if($response_json.status == 'success') {
                        window.location = $response_json.shop_url;
                    } else {
                        $('.woocommerce').unblock();
                    }
                } else {
                    $('.woocommerce').unblock();
                }
            }
        });
    }
});