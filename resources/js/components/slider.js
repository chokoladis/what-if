$(function () {
    $('.category_slider').slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1,
    });
    $('.slider-popular').slick({
        autoplay: true,
        autoplaySpeed: 3500,
        infinite: true,
        dots: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                }
            },
        ],
    });
});