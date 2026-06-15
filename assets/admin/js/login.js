$(document).ready(function () {
    $('#FormUser').on('submit', function () {
        submitFormLogin();
    });
});

function submitFormLogin() {
    if ($('#show_hide_password input').attr("type") == "text") {
        $('#show_hide_password input').attr('type', 'password');
        $('#show_hide_password i').addClass("fa-eye-slash");
        $('#show_hide_password i').removeClass("fa-eye");
    }

    return true;
}
