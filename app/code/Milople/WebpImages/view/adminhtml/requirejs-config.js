var config = {
    config: {
        mixins: {
            'Magento_Ui/js/form/element/image-uploader': {
                'Milople_WebpImages/js/form/element/image-uploader-mixin': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Backend/js/media-uploader': 'Milople_WebpImages/js/media-uploader'
        }
    }
};
