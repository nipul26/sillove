/**
 * Copyright © Magepow. All rights reserved.
 * See COPYING.txt for license details.
 */
var config = {
    config: {
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'Magepow_ConfigurablePreselect/js/configurable': true
            },
            'Magento_Swatches/js/swatch-renderer': {
                'Magepow_ConfigurablePreselect/js/swatch-renderer': true
            }
        }
    }
};
