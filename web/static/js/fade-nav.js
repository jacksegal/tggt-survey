//jQuery for page scrolling feature - requires jQuery Easing plugin
$(function () {
    $("form").submit(function (event) {

        /* submit form */
        var bsdFormId = $(this).attr("data-bsd-form-id");
        submitBsdForm(bsdFormId);

        var target = $(this).children('button[type="submit"]').attr('data-href');
        var nextSlide = $(target);
        var previousSlide = $(this).closest("section.slide");

        $(previousSlide).fadeOut("slow", function () {
            $(nextSlide).fadeIn("slow");
        });

        /* disable the click */
        event.preventDefault();
    });
});

