<?php

namespace Leaphly\CartBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Leaphly\CartBundle\DependencyInjection\LeaphlyCartExtension;
use Symfony\Component\Yaml\Parser;

/**
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @package Leaphly\CartBundle\Tests\DependencyInjection
 */
class LeaphlyCartExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerBuilder */
    protected $configuration;

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testCartLoadThrowsExceptionUnlessDatabaseDriverSet()
    {
        $loader = new LeaphlyCartExtension();
        $config = $this->getFullConfigForOneRole();
        unset($config['db_driver']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testCartLoadThrowsExceptionUnlessDatabaseDriverIsValid()
    {
        $loader = new LeaphlyCartExtension();
        $config = $this->getFullConfigForOneRole();
        $config['db_driver'] = 'foo';
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testCartLoadThrowsExceptionUnlessCartModelClassSet()
    {
        $loader = new LeaphlyCartExtension();
        $config = $this->getFullConfigForOneRole();
        unset($config['cart_class']);
        $loader->load(array($config), new ContainerBuilder());
    }

    public function testCartLoadModelClass()
    {
        $this->createFullConfiguration();
        $this->assertParameter('Acme\MyBundle\Entity\Cart', 'leaphly_cart.model.cart.class');
    }


    public function testDefaultCartTransitionWithAlias()
    {
        $this->createFullConfiguration();
        $this->assertAlias('leaphly_cart.cart.finite.transition', 'leaphly_cart.cart.transition');
    }

    public function testCustomCartTransition()
    {
        $config = $this->getOnlyFormConfig();
        $config['cart_transition'] = 'my.custom';
        $this->createConfiguration($config);
        $this->assertNotAlias('leaphly_cart.cart.finite.transition', 'leaphly_cart.cart.transition');
        $this->assertHasDefinition('leaphly_cart.cart.transition');
    }

    public function testCartLoadManagerClass()
    {
        $this->createFullConfiguration();
        $this->assertAlias('acme_my.product_family_provider', 'leaphly_cart.product_family_provider');
    }

    public function testCartHandlerRegistration()
    {
        $this->createConfiguration($this->getOnlyFormConfig());

        $this->assertHasDefinition('leaphly_cart.cart.full.handler');
        $this->assertHasDefinition('leaphly_cart.cart.limited.handler');
    }

    public function testControllersRegistration()
    {
        $this->createConfiguration($this->getOnlyFormConfig());
        $this->assertHasDefinition('leaphly_cart.cart.full.controller');
        $this->assertHasDefinition('leaphly_cart.cart.limited.controller');
    }

    public function testGodFatherAutoConfiguration()
    {
        $this->createConfiguration($this->getFullConfig());

        $this->assertHasDefinition('godfather.full');
        $this->assertHasDefinition('godfather.limited');
    }

    /**
     *  @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testExpectedExceptionWhenMultipleDefaultRole()
    {
        $roleName = 'full';
        $config = $this->getFullConfig();
        $config['roles']['full']['is_default'] = true;
        $config['roles']['limited']['is_default'] = true;
        $this->createConfiguration($config);
    }


    public function testSetDefaultRole()
    {
        $cartHandlerId = 'leaphly_cart.cart.handler';

        $config = $this->getFullConfig();
        $config['roles']['limited']['is_default'] = true;
        $this->createConfiguration($config);

        $this->assertAlias('leaphly_cart.cart.limited.handler', $cartHandlerId);
    }

    public function testIsDefaultRole()
    {
        $cartHandlerId = 'leaphly_cart.cart.handler';

        $config = $this->getFullConfig();
        $config['roles']['full']['is_default'] = true;
        $config['roles']['limited']['is_default'] = false;
        $this->createConfiguration($config);

        $this->assertAlias('leaphly_cart.cart.full.handler', $cartHandlerId);
    }

    public function testAllExplicitRoles()
    {
        $roleName = 'full';
        $cartHandlerId = sprintf('leaphly_cart.cart.%s.handler', $roleName);
        $controllerId = sprintf('leaphly_cart.cart.%s.controller', $roleName);
        $cartItemHandlerId = sprintf('leaphly_cart.cart_item.%s.handler', $roleName);
        $controllerCartItemId =sprintf('leaphly_cart.cart_item.%s.controller', $roleName);

        $this->createConfiguration($this->getFullConfigForOneRole());
        $this->assertAlias('full.controller.cart', $controllerId);
        $this->assertAlias('full.controller.item', $controllerCartItemId);
        $this->assertAlias('full.handler.cart', $cartHandlerId);
        $this->assertAlias('full.handler.item', $cartItemHandlerId);
    }

    public function testControllerShouldBeRegisteredDefiningFormAndHandlersExplicitly()
    {
        $roleName = 'full';
        $cartHandlerId = sprintf('leaphly_cart.cart.%s.handler', $roleName);
        $controllerId = sprintf('leaphly_cart.cart.%s.controller', $roleName);
        $cartItemHandlerId = sprintf('leaphly_cart.cart_item.%s.handler', $roleName);
        $controllerCartItemId =sprintf('leaphly_cart.cart_item.%s.controller', $roleName);

        $config = $this->getFullConfigForOneRole();
        unset($config['roles']['full']['controller']);
        $this->createConfiguration($config);
        $this->assertHasDefinition($controllerId);
        $this->assertHasDefinition($controllerCartItemId);
        $this->assertAlias('full.handler.cart', $cartHandlerId);
        $this->assertAlias('full.handler.item', $cartItemHandlerId);
    }

    public function testHandlersAndControllerShouldBeRegisteredDefiningFormExplicitly()
    {
        $roleName = 'full';
        $cartHandlerId = sprintf('leaphly_cart.cart.%s.handler', $roleName);
        $controllerId = sprintf('leaphly_cart.cart.%s.controller', $roleName);
        $formFactoryName = sprintf('leaphly_cart.cart.%s.form.factory', $roleName);
        $cartItemHandlerId = sprintf('leaphly_cart.cart_item.%s.handler', $roleName);
        $controllerCartItemId =sprintf('leaphly_cart.cart_item.%s.controller', $roleName);

        $config = $this->getFullConfigForOneRole();
        unset($config['roles']['full']['handler']);
        unset($config['roles']['full']['controller']);
        $this->createConfiguration($config);
        $this->assertHasDefinition($controllerId);
        $this->assertHasDefinition($controllerCartItemId);
        $this->assertHasDefinition($cartHandlerId);
        $this->assertHasDefinition($cartItemHandlerId);
    }

    public function testControllerShouldBeRegisteredDefiningHandlersExplicitly()
    {
        $roleName = 'full';
        $cartHandlerId = sprintf('leaphly_cart.cart.%s.handler', $roleName);
        $controllerId = sprintf('leaphly_cart.cart.%s.controller', $roleName);
        $cartItemHandlerId = sprintf('leaphly_cart.cart_item.%s.handler', $roleName);
        $controllerCartItemId =sprintf('leaphly_cart.cart_item.%s.controller', $roleName);

        $config = $this->getFullConfigForOneRole();
        unset($config['roles']['form']);
        unset($config['roles']['full']['controller']);
        $this->createConfiguration($config);
        $this->assertHasDefinition($controllerId);
        $this->assertHasDefinition($controllerCartItemId);
        $this->assertHasDefinition($cartHandlerId);
        $this->assertHasDefinition($cartItemHandlerId);
    }

    /**
     *  @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testWithRolesExpectedException()
    {
        $config = $this->getBaseConfig();
        $this->createConfiguration($config);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testWithoutRolesExpectedException()
    {
        $config = $this->getBaseConfig();
        unset($config['roles']);
        $this->createConfiguration($config);
    }

    /**
     *  @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testNotDefiningFormAndHandlersExceptionShouldBeExpected()
    {

        $config = $this->getFullConfigForOneRole();
        unset($config['roles']['full']['form']);
        unset($config['roles']['full']['handler']);
        $this->createConfiguration($config);
    }

    protected function createFullConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new LeaphlyCartExtension();
        $config = $this->getFullConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    protected function createConfiguration($config)
    {
        $this->configuration = new ContainerBuilder();
        $loader = new LeaphlyCartExtension();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * getBaseConfig
     *
     * @return array
     */
    protected function getBaseConfig()
    {
        return array(
            'db_driver'  =>  'mongodb',
            'cart_class' => 'Acme\MyBundle\Document\Cart',
            'product_family_provider' => 'acme_my.product_family_provider',
            'roles' => array()
        );
    }
    protected function getFullConfig()
    {
        $yaml = <<<EOF
db_driver: orm
cart_class: Acme\MyBundle\Entity\Cart
product_family_provider: acme_my.product_family_provider
roles:
    full:
        form: cart.full_form
        fallback_strategy: fallback.item_handler
        handler:
            cart: cart.full_form
            item: cart.full_form
        controller:
            cart: cart.full_form
            item: cart.full_form
    limited:
       form: cart.limited_form
       fallback_strategy: fallback.item_handler
       handler:
            cart: cart.limited_form
            item: cart.limited_form
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    protected function getOnlyFormConfig()
    {
        $yaml = <<<EOF
db_driver: orm
cart_class: Acme\MyBundle\Entity\Cart
product_family_provider: acme_my.product_family_provider
roles:
    full:
        form: full_access.form.type
    limited:
       form: limited_access.form.type
       strategy: godfather.limited
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }


    protected function getFullConfigForOneRole()
    {
        $yaml = <<<EOF
db_driver: orm
cart_class: Acme\MyBundle\Entity\Cart
product_family_provider: acme_my.product_family_provider
roles:
    full:
        form: full_access.form.type
        handler:
            cart: full.handler.cart
            item: full.handler.item
        controller:
            cart: full.controller.cart
            item: full.controller.item
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    /**
     * @param string $value
     * @param string $key
     */
    private function assertAlias($value, $key)
    {
        $this->assertEquals($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    /**
     * @param string $value
     * @param string $key
     */
    private function assertNotAlias($value, $key)
    {
        $this->assertNotEquals($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    /**
     * @param mixed  $value
     * @param string $key
     */
    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    /**
     * @param string $id
     */
    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    /**
     * @param string $id
     */
    private function assertNotHasDefinition($id)
    {
        $this->assertFalse(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }
}
