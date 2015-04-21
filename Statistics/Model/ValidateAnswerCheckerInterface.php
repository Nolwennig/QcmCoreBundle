<?php

namespace Qcm\Bundle\CoreBundle\Statistics\Model;

use Qcm\Component\Question\Model\QuestionInterface;

/**
 * Class ValidateAnswerCheckerInterface
 */
interface ValidateAnswerCheckerInterface
{
    /**
     * Check answer validation
     *
     * @param array             $data
     * @param QuestionInterface $question
     * @param ScoreInterface    $score
     *
     * @return boolean
     */
    public function validate($data, QuestionInterface $question, ScoreInterface $score);
}
