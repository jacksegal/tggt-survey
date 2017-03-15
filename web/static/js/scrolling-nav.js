//jQuery to collapse the navbar on scroll
$(window).scroll(function () {
    if ($(".navbar").offset().top > 50) {
        $(".navbar-fixed-top").addClass("top-nav-collapse");
    } else {
        $(".navbar-fixed-top").removeClass("top-nav-collapse");
    }
});

//jQuery for page scrolling feature - requires jQuery Easing plugin
$(function () {
    $("form").submit(function (event) {
        /* submit form */
        var bsdFormId = $(this).attr("data-bsd-form-id");
        submitBsdForm(bsdFormId);

        /* show next slide */
        var target = $(this).children('button[type="submit"]').attr('data-href');
        $(target).removeClass('hidden');

        var previousSlide = $(this).closest("section.slide");

        /* move to next slide */
        var $anchor = $(this).children('button[type="submit"]');
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('data-href')).offset().top
        }, 1500, 'easeInOutExpo', function () {
            $(previousSlide).addClass('hidden');
        });

        /* disable the click */
        event.preventDefault();
    });
});


