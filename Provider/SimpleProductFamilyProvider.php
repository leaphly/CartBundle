<?php

namespace Leaphly\CartBundle\Provider;

/**
 * Class SimpleProductFamilyProvider, it returns the family given into the response.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class SimpleProductFamilyProvider implements ProductFamilyProviderInterface
{
    /**
     * {inheritdoc}
     */
    public function getProductFamily(array $parameters)
    {
        return $parameters[ProductFamilyProviderInterface::FAMILY_PARAMETER];
    }
}
