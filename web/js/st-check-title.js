//
// 2 functions
// 
// 1 - On error on sibmit form, we remove the upload file
// 2 - Ajax - Check title
//
// IIFE - Immediately Invoked Function Expression
(function(checkTitle) {
    // The global jQuery object is passed as a parameter
    checkTitle(window.jQuery, window, document);
}(function($, window, document) {
    // The $ is now locally scoped 
    // Listen for the jQuery ready event on the document
    $(function() {
        // The DOM is ready!

        //If error on sibmit form, we remove the upload file
        $(".deleteOnLoad").remove();

        // Check the title
        var addTitle = $("#add_title");

        addTitle.on( "blur", function() {
            var invalidTitle = $("#invalid-title");

            invalidTitle.remove();
            addTitle.removeClass("is-invalid");

            var title = $(this).val();

            $.ajax({
                url: ST_check_title_ajax,
                method: "post",
                data: {title: title}
            }).done(function(data){
                var submit = $('#saveTrick');
                console.log(submit);
                addTitle.removeClass("is-invalid");
                submit.prop('disabled', false);

                if (data == 'false') {
                    var response = '<div id="invalid-title" class="invalid-feedback">Le trick existe déjà!</div>';

                    addTitle.parent().append(response);
                    invalidTitle.addClass("d-block");
                    addTitle.addClass("is-invalid");
                    submit.prop('disabled', true);
                }
            });
        });
    });
    // The rest of the code goes here!
}));