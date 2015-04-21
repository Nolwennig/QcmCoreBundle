<?php

namespace Qcm\Bundle\CoreBundle\Statistics\Checker;

use Qcm\Bundle\CoreBundle\Statistics\Model\ScoreInterface;
use Qcm\Bundle\CoreBundle\Statistics\Model\ValidateAnswerCheckerInterface;
use Qcm\Component\Question\Model\QuestionInterface;

/**
 * Class CheckboxChecker
 */
class ChoiceChecker implements ValidateAnswerCheckerInterface
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
    public function validate($data, QuestionInterface $question, ScoreInterface $score)
    {
        $isValid = true;
        foreach ($question->getAnswers() as $answer) {
            if ($answer->isValid() && array_shift($data) != $answer->getId()) {
                $isValid = false;
                break;
            }
        }

        if (!$isValid) {
            $score->addNotValid();

            return false;
        }

        $score->addValid();

        return true;
    }
}
