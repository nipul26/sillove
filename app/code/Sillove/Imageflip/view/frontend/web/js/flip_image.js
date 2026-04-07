require(["jquery"], function ($) {
    $('.product-image-container').hover(function (e) {
        var img = $(this).find('img');
        if (!img.attr('flipurl')) return;
        var src = img.attr('src'),
            flipurl = img.attr('flipurl'),
            flipvalue = img.attr('flipvalue');
        img.removeClass('flip-box-inner-' + flipvalue).addClass('flip-box-back-' + flipvalue);
        img.attr('src', flipurl).attr('flipurl', src);
        if (flipvalue == 'x' || flipvalue == 'y') img.parent().addClass('rotate-' + flipvalue);
    }, function (e) {
        var img = $(this).find('img');
        if (!img.attr('flipurl')) return;
        var src = img.attr('src'),
            flipurl = img.attr('flipurl'),
            flipvalue = img.attr('flipvalue');
        img.attr('flipurl', src).attr('src', flipurl);
        img.addClass('flip-box-inner-' + flipvalue).removeClass('flip-box-back-' + flipvalue);
        img.attr('flipurl', src).attr('src', flipurl);
        if (flipvalue == 'x' || flipvalue == 'y') img.parent().removeClass('rotate-' + flipvalue);
    });
});