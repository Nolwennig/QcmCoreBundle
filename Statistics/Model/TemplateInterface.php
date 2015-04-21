<?php

namespace Qcm\Bundle\CoreBundle\Statistics\Model;

use Qcm\Component\Question\Model\QuestionInterface;

/**
 * Interface TemplateInterface
 */
interface TemplateInterface
{
    /**
     * Set question
     *
     * @param QuestionInterface $question
     *
     * @return $this
     */
    public function setQuestion(QuestionInterface $question);

    /**
     * Get question
     *
     * @return QuestionInterface
     */
    public function getQuestion();

    /**
     * Set is valid
     *
     * @param boolean $valid
     *
     * @return $this
     */
    public function setValid($valid);

    /**
     * Is valid answer
     *
     * @return boolean
     */
    public function isValid();

    /**
     * Set flag
     *
     * @param boolean $flag
     *
     * @return boolean $flag
     */
    public function setFlag($flag);

    /**
     * Get flag
     *
     * @return boolean
     */
    public function isFlag();
}
