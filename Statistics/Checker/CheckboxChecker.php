<?php

namespace Qcm\Bundle\CoreBundle\Statistics\Checker;

use Qcm\Component\Question\Model\QuestionInterface;
use Qcm\Component\Statistics\Model\ScoreInterface;
use Qcm\Component\Statistics\Model\ValidateAnswerCheckerInterface;

/**
 * Class CheckboxChecker
 */
class CheckboxChecker implements ValidateAnswerCheckerInterface
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
        $answersValid = array();

        foreach ($question->getAnswers() as $answer) {
            if ($answer->isValid()) {
                $answersValid[] = $answer->getId();
            }
        }

        $answerCompare = array_diff($answersValid, $data);

        if (empty($answersValid) && !empty($data)) {
            $score->addNotValid();

            return false;
        }

        if (count($answerCompare) === 0) {
            $score->addValid();
        } else if (count($answersValid) === count($answerCompare)) {
            $score->addNotValid();

            return false;
        } else if (count($answersValid) > count($answerCompare)) {
            $score->addPartial();

            return false;
        }

        return true;
    }
}
