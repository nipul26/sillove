define(["jquery"], function($){
    'use strict';

    return function(config, element) {
        $(element).on('change', function(e) {
            if ($(this).attr('type') !== 'file') {
                return;
            }
            var filename = $(this).val();
            // get all file extension list
            var extensionlist = $('.check-extension').text();
            var numbersArray = extensionlist.split(',');
            // get uploaded file extension name
            var fileNameExt = filename.substr(filename.lastIndexOf('.') + 1);
            var match = 0;
            var checkstring = '';
            for (var i = 0; i < numbersArray.length; i++) {
                checkstring = numbersArray[i];
                checkstring = checkstring.replace(/\s/g, '');
                if (checkstring == fileNameExt) {
                    match = 1;
                }
            }
            if (match == 0) {
                $(this).val("");
                $('.attachement-error-msg').text("Please upload valid extension attachment file.");
                e.preventDefault();
            } else {
                $('.attachement-error-msg').text("");
            }
        });
    };
});
