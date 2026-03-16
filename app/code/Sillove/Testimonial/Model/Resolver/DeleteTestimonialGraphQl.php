<?php
namespace Sillove\Testimonial\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Sillove\Testimonial\Model\TestimonialFactory;
use Sillove\Testimonial\Model\ResourceModel\Testimonial as TestimonialResource;

class DeleteTestimonialGraphQl implements ResolverInterface
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
     * __construct
     *
     * @param TestimonialFactory  $testimonialFactory
     * @param TestimonialResource $testimonialResource
     */
    public function __construct(
        TestimonialFactory $testimonialFactory,
        TestimonialResource $testimonialResource
    ) {
        $this->testimonialFactory = $testimonialFactory;
        $this->testimonialResource = $testimonialResource;
    }

    /**
     * @inheritdoc
     */
    public function resolve($field, $context, $info, array $value = null, array $args = null)
    {
        if (empty($args['testimonialId'])) {
            throw new GraphQlInputException(__('Testimonial ID is required.'));
        }
        $testimonialId = $args['testimonialId'];
        $testimonial = $this->testimonialFactory->create();
        $this->testimonialResource->load($testimonial, $testimonialId);

        if (!$testimonial->getId()) {
            throw new GraphQlInputException(__('Testimonial with ID %1 does not exist.', $testimonialId));
        }
        try {
            $this->testimonialResource->delete($testimonial);
            return ['message' => __('Testimonial deleted successfully.')];
        } catch (\Exception $e) {
            throw new GraphQlInputException(__('Error occurred while deleting the testimonial: %1', $e->getMessage()));
        }
    }
}
