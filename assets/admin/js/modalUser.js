function doModalUser() {
    $('body').find('a[rel=modal]').on('click', function (evt) {
        evt.preventDefault();
        var modal = $('#modalForm');
        var title = $(this).attr('title');
        modal.find('.modal-wrap').load($(this).attr('href'), function (responseText, textStatus, xhr) {
            if (textStatus === 'success' || textStatus === 'notmodified') {
                if (modal.find('.modal-wrap #formModal').length) {
                    modal.find('.modal-dialog').addClass('modal-lg');
                    modal.find('.modal-body').css('height', ($(window).height() - 190) + "px");
                    modal.find('.modal-body').css('overflow-y', 'auto');
                } else {
                    modal.find('.modal-dialog').removeClass('modal-lg');
                }
                //
                hideShowPassword();
                submitFormUser();
                //
                modal.find('.modal-title').text(title);
                $('#modalForm').modal('show');
            } else {
                console.log("Erro: " + xhr.status + ' ' + xhr.statusText);
            }
        });
    });
}

function submitFormUser() {
    $('form[name="formModal"]').validate({
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
            pass: 'Obrigatório informar a senha.',
            confirmpassword: 'A senhas não conferem. Por favor tente novamente.'
        },
        submitHandler: function (form) {
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("fa-eye-slash");
                $('#show_hide_password i').removeClass("fa-eye");
            }
            $('#password').val(hex_md5($('#password').val()));
            $('#confirmpassword').val(hex_md5($('#confirmpassword').val()));
            form.submit();
        }
    });
}