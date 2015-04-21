<?php

namespace Qcm\Bundle\CoreBundle;

use Qcm\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterAnswerCheckersPass;
use Qcm\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterValidationAnswerCheckersPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class QcmCoreBundle
 */
class QcmCoreBundle extends Bundle
{
    /**
     * Return array with currently supported drivers.
     *
     * @return array
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterAnswerCheckersPass());
        $container->addCompilerPass(new RegisterValidationAnswerCheckersPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getBundlePrefix()
    {
        return 'raf_core';
    }
}
