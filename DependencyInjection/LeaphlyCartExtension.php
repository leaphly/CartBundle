<?php

namespace Leaphly\CartBundle\DependencyInjection;

use PUGX\GodfatherBundle\DependencyInjection\GodfatherExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class LeaphlyCartExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->loadDbDriver($config, $container, $loader);
        $this->loadServices(array('calculator', 'listener', 'event', 'transition', 'parameter'), $loader);

        $container->setAlias('leaphly_cart.cart.transition', $config['cart_transition']);
        $container->setAlias('leaphly_cart.cart_manager', $config['cart_manager']);
        $container->setAlias('leaphly_cart.product_family_provider', $config['product_family_provider']);

        $this->remapParametersNamespaces($config, $container, array(
            '' => array(
                'model_manager_name' => 'leaphly_cart.model_manager_name',
                'cart_class' => 'leaphly_cart.model.cart.class'
            )
        ));

        $this->registerPriceListener($config, $container);
        $container->setParameter('leaphly_cart.cart.form.name', 'cart');
        $this->loadFormType($container, $loader, $config);
        $this->loadRoles($config, $container);

        $this->defineGodfatherConfiguration($config, $container);
    }

    private function registerPriceListener($config, ContainerBuilder $container)
    {
        if ($config['use_price_listener']) {
            $container->getDefinition('leaphly_cart.listener.price_calculator')->addTag(
                'kernel.event_listener',
                array('method' => 'calculatePrice', 'event'=>\Leaphly\Cart\LeaphlyCartEvents::CART_CREATE_SUCCESS)
            );
            $container->getDefinition('leaphly_cart.listener.price_calculator')->addTag(
                'kernel.event_listener',
                array('method' => 'calculatePrice', 'event'=>\Leaphly\Cart\LeaphlyCartEvents::CART_EDIT_SUCCESS)
            );
            $container->getDefinition('leaphly_cart.listener.price_calculator')->addTag(
                'kernel.event_listener',
                array('method' => 'calculatePrice', 'event'=>\Leaphly\Cart\LeaphlyCartEvents::ITEM_CREATE_SUCCESS)
            );
            $container->getDefinition('leaphly_cart.listener.price_calculator')->addTag(
                'kernel.event_listener',
                array('method' => 'calculatePrice', 'event'=>\Leaphly\Cart\LeaphlyCartEvents::ITEM_DELETE_COMPLETED)
            );
        }
    }

    private function loadRoles($config, ContainerBuilder $container)
    {
        $cartControllerClass = $container->getParameter('leaphly_cart.cart.controller.class');
        $cartHandlerClass = $container->getParameter('leaphly_cart.cart.handler.class');
        $itemControllerClass = $container->getParameter('leaphly_cart.item.controller.class');
        $itemHandlerClass = $container->getParameter('leaphly_cart.item.handler.class');
        $cartFormFactoryHandlerClass = $container->getParameter('leaphly_cart.cart.form.factory.class');
        $defaultCartHandler = 'leaphly_cart.cart.handler';
        $defaultCartItemHandler = 'leaphly_cart.cart_item.handler';

        if (isset($config['roles'])) {
            foreach ($config['roles'] as $roleName => $role) {

                $cartHandlerId = sprintf('leaphly_cart.cart.%s.handler', $roleName);
                $controllerId = sprintf('leaphly_cart.cart.%s.controller', $roleName);
                $formFactoryName = sprintf('leaphly_cart.cart.%s.form.factory', $roleName);
                $cartItemHandlerId = sprintf('leaphly_cart.cart_item.%s.handler', $roleName);
                $controllerCartItemId =sprintf('leaphly_cart.cart_item.%s.controller', $roleName);

                // if the handler is not defined explicitally, register the cart.handler
                if (isset($role['form']) && !isset($role['handler']['cart'])) {
                    $this->registerFormFactory($container, $formFactoryName, $cartFormFactoryHandlerClass, '%leaphly_cart.cart.form.name%',  $role['form']);
                    $this->registerCartHandler($container, $cartHandlerId, $cartHandlerClass, $formFactoryName);
                } elseif (isset($role['handler']['cart'])) {
                    $container->setAlias($cartHandlerId, $role['handler']['cart']);
                }

                // use the cart handler in the controller
                if (!isset($role['controller']['cart'])) {
                    $this->registerController(
                        $container,
                        $controllerId,
                        $cartControllerClass,
                        array(
                            array('setContainer', array(new Reference('service_container'))),
                            array('setCartHandler', array(new Reference($cartHandlerId)))
                        )
                    );
                } else {
                    $container->setAlias($controllerId, $role['controller']['cart']);
                }

                $strategy = isset($role['strategy']) ? $role['strategy'] : sprintf('godfather.%s', $roleName);

                if (isset($role['form']) && !isset($role['handler']['item'])) {
                    $this->registerCartItemHandler($container, $cartItemHandlerId, $itemHandlerClass, $strategy);
                } elseif (isset($role['handler']['item'])) {
                    $container->setAlias($cartItemHandlerId, $role['handler']['item']);
                }

                if (!isset($role['controller']['item'])) {
                    $this->registerController(
                        $container,
                        $controllerCartItemId,
                        $itemControllerClass,
                        array(
                            array('setContainer', array(new Reference('service_container'))),
                            array('setCartHandler', array(new Reference($cartHandlerId))),
                            array('setItemHandler', array(new Reference($cartItemHandlerId)))
                        )
                    );
                } else {
                    $container->setAlias($controllerCartItemId, $role['controller']['item']);
                }

                // setting the default cart hanlder
                if (!$container->hasAlias($defaultCartHandler) || $role['is_default']) {
                    $container->setAlias($defaultCartHandler, $cartHandlerId);
                }
                // setting the default cart hanlder
                if (!$container->hasAlias($defaultCartItemHandler) || $role['is_default']) {
                    $container->setAlias($defaultCartItemHandler, $cartItemHandlerId);
                }

            }
        }
    }

    private function loadDbDriver($config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $loader->load(sprintf('%s.xml', $config['db_driver']));
        $container->setParameter($this->getAlias() . '.backend_type_' . $config['db_driver'], true);
    }

    private function loadServices(array $services, XmlFileLoader $loader)
    {
        foreach ($services as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }
    }

    private function registerController(ContainerBuilder $container, $id, $class, $calls = array())
    {
        $controller = new Definition($class);
        $controller->setMethodCalls($calls);
        $container->setDefinition($id, $controller);
    }

    private function registerService(ContainerBuilder $container, $id, $class, $params = array())
    {
        $arguments = array_map(function ($arg) use ($container) {
            if ($arg[0] == '%' && $arg[strlen($arg) - 1] == '%') {
                return $container->getParameter(str_replace('%', '', $arg));
            } else {
                return new Reference($arg);
            }
        }, $params);
        $service = new Definition($class, $arguments);
        $container->setDefinition($id, $service);
    }

    private function registerFormFactory(ContainerBuilder $container, $formNameId, $cartFormFactoryHandlerClass, $formName, $formType)
    {
        $this->registerService(
            $container,
            $formNameId,
            $cartFormFactoryHandlerClass,
            array(
                'form.factory',
                $formName,
                $formType
            )
        );
    }

    private function registerCartHandler(ContainerBuilder $container, $nameId, $cartHandlerClass, $formFactoryName)
    {
        $this->registerService(
            $container,
            $nameId,
            $cartHandlerClass,
            array(
                'leaphly_cart.cart_manager',
                $formFactoryName,
                'leaphly_cart.cart.transition',
                'event_dispatcher',
                'leaphly_cart.cart_event_factory'
            )
        );
    }

    private function registerCartItemHandler(ContainerBuilder $container, $nameId, $cartItemHandlerClass, $strategy)
    {
        $this->registerService(
            $container,
            $nameId,
            $cartItemHandlerClass,
            array(
                'leaphly_cart.cart_manager',
                'leaphly_cart.product_family_provider',
                $strategy,
                'leaphly_cart.cart.transition',
                'event_dispatcher',
                'leaphly_cart.cart_item.event_factory'
            )
        );
    }

    private function defineGodfatherConfiguration($config, ContainerBuilder $container)
    {
        $godfatherConfiguration = array();

        foreach ($config['roles'] as $roleName => $role) {
            $roleStrategy = array(
                'contexts' => array(
                    'item_handler' => array(
                    ),
                )
            );

            if (isset($role['fallback_strategy'])) {
                $roleStrategy['contexts']['handler']['fallback'] = $role['fallback_strategy'];
            }
            $godfatherConfiguration['godfather'][$roleName] = $roleStrategy;
        }

        $strategy = new GodfatherExtension();
        $strategy->load($godfatherConfiguration, $container);
    }

    private function loadFormType(ContainerBuilder $container, XmlFileLoader $loader, $config)
    {
        $loader->load('form.xml');
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }
}
