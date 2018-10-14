var swiper = new Swiper('.swiper-container', {
    pagination: {
        el: '.swiper-pagination'
    }
});

time('年','月','号');


function logout() {
    window.location.href = '/index/logout';
}