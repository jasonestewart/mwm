function checkPasswordMatch() {
    var password = jQuery("#pass1").val();
    var confirmPassword = jQuery("#pass2").val();

    if (password != confirmPassword) {
        jQuery("#matchUpdate").html("Passwords do not match!");
	jQuery("#submit").attr("disabled","");
    } else {
        jQuery("#matchUpdate").html("Passwords match.");
	jQuery("#submit").removeAttr("disabled");
    }
}

jQuery(document).ready(function ($) {
    $("#pass2").keyup(checkPasswordMatch);
});