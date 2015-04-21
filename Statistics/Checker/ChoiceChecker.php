<?php

namespace Qcm\Bundle\CoreBundle\Statistics\Checker;

use Qcm\Component\Question\Model\QuestionInterface;
use Qcm\Component\Statistics\Model\ScoreInterface;
use Qcm\Component\Statistics\Model\ValidateAnswerCheckerInterface;

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
