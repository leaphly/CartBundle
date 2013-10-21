<?php

namespace Leaphly\CartBundle\Tests\Provider;

use Symfony\Component\HttpFoundation\Request;
use Leaphly\CartBundle\Provider\SimpleProductFamilyProvider;

/**
 * @author Giulio De Donato <liuggio@gmail.com>
 * @package Leaphly\CartBundle\Tests\Provider
 */
class SimpleProductFamilyProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $assertion = 'family_01';
        $parameters = array('family' => $assertion);

        $provider = new SimpleProductFamilyProvider();
        $family = $provider->getProductFamily($parameters);

        $this->assertEquals($family, $assertion);
    }
}
