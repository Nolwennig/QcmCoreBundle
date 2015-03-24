<?php

namespace Qcm\Bundle\CoreBundle\Configuration;

use Qcm\Component\Configuration\Model\ConfigurationInterface;

/**
 * Class Configuration
 */
abstract class AbstractConfiguration implements ConfigurationInterface
{
    /**
     * @var integer $questionsMax
     */
    public $questionsMax;

    /**
     * @var integer $questionLevel
     */
    public $questionLevel;

    /**
     * @var integer $answersMax
     */
    public $answersMax;

    /**
     * @var integer $timePerQuestion
     */
    public $timePerQuestion;

    /**
     * @var integer $timeout
     */
    public $timeout;

    /**
     * Construct
     *
     * @param array $configuration
     */
    public function __construct($configuration)
    {
        $this->questionsMax = $configuration['max_questions'];
        $this->questionLevel = $configuration['question_level'];
        $this->answersMax = $configuration['answers_max'];
        $this->timePerQuestion = $configuration['time_per_question'];
        $this->timeout = $configuration['timeout'];
    }

    /**
     * Get max questions
     *
     * @return integer
     */
    public function getQuestionsMax()
    {
        return $this->questionsMax;
    }

    /**
     * Get question level max
     *
     * @return integer
     */
    public function getQuestionLevel()
    {
        return $this->questionLevel;
    }

    /**
     * Get max answers per question
     *
     * @return integer
     */
    public function getAnswersMax()
    {
        return $this->answersMax;
    }

    /**
     * Get time per question
     *
     * @return integer
     */
    public function getTimePerQuestion()
    {
        return $this->timePerQuestion;
    }

    /**
     * Get timeout questionnaire
     *
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }
}
