$(document).ready(function () {
    dophoneMask();
});


/**
 *
 * @returns {undefined}
 */
function dophoneMask() {
    $('.phone').focusout(function () {
        var phone, element;
        element = $(this);
        element.unmask();
        phone = element.val().replace(/\D/g, '');
        if (phone.length > 10) {
            element.mask("(99) 99999-999?9");
        } else {
            element.mask("(99) 9999-9999?9");
        }
    }).trigger('focusout');
}

/**
 * 
 * @returns {undefined}
 */
function swalRsvp(title, msg, type) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'theme-btn-s2'
        }
    });
    swalWithBootstrapButtons.fire({
        icon: type,
        title: title,
        html: msg
    });
}