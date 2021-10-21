$(document).ready(function () {
    var current;
    current = ("redbean-twig-slim" === location.pathname.split("/")[1]) ? location.pathname.split("/")[3] : location.pathname.split("/")[2];

    $('ul.nav > li.nav-item').each(function () {
        var $this = $(this);
        if ($this.attr('data-group') !== undefined && $this.attr('data-group') == current) {
            $this.addClass('menu-open');
            $this.find('> a.nav-link').addClass('active');
            console.log($this.attr('data-group') + ' - ' + current);
        }

    });
});