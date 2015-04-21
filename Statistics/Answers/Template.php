<?php

namespace Qcm\Bundle\CoreBundle\Statistics\Answers;

use Qcm\Component\Question\Model\QuestionInterface;
use Qcm\Component\Statistics\Model\TemplateInterface;

/**
 * Class Template
 */
class Template implements TemplateInterface
{
    /**
     * @var QuestionInterface $question
     */
    protected $question;

    /**
     * @var boolean $valid
     */
    protected $valid;

    /**
     * @var boolean $flag
     */
    protected $flag;

    /**
     * Set question
     *
     * @param QuestionInterface $question
     *
     * @return $this
     */
    public function setQuestion(QuestionInterface $question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return QuestionInterface
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set is valid
     *
     * @param boolean $valid
     *
     * @return $this
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Is valid answer
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Set flag
     *
     * @param boolean $flag
     *
     * @return boolean $flag
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * Get flag
     *
     * @return boolean
     */
    public function isFlag()
    {
        return $this->flag;
    }

}
