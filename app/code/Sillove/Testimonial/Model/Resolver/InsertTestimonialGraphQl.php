<?php
namespace Sillove\Testimonial\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Sillove\Testimonial\Model\TestimonialFactory;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Store\Model\StoreManagerInterface;

class InsertTestimonialGraphQl implements ResolverInterface
{
    /**
     * @var TestimonialFactory
     */
    protected $testimonialFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var File
     */
    protected $fileDriver;

    /**
     * __construct
     *
     * @param TestimonialFactory    $testimonialFactory
     * @param StoreManagerInterface $storeManager
     * @param File                  $fileDriver
     */
    public function __construct(
        TestimonialFactory $testimonialFactory,
        StoreManagerInterface $storeManager,
        File $fileDriver
    ) {
        $this->testimonialFactory = $testimonialFactory;
        $this->storeManager = $storeManager;
        $this->fileDriver = $fileDriver;
    }

    /**
     * @inheritdoc
     */
    public function resolve($field, $context, $info, array $value = null, array $args = null)
    {
        if (empty($args['input'])) {
            throw new GraphQlInputException(__('Input data is required.'));
        }

        $input = $args['input'];
        $testimonial = $this->testimonialFactory->create();
        $testimonial->setName($input['name']);
        $testimonial->setText($input['text'] ?? null);
        $testimonial->setCompany($input['company'] ?? null);
        $testimonial->setRatingSummary($input['rating_summary'] ?? 5);
        $testimonial->setStores($input['stores'] ?? null);
        $testimonial->setOrder($input['order'] ?? null);
        $testimonial->setStatus($input['status'] ?? 1);
        if (!empty($input['image_base64'])) {
            try {
                $imagePath = $this->saveBase64Image($input['image_base64']);
                $testimonial->setImage($imagePath);
            } catch (\Exception $e) {
                throw new GraphQlInputException(__('Image upload failed: %1', $e->getMessage()));
            }
        }
        try {
             $testimonial->save();
            return [
                'message' => __('Testimonial Created Successfully.')
            ];
        } catch (\Exception $e) {
            throw new GraphQlInputException(__('Error occurred while saving the testimonial: %1', $e->getMessage()));
        }
    }

    /**
     * Save the base64 image and return its path
     *
     * @param string $base64Image
     * @return string
     * @throws \Exception
     */
    private function saveBase64Image($base64Image)
    {
        $imageData = explode(',', $base64Image);
        $imageInfo = $imageData[0];
        $imageContent = base64_decode($imageData[1]);// @codingStandardsIgnoreLine
        if (strpos($imageInfo, 'image/jpeg') !== false) {
            $extension = 'jpg';
        } elseif (strpos($imageInfo, 'image/png') !== false) {
            $extension = 'png';
        } else {
            throw new GraphQlInputException(__('Unsupported image format.'));// @codingStandardsIgnoreLine
        }
        $imageName = uniqid('testimonial_') . '.' . $extension;
        $mediaDir = $this->storeManager->getStore()->getBaseMediaDir() . '/testimonial_images';
        if (!$this->fileDriver->isExists($mediaDir)) {
            $this->fileDriver->createDirectory($mediaDir, 0777);
        }
        $imagePath = $mediaDir . '/' . $imageName;
        $this->fileDriver->filePutContents($imagePath, $imageContent);

        return 'testimonial_images/' . $imageName;
    }
}
