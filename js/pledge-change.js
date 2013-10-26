function calculateRemainder() {
    var income     = jQuery("#income").val();
    var saveAmount = jQuery("#sav_amount").val();
    var consAmount = jQuery("#cons_amount").val();
    jQuery("#remainder").val(income - saveAmount - consAmount);
}