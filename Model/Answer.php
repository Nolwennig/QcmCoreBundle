<?php

namespace Qcm\Bundle\CoreBundle\Model;

use Qcm\Component\Answer\Model\AnswerInterface;
use Qcm\Component\Question\Model\QuestionInterface;

/**
 * Class Answer
 */
abstract class Answer implements AnswerInterface
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var QuestionInterface $question
     */
    protected $question;

    /**
     * @var string $value
     */
    protected $value;

    /**
     * @var boolean $valid
     */
    protected $valid;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

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
     * Set value
     *
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set is valid
     *
     * @param bool $valid
     *
     * @return $this
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }
}
