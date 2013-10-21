<?php
namespace Leaphly\CartBundle\Provider;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ClassNameProductFamilyProvider,
 * useful when you have Product handled by Object manager with inheritance and the 'family' is the Discriminator Class,
 * or when you want to send via body-post the 'family' name as parameter.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class ClassNameProductFamilyProvider implements ProductFamilyProviderInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param ObjectManager $om
     * @param $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);

        $metadata = $om->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * Get the Product Family from the Request, the family name is the FQCN of the object.
     *
     * @param array $parameters
     *
     * @return null|string
     */
    public function getProductFamily(array $parameters)
    {
        $productRequest = $this->extractProductParametersFromRequest($parameters);

        if (isset($productRequest[ProductFamilyProviderInterface::FAMILY_PARAMETER])) {

            return  $productRequest[ProductFamilyProviderInterface::FAMILY_PARAMETER];
        }

        if ($product = $this->repository->find($productRequest[ProductFamilyProviderInterface::PRODUCT_ID_PARAMETER])) {
           $reflection = new \ReflectionClass($product);

           return $reflection->getShortName();
        }

        return null;
    }

    /**
     * Extract from the body post the product_id or the family.
     *
     * @param array $parameters
     *
     * @return array
     */
    private function extractProductParametersFromRequest($parameters)
    {
        $productRequest = array();

        if(isset($parameters[ProductFamilyProviderInterface::FAMILY_PARAMETER])) {
            $family =  $parameters[ProductFamilyProviderInterface::FAMILY_PARAMETER];
            $productRequest[ProductFamilyProviderInterface::FAMILY_PARAMETER] = $family;
        }
        if(isset($parameters[ProductFamilyProviderInterface::PRODUCT_ID_PARAMETER])) {
            $productId = $parameters[ProductFamilyProviderInterface::PRODUCT_ID_PARAMETER];
            $productRequest[ProductFamilyProviderInterface::PRODUCT_ID_PARAMETER] = $productId;
        }


        return $productRequest;
    }

}
