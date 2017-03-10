/* Submit Form to BSD */
function submitBsdForm(id) {
    var form = getHtmlFormIdentifier(id);
    $.ajax({
        type: "POST",
        data: $(form).serialize(),
        url: "/api/ijqw7dx/signup/" + id,
        dataType: "json",
        success: function (response) {
            console.log(response);
        }
    });
}

/* Build jQuery Identifier for Form */
function getHtmlFormIdentifier(bsdFormId) {
    return 'form[data-bsd-form-id="' + bsdFormId + '"]';
}

/* Handle Custom Submits */
$(function () {

    /* Fill out Hidden Emails */
    $("#pledge form").submit(function (event) {
        var email = $("#inputEmail").val();
        $('input[data-email="true"]').val(email);
    });

    /* Share Text based on Activity */
    $("#activity-appeals form").submit(function () {
        var activity = $('#activity-appeals form input[name="activityOption"]:checked').val();
        $('#share-main h4[data-option="' + activity + '"]').removeClass('hidden');
    });

    /* Get Next Slide based on Activity */
    $('#activity-appeals input:radio').change(function () {
        var target = $(this).attr("data-href");
        $('#activity-appeals button[type="submit"]').attr("data-href", target);
    });

    /* Get Next Slide based on if you have Idea */
    $('#other-something input:radio').change(function () {
        var target = $(this).attr("data-href");
        $('#other-something button[type="submit"]').attr("data-href", target);
    });

    /* Get Share Text for Own Idea */
    $('#other-something form').submit(function () {
        var answer = $('#other-something form input[name="somethingOption"]:checked').val();
        if (answer == 'yes') {
            $('#share-main h4[data-option="all"]').addClass('hidden');
            $('#share-main h4[data-option="ownIdea"]').removeClass('hidden');
        }
    });

    /* Get Share Text for Just Attend */
    $('#other-closest form').submit(function () {
        var answer = $('#other-closest form input[name="closestOption"]:checked').val();
        if (answer == 'Id like to attend something, not organise it') {
            $('#share-main h4[data-option="all"]').addClass('hidden');
        } else {
            $('#share-main h4[data-option="all"]').addClass('hidden');
            $('#share-main h4[data-option="closeIdea"]').removeClass('hidden');
        }
    });


});



