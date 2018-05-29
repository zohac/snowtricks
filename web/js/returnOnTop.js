//
// A function to return on top of html page
//
// IIFE - Immediately Invoked Function Expression
(function(returnOnTop) {
    // The global jQuery object is passed as a parameter
    returnOnTop(window.jQuery, window, document);
}(function($, window, document) {
    // The $ is now locally scoped 
    // Listen for the jQuery ready event on the document
    $(function() {
        // The DOM is ready!
        var returnOnTopID = $('#returnOnTop');

        $(window).scroll(function() {
            //	If on top fade the bouton out, else fade it in
            if ($(window).scrollTop() == 0)  returnOnTopID.fadeOut();
            else returnOnTopID.fadeIn();
        });

        returnOnTopID.on('click', function() {
            $('html,body').animate({scrollTop: 0}, 'slow');
        });
    });
    // The rest of the code goes here!
}));
