<?php
/**
 * Copyright © Magepow. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Source model for configurable preselect type options.
 *
 * @category  Magepow
 * @package   Magepow_ConfigurablePreselect
 */
declare(strict_types=1);

namespace Magepow\ConfigurablePreselect\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Provides preselection strategy options for admin configuration.
 */
class PreSelectedOptions implements OptionSourceInterface
{
    /**
     * Retrieve option array for the preselection type dropdown.
     *
     * @return array<int, array{value: string, label: \Magento\Framework\Phrase}>
     */
    public function toOptionArray(): array
    {
        $options = [
            '1' => __('Default Preselected'),
            '2' => __('Highest Preselected Price'),
            '3' => __('Lowest Preselected Price'),
        ];

        $result = [];
        foreach ($options as $key => $value) {
            $result[] = ['value' => $key, 'label' => $value];
        }

        return $result;
    }
}
