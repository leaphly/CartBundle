<?php

namespace Leaphly\CartBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Leaphly\CartBundle\DependencyInjection\Compiler\ValidationPass;
use Leaphly\CartBundle\DependencyInjection\Compiler\RegisterMappingsPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;

/**
 * Class LeaphlyCartBundle
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class LeaphlyCartBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ValidationPass());

        $this->addRegisterMappingsPass($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addRegisterMappingsPass(ContainerBuilder $container)
    {
        // the base class is only available since symfony 2.3
        $symfonyVersion = class_exists('Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass');

        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Leaphly\CartBundle\Model',
        );

        if ($symfonyVersion && class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('leaphly_cart.model_manager_name'), 'leaphly_cart.backend_type_orm'));
        } else {
            $container->addCompilerPass(RegisterMappingsPass::createOrmMappingDriver($mappings));
        }

        if ($symfonyVersion && class_exists('Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass')) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, array('leaphly_cart.model_manager_name'), 'leaphly_cart.backend_type_mongodb'));
        } else {
            $container->addCompilerPass(RegisterMappingsPass::createMongoDBMappingDriver($mappings));
        }
    }
}
