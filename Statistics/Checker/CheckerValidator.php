<?php

namespace Qcm\Bundle\CoreBundle\Statistics\Checker;

use Qcm\Component\Statistics\Model\Checker\CheckerValidatorInterface;
use Qcm\Component\Statistics\Model\ValidateAnswerCheckerInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class CheckerValidator
 */
class CheckerValidator extends ContainerAware implements CheckerValidatorInterface
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
     * @return ValidateAnswerCheckerInterface
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
