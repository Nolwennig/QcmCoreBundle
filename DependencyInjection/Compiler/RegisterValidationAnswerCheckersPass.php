<?php

namespace Qcm\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RegisterValidationAnswerCheckersPass
 */
class RegisterValidationAnswerCheckersPass implements CompilerPassInterface
{
    /**
     * Process
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $checkers = array();
        foreach ($container->findTaggedServiceIds('qcm_core.validate_answer_type_checker') as $id => $attributes) {
            $checkers[$attributes[0]['type']] = $id;
        }

        $container->setParameter('qcm_core.validate_answer_checkers', $checkers);
    }
}
