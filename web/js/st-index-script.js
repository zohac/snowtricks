//
// 2 functions
//
// 1 - Scroll down to #tricks with effect
// 2 - A modal window for trick removal
//
// IIFE - Immediately Invoked Function Expression
(function(indexScript) {
    // The global jQuery object is passed as a parameter
    indexScript(window.jQuery, window, document);
}(function($, window, document) {
    // The $ is now locally scoped 
    // Listen for the jQuery ready event on the document
    $(function() {
        // The DOM is ready!
/*
        //
        // Scroll down to #tricks
        //
        $('#goToTricks').on('click', function() {
            $('html, body').animate({
                scrollTop: $('#tricks').offset().top
            }, 500, 'linear');
        });
*/
        //
        // Modal window for trick removal
        //
        var bodyElement = $('body');

        // On click, show a modal window
        bodyElement.on('click', '.delete', function () {
            var deleteItem = $(this);
            var title = deleteItem.data("title");
            var path = deleteItem.data("path");

            $(".modal-body h2").text( title );
            $("#deleteModal").modal();
            $("#deleteButton").data( "path", path );
        });
        //Redirection for deleting a trick
        bodyElement.on('click', '#deleteButton', function () {
            var path = $(this).data('path');
            document.location.href = path;
        });
    });
    // The rest of the code goes here!
}));