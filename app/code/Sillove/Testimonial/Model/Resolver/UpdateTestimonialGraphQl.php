<?php
namespace Sillove\Testimonial\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Sillove\Testimonial\Model\TestimonialFactory;
use Sillove\Testimonial\Model\ResourceModel\Testimonial as TestimonialResource;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Store\Model\StoreManagerInterface;

class UpdateTestimonialGraphQl implements ResolverInterface
{
    /**
     * @var TestimonialFactory
     */
    protected $testimonialFactory;
    /**
     * @var TestimonialResource
     */
    protected $testimonialResource;
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
     * @param TestimonialResource   $testimonialResource
     * @param StoreManagerInterface $storeManager
     * @param File                  $fileDriver
     */
    public function __construct(
        TestimonialFactory $testimonialFactory,
        TestimonialResource $testimonialResource,
        StoreManagerInterface $storeManager,
        File $fileDriver
    ) {
        $this->testimonialFactory = $testimonialFactory;
        $this->testimonialResource = $testimonialResource;
        $this->storeManager = $storeManager;
        $this->fileDriver = $fileDriver;
    }

    /**
     * @inheritdoc
     */
   
    public function resolve($field, $context, $info, array $value = null, array $args = null)
    {
        if (empty($args['input']) || !isset($args['input']['testimonialId'])) {
            throw new GraphQlInputException(__('Input data and Testimonial Id are required to Update .'));
        }

        $input = $args['input'];
        $testimonialId = $input['testimonialId'];
        $testimonial = $this->testimonialFactory->create();
        $this->testimonialResource->load($testimonial, $testimonialId);

        if (!$testimonial->getId()) {
            throw new GraphQlInputException(__('Testimonial with ID %1 does not exist.', $testimonialId));
        }

        isset($input['name']) ? $testimonial->setName($input['name']) : null;
        isset($input['text']) ? $testimonial->setName($input['text']) : null;
        isset($input['company']) ? $testimonial->setName($input['company']) : null;
        isset($input['rating_summary']) ? $testimonial->setName($input['rating_summary']) : null;
        isset($input['stores']) ? $testimonial->setName($input['stores']) : null;
        isset($input['order']) ? $testimonial->setName($input['order']) : null;
        isset($input['status']) ? $testimonial->setName($input['status']) : null;
        if (!empty($input['image_base64'])) {
            try {
                $imagePath = $this->saveBase64Image($input['image_base64']);
                $testimonial->setImage($imagePath);
            } catch (\Exception $e) {
                throw new GraphQlInputException(__('Image upload failed: %1', $e->getMessage()));
            }
        }

        try {
            $this->testimonialResource->save($testimonial);
            return [
                'message' => __('Testimonial updated successfully.')
            ];
        } catch (\Exception $e) {
            throw new GraphQlInputException(__('Error occurred while saving the testimonial: %1', $e->getMessage()));
        }
    }

    /**
     * Save Image of Base64 Format
     *
     * @param  [type] $base64Image
     * @return file
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
