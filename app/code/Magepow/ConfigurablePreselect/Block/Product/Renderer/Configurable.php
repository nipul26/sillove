<?php
/**
 * Copyright © Magepow. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Configurable product swatch renderer block override.
 * Overrides the swatch renderer template to inject preselect functionality.
 *
 * @category  Magepow
 * @package   Magepow_ConfigurablePreselect
 */
declare(strict_types=1);

namespace Magepow\ConfigurablePreselect\Block\Product\Renderer;

use Magento\Swatches\Block\Product\Renderer\Configurable as SwatchConfigurable;

/**
 * Swatch renderer block with configurable preselect support.
 *
 * Extends the core swatch configurable renderer to use a custom template
 * that passes the preselect configuration to the frontend JS components.
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Configurable extends SwatchConfigurable
{
    /**
     * Path to template file with Swatch renderer for preselect functionality.
     */
    public const SWATCH_RENDERER_TEMPLATE = 'Magepow_ConfigurablePreselect::product/view/renderer.phtml';

    /**
     * Return renderer template.
     *
     * Uses a custom swatch template that includes preselect configuration,
     * falling back to the standard configurable template when no swatch attributes exist.
     * Overridden because PHP `self::` in the parent binds to the parent constant.
     *
     * @return string
     */
    protected function getRendererTemplate(): string
    {
        return $this->isProductHasSwatchAttribute()
            ? self::SWATCH_RENDERER_TEMPLATE
            : parent::CONFIGURABLE_RENDERER_TEMPLATE;
    }
}
