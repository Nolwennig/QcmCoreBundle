<?php

namespace Qcm\Bundle\CoreBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class QcmCoreExtension extends AbstractResourceExtension
{
    protected $applicationName = 'qcm_core';

    protected $configDirectory = '/../Resources/config/container';

    protected $configFiles = array(
        'services',
        'forms',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS
        );
    }

    /**
     * In case any extra processing is needed.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return array
     */
    protected function process(array $config, ContainerBuilder $container)
    {
        $container->setParameter('qcm_core.website_name', $config['website_name']);
        $container->setParameter('qcm.model.user.class', $config['user_class']);
        $container->setParameter('qcm.configuration', $config['configuration']);
        $container->setParameter('qcm_core.statistics.class', $config['service']['statistics']['class']);
        $container->setParameter('qcm_core.template.class', $config['service']['statistics']['template']);

        $container->setAlias('qcm.configuration', $config['service']['configuration']);
        $container->setAlias('qcm.user_session_configuration', $config['service']['user_session_configuration']);

        return $config;
    }
}
