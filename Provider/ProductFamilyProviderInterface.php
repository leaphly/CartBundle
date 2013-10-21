<?php

namespace Leaphly\CartBundle\Provider;

/**
 * ProductManagerInterface given a product identifier should return the family name.
 * It should be implemented in the customer domain.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
interface ProductFamilyProviderInterface
{
    CONST FAMILY_PARAMETER = 'family';
    CONST PRODUCT_ID_PARAMETER = 'product';

    /**
     * Given the response, it should return the product family.
     *
     * @param array $parameters
     *
     * @return string
     */
    public function getProductFamily(array $parameters);

}
