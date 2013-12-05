jQuery(document).ready(function($) {
    $("#svgc-reload")
            .click(function() {
                data = {
                    action : 'svgc_captcha_reload', // Same as php callback for wp_ajax_nopriv
                    reload : 'reload'
                };
                $("#SVGCaptchaContainer").load(myAjaxObject.ajaxurl, data, function(response, status, xhr) {
                    if (status == "error") {
                        var msg = "Sorry but there was an error: ";
                        console.log(msg + xhr.status + " " + xhr.statusText);
                    } else {
                        console.log(msg + xhr.status + " " + xhr.statusText);
                    }
                });
            });
});