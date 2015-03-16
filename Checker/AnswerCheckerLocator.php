<?php

namespace Qcm\Bundle\CoreBundle\Checker;

use Qcm\Component\Answer\Checker\AnswerCheckerInterface;
use Qcm\Component\Answer\Checker\AnswerCheckerLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class AnswerCheckerLocator
 */
class AnswerCheckerLocator extends ContainerAware implements AnswerCheckerLocatorInterface
{
    /**
     * @var array $checkers
     */
    private $checkers;

    /**
     * Construct
     *
     * @param array $checkers
     */
    public function __construct(array $checkers)
    {
        $this->checkers = $checkers;
    }

    /**
     * Get checkers list
     *
     * @return array $checkers
     */
    public function getCheckers()
    {
        return $this->checkers;
    }

    /**
     * Is answer checker of type $type known?
     *
     * @param string $type
     *
     * @return boolean
     */
    public function has($type)
    {
        if (!isset($this->checkers[$type])) {
            return false;
        }

        return $this->container->has($this->checkers[$type]);
    }

    /**
     * Get requested Answer Checker
     *
     * @param string $type
     *
     * @return AnswerCheckerInterface
     */
    public function get($type)
    {
        return $this->container->get($this->checkers[$type]);
    }

    /**
     * Get checkers types
     *
     * @return array
     */
    public function getTypes()
    {
        return array_keys($this->checkers);
    }
}
