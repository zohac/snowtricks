//
// 4 functions
//
// 1 - A modal window for trick and comment removal
// 2 - A modal window for video
// 3 - Ajax: Loading comments
// 4 - Media display
//
// IIFE - Immediately Invoked Function Expression
(function(trickSript) {
    // The global jQuery object is passed as a parameter
    trickSript(window.jQuery, window, document);
}(function($, window, document) {
    // The $ is now locally scoped 
    // Listen for the jQuery ready event on the document
    $(function() {
        // The DOM is ready!

    //
    // Modal window for trick and comment removal
    //
        var deleteButton = $("#deleteButton");
        var modalBody = $(".modal-body");

        // Show modal for trick removal
        $("body").on( "click", ".delete", function() {
            var deleteItem = $(this);

            var title = deleteItem.data("title");
            var path = deleteItem.data("path");
            var token = deleteItem.data("token");
            var type = "Suppression d'un " + deleteItem.data("type");
            var issue = "Voulez-vous vraiment supprimer ce " + deleteItem.data("type");

            $(".modal-title").text( type );
            modalBody.find('h2').text( title );
            modalBody.find('p').text( issue );

            $("#deleteModal").modal();

            deleteButton
                .data( "path", path )
                .data( "token", token );
        });
        // Redirect for removal trick
        deleteButton.on('click', function () {
            var path = $(this).data('path');
            document.location.href = path;
        });
/*
    //
    // Modal window for video
    //
        var videoModal = $('#videoModal');

        // Show the modal video
        $(".st-video-modal").on('click', function () {
            var iframe = $(this).data("iframe");

            $( ".st-modal-body div" ).html( iframe );
            videoModal.modal();
        });
        // Remove the iframe on hidden modal
        videoModal.on('hidden.bs.modal', function (e) {
            $("iframe").remove();
        });

    //
    // Ajax: Loading comments
    //
        $("#load-comments").on('click', function() {
            loadCommentAjax(this);
        });

    //
    // Media display
    //
        var media = $('#media');

        media.on('hidden.bs.collapse', function () {
            media.addClass("d-none d-sm-block");
            media.removeClass("collapse");
        });
        media.on('show.bs.collapse', function () {
            media.removeClass("d-none d-sm-block");
        });
*/
    });
}));
