$(document).ready(function () {
    submitFormUser();
});

/**
 * 
 * @param {type} el
 * @param {type} baseUrl
 * @returns {undefined}
 */
function checkValidLogin(el, baseUrl) {
    if (el.val()) {
        var param = {};
        param.login = el.val();

        if (window.location.protocol !== 'http:') {
            baseUrl = (window.location.protocol + '//' + window.location.host);
        }

        $.post(baseUrl + '/check-mail', param, function (r) {
            if (r.id) {
                $('form[id="FormUser"]').find('input[name="id"]').val(r.id);
                $('form[id="FormUser"]').find('input[name="login"]').attr('readonly', true);
                $('form[id="FormUser"]').find('button[type="submit"]').text('Trocar Senha');
                $('#show_hide_password').removeClass('d-none');
            }
        });
    }
}

/**
 * 
 * @returns {undefined}
 */
function submitFormUser() {
    $('#FormUser').validate({
        //  debug: true,
        errorElement: "span",
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        rules: {
            password: "required",
            confirmpassword: {
                equalTo: "#password"
            }
        },
        messages: {
            login: 'Este campo é obrigatório.',
            pass: 'Obrigatório informar a senha.',
            confirmpassword: 'As senhas não conferem. Por favor tente novamente.'
        },
        submitHandler: function (form) {
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("fa-eye-slash");
                $('#show_hide_password i').removeClass("fa-eye");
            }
            form.submit();
        }
    });
}
