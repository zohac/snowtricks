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

    //
    //
    //
        $('.add-another-collection-widget').click(function (e) {
            e.preventDefault();
            var list = $($(this).attr('data-list'));
            // Try to find the counter of the list
            counter = list.data('widget-counter') | list.children().length;
            // If the counter does not exist, use the length of the list
            if (!counter) { counter = list.children().length; }
            // grab the prototype template
            var newWidget = list.attr('data-prototype');
            // replace the "__name__" used in the id and name of the prototype
            // with a number that's unique to your emails
            // end name attribute looks like name="contact[emails][2]"
            newWidget = newWidget.replace(/__name__/g, counter);
            // Increase the counter
            counter++;
            // And store it, the length cannot be used if deleting widgets is allowed
            list.data(' widget-counter', counter);
            // create a new list element and add it to the list
            var newElem = $(list.attr('data-widget-tags')).html(removeButton + newWidget);
    
        /*    if (newElem.find("input").is(":file")) {
                $(newElem).find("input:file").attr("onchange", "readURL(this)");
            }*/
            newElem.appendTo(list);
        });

    //
    //
    //

    });
    // The rest of the code goes here!
    var removeButton = "<button type='button' class='btn btn-danger btn-xs mr-3' onclick='removeFile($(this));'><i class='fa fa-times' aria-hidden='true'></i></button>";
    var counter;
}));

//
// Remove an element.
//
function removeFile(ob) {
    ob.parent().remove();
}

//
// Add thumbnail after change an input type file
//
$(document).on('change', 'input[type=file]', function(){
    readURL(this);
});

function readURL(input) {
    var inputObj = $(input);
    var id = 'src_' + input.id;
    var img = "<img id='" + id + "' class='img-fluid st-input-thumbnail'>";

    $(img).prependTo(inputObj.parent());

    if (input.files && input.files[0]) {
        var reader = new FileReader();
        var inputId = $("#" + id);

        reader.onload = function(e) {
            inputId.attr('src', e.target.result);
            inputObj.addClass('d-none');
            inputObj.parent().children("label").addClass('d-none');
            inputId.hide();
            inputId.fadeIn(650);
        };
        reader.readAsDataURL(input.files[0]);
    }
}