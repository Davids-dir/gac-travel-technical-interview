section = document.getElementById('iq-sidebar-toggle').children;

if (window.location.href.indexOf("product") > -1) {
    section[1, 2].classList.remove('active');
    section[0].classList.add('active');
} else if (window.location.href.indexOf("category" ) > -1) {
    section[0, 2].classList.remove('active');
    section[1].classList.add('active');
} else {
    section[0, 1].classList.remove('active');
    section[2].classList.add('active');
}