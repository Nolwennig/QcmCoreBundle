<?php
namespace Qcm\Bundle\CoreBundle\Statistics\Checker;

use Qcm\Bundle\CoreBundle\Statistics\Model\ValidateAnswerCheckerInterface;

/**
 * Interface CheckerInterface
 */
interface CheckerValidatorInterface
{
    /**
     * Get Known checker types
     *
     * @return string[]
     */
    public function getTypes();

    /**
     * Is answer checker of type $type known?
     *
     * @param string $type
     *
     * @return boolean
     */
    public function has($type);

    /**
     * Get requested Answer Checker
     *
     * @param string $type
     *
     * @return ValidateAnswerCheckerInterface
     */
    public function get($type);
}
