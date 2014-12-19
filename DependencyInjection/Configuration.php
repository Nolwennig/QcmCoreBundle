<?php

namespace Qcm\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('qcm_core');

        $rootNode
            ->children()
                ->scalarNode('user_class')->isRequired()->cannotBeEmpty()->end()
            ->end();

        $this->addClassesSection($rootNode);

        return $treeBuilder;
    }

    public function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                // Driver used by the resource bundle
                ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()

                // Object manager used by the resource bundle, if not specified "default" will used
                ->scalarNode('manager')->defaultValue('default')->end()

                // Validation groups used by the form component
                ->arrayNode('validation_groups')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('user')->defaultValue('QcmCoreRegistration')->end()
                        ->scalarNode('user_profile')->defaultValue('QcmCoreProfile')->end()
                        ->scalarNode('category')->defaultValue('QcmCoreCategory')->end()
                    ->end()
                ->end()

                // Configure the template namespace used by each resource
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('security')->defaultValue('QcmCoreBundle:Security')->end()
                        ->scalarNode('user')->end()
                    ->end()
                ->end()

                // The resources
                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('security')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->end()
                                ->scalarNode('controller')->defaultValue('Qcm\Bundle\CoreBundle\Controller\SecurityController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Qcm\Bundle\CoreBundle\Form\Type\SecurityFormType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->end()
                                ->scalarNode('controller')->defaultValue('Qcm\Bundle\CoreBundle\Controller\UserController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Qcm\Bundle\CoreBundle\Form\Type\UserFormType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->end()
                                ->scalarNode('controller')->defaultValue('Qcm\Bundle\CoreBundle\Controller\CategoryController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Qcm\Bundle\CoreBundle\Form\Type\CategoryFormType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('question')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->end()
                                ->scalarNode('controller')->defaultValue('Qcm\Bundle\CoreBundle\Controller\QuestionController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Qcm\Bundle\CoreBundle\Form\Type\QuestionFormType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('answer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->end()
                                ->scalarNode('controller')->defaultValue('Qcm\Bundle\CoreBundle\Controller\AnswerController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Qcm\Bundle\CoreBundle\Form\Type\AnswerFormType')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
