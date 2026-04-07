<?php

namespace Custom\CategoryMultiselect\Plugin\Block\Navigation\Renderer;

use Smile\ElasticsuiteCatalog\Block\Navigation\Renderer\Category;

/**
 * Enables checkbox-based multiselect on the category filter renderer.
 */
class CategoryPlugin
{
    /**
     * @param Category $subject
     * @param bool     $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsMultipleSelectEnabled(Category $subject, $result)
    {
        return true;
    }
}
