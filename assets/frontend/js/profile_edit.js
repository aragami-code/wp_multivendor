jQuery(document).ready(function($) {	
	$('#vendor_image_remove_button').hide();
	$('#vendor_banner_remove_button').hide();
        $('.wcmb_template_list li i.fa').hide();
        $('.vendor-shipping .wcmb_orange_btn, .shop-front .wcmb_orange_btn, .vendor-policies .wcmb_orange_btn, .vendor-billing .wcmb_orange_btn').attr('disabled', 'disabled');
        $('.vendor-shipping .input-group-addon, .shop-front .input-group-addon, .vendor-policies .input-group-addon, .vendor-billing .input-group-addon').hide();
	$('.edit_shop_settings').on( "click", function(e) {
		e.preventDefault();
		$(this).css('display', 'none');
                $(this).before('<span>Edit Mode</span>');
		$('.wcmb_shop_settings_form input[type=text], .wcmb_shop_settings_form textarea, .wcmb_billing_form .select_box').each(function(){
			if($(this).hasClass('no_input')) {
				$(this).removeClass('no_input');
				$(this).attr("readonly", false);
			}
		});
		$('#vendor_image_remove_button').show();
		$('#vendor_banner_remove_button').show();
		$('.green_massenger').each(function(e){$(this).remove();});
		$('.red_massenger').each(function(e){$(this).remove();});
                $('.wcmb_orange_btn').removeAttr('disabled');
                $('.input-group-addon').show();
                $('.wcmb_template_list li i.fa').show();
	});
	$('.edit_policy').on( "click", function(e) {
		e.preventDefault();
		$(this).css('display', 'none');
                $(this).before('<span>Edit Mode</span>');
		$('.wcmb_policy_form input[type=text], .wcmb_policy_form textarea, .wcmb_billing_form .select_box').each(function(){
			if($(this).hasClass('no_input')) {
				$(this).removeClass('no_input');
				$(this).attr("readonly", false);
			}
		});
		$('.green_massenger').each(function(e){$(this).remove();});
                $('.wcmb_orange_btn').removeAttr('disabled');
                $('.input-group-addon').show();
	});
	$('.edit_billing').on( "click", function(e) {
		e.preventDefault();
		$(this).css('display', 'none');
                $(this).before('<span>Edit Mode</span>');
		$('.wcmb_billing_form input[type=text], .wcmb_billing_form textarea, .wcmb_billing_form .select_box').each(function(){
			if($(this).hasClass('no_input')) {
				$(this).removeClass('no_input');
				$(this).attr("readonly", false);
			}
		});
		$('#vendor_payment_mode').removeAttr('disabled');     
		$('#vendor_bank_account_type').removeAttr('disabled');
		$('.green_massenger').each(function(e){$(this).remove();});
                $('.wcmb_orange_btn').removeAttr('disabled');
                $('.input-group-addon').show();
	});
	
	$('.edit_shipping').on( "click", function(e) {
		e.preventDefault();
		$(this).css('display', 'none');
		$(this).before('<span>Edit Mode</span>');
		
		$('.wcmb_shipping_form input[type=text]').each(function(){
			if($(this).hasClass('no_input')) {
				$(this).removeClass('no_input');
				$(this).attr("readonly", false);
			}
		});		
		$('.green_massenger').each(function(e){$(this).remove();});
		$('.red_massenger').each(function(e){$(this).remove();});
                $('.wcmb_orange_btn').removeAttr('disabled');
                $('.input-group-addon').show();
	});
	
});