// Sets all options as disabled/enabled depending on the checked attribute of custom_captcha.

jQuery(document).ready(function($) {
    var ids = [
        'cg_glyph_offsetting',
        'cg_glyph_fragments',
        'cg_transformations',
        'cg_approx_shapes',
        'cg_change_degree',
        'cg_split_curve',
        'cg_shapeify'
    ];

    if ($("#custom_captcha").prop("checked")) {
        $.each(ids, function(index, value) {
            $("#" + value).prop("disabled", false);
        });
        $("#captcha_difficulty").attr("disabled", "disabled");
    } else {
        $.each(ids, function(index, value) {
            $("#" + value).attr("disabled", "disabled");
        });
        $("#captcha_difficulty").prop("disabled", false);
    }
    

    // Handle events
    $("#custom_captcha").change(function() {
        if (this.checked) {
            $.each(ids, function(index, value) {
                $("#" + value).prop("disabled", false);
            });
            $("#captcha_difficulty").attr("disabled", "disabled");
        } else {
            $.each(ids, function(index, value) {
                $("#" + value).attr("disabled", "disabled");
            });
            $("#captcha_difficulty").prop("disabled", false);
        }
    });
});


