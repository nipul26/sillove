<?php
namespace Sillove\DynamicHeader\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Data extends AbstractHelper
{
    const XML_PATH_LINKS = 'sillove_header/dynamic_links/links';

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        SerializerInterface $serializer
    ) {
        parent::__construct($context);
        $this->serializer = $serializer;
    }

    /**
     * Get header links configured in admin
     *
     * @return array
     */
    public function getHeaderLinks(): array
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_LINKS,
            ScopeInterface::SCOPE_STORE
        );
        if (!$value) {
            return [];
        }

        try {
            $data = $this->serializer->unserialize($value);
        } catch (\InvalidArgumentException $e) {
            $data = @unserialize($value);
        }

        return is_array($data) ? $data : [];
    }
}