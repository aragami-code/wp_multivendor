jQuery(document).ready(function ($) {
    $('#commission_type').change(function () {
        $('.wcmb_commission_type_fields').hide();
        $($(this).find('option:selected').data('fields')).show();
    }).change();
    $('#wcmb_disbursal_mode_admin').change(function (){
        if($(this).is(':checked')){
            $($(this).data('field')).show();
        } else{
            $($(this).data('field')).hide();
        }
    }).change();
});