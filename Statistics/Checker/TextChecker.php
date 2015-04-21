<?php

namespace Qcm\Bundle\CoreBundle\Statistics\Checker;

use Qcm\Bundle\CoreBundle\Statistics\Model\ScoreInterface;
use Qcm\Bundle\CoreBundle\Statistics\Model\ValidateAnswerCheckerInterface;
use Qcm\Component\Question\Model\QuestionInterface;

/**
 * Class CheckboxChecker
 */
class TextChecker implements ValidateAnswerCheckerInterface
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
        $value = $question->getAnswers()->first()->getValue();
        $userAnswer = array_shift($data);

        if (!preg_match('#' . $value . '#i', $userAnswer)) {
            $score->addNotValid();

            return false;
        }

        if (strlen($value) !== strlen($userAnswer)) {
            $score->addPartial();

            return false;
        }

        $score->addValid();

        return true;
    }
}
