$(document).ready(function () {
    var current;
    current = ("redbean-twig-slim" === location.pathname.split("/")[1]) ? location.pathname.split("/")[3] : location.pathname.split("/")[2];

    if (current !== 'profile')
        localStorage.removeItem('profileSlctdTab');
});