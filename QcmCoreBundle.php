<?php

namespace Qcm\Bundle\CoreBundle;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
    protected function getBundlePrefix()
    {
        return 'qcm_core';
    }
}
