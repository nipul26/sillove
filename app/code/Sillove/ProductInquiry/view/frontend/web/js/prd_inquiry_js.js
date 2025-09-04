define(["jquery", "domReady!","prd_inquiry_js"], function($,dom,prd_inquiry_js){
    $('#file-upload').change(function() {
        var filename = $('#file-upload').val();
        //alert(filename);
        // get all file extension list
        var extensionlist = $('.check-extension').text();
        var numbersArray = extensionlist.split(',');
        console.log(numbersArray);
        // get uploaded file extension name
        var fileNameExt = filename.substr(filename.lastIndexOf('.') + 1);
        //alert(fileNameExt);
        var match = 0 ;
        var checkstring = '';
        for (i=0; i < numbersArray.length; i++) {
        	checkstring = numbersArray[i];
        	checkstring = checkstring.replace(/\s/g, '');
        	if(checkstring == fileNameExt)
        	{
        		match = 1;
        	}
        }
        if (match == 0) {
        	var filename = $('#file-upload').val("");
        	$('.attachement-error-msg').text( "Please upload valid extension attachment file." );
			e.preventDefault();
        } else
        {
        	$('.attachement-error-msg').text("");
        }


    });
 //    $(document).on('click', '#inq-submit', function(){
	// 	$("#inq-form").on('submit', function(e) {
	// 		// for Recaptcha Responese
	// 		if($('#allow_attechment').length)
	// 		{
	// 			//lert("attechment");
	// 		}
	//         if($( '#recaptcha' ).length)
	// 		{
	// 			response = grecaptcha.getResponse();
	//         	//alert(response.length);
	// 			if (response.length === 0)
	// 			{
	// 				$('.captcha-error-msg').text( "This is a required field." );
	// 				e.preventDefault();
	// 			}
	// 			else
	// 			{
	// 				$('.captcha-error-msg').css("display","none");
	// 			}
	// 		}
	//    	});
	// });
})
