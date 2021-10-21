$(document).ready(function () {

});

function submitFormLogin() {
    if ($('#show_hide_password input').attr("type") == "text") {
        $('#show_hide_password input').attr('type', 'password');
        $('#show_hide_password i').addClass("fa-eye-slash");
        $('#show_hide_password i').removeClass("fa-eye");
    }
    if ($('input[type="password"]').val())
        $('input[type="password"]').val(hex_md5($('input[type="password"]').val()));
}