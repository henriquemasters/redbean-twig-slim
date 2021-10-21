function doModal() {
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
                modal.find('.modal-title').text(title);
                $('#modalForm').modal('show');
            } else {
                console.log("Erro: " + xhr.status + ' ' + xhr.statusText);
            }
        });
    });
}