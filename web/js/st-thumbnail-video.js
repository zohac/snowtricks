//
// A function to return on top of html page
//
// IIFE - Immediately Invoked Function Expression
(function(videoThumbnail) {
    // The global jQuery object is passed as a parameter
    videoThumbnail(window.jQuery, window, document);
}(function($, window, document) {
    // The $ is now locally scoped 
    // Listen for the jQuery ready event on the document
    $(function() {
        // The DOM is ready!
        var vimeoClass = [];
        var numberOfPicture = document.querySelectorAll("#add_video li").length;
    
        for (var i=1; i<numberOfPicture+1; i++) {
            var videoClass = $(".add_video_" + i);
            //URL à récupérer depuis un input par exemple
            var thumbnailUrl = videoClass.data("url");
    
            //On récupère les valeurs de l'url dans un array
            if (match = thumbnailUrl.match(/(?:https?:\/{2})?(?:w{3}.)?youtu(?:be)?.(?:com|be)(?:\/watch\?v=|\/)([^s&]+)/)) {
                thumbnailUrl = 'https://img.youtube.com/vi/' + match[1] + '/hqdefault.jpg';
                videoClass.attr('src', thumbnailUrl);
            }
            if (match = thumbnailUrl.match(/(?:https?:\/{2})?(?:w{3}.dailymotion.com\/video\/)([^s&]+)/)) {
                thumbnailUrl = 'https://www.dailymotion.com/thumbnail/150x120/video/' + match[1];
                videoClass.attr('src', thumbnailUrl);
            }
            if (match = thumbnailUrl.match(/(?:https?:\/{2})?(?:vimeo.com\/)([^s&]+)/)) {
                vimeoClass[thumbnailUrl] = ".add_video_" + i;
                $.getJSON(
                    'https://www.vimeo.com/api/v2/video/' + match[1] + '.json?callback=?',
                    {format: "json"}
                ).done(function(data) {
                        $(vimeoClass[data[0].url]).attr('src', data[0].thumbnail_large);
                });
            }
        }
    });
    // The rest of the code goes here!
}));