<?php

namespace Qcm\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Qcm\Component\Answer\Model\AnswerInterface;
use Qcm\Component\Category\Model\CategoryInterface;
use Qcm\Component\Question\Model\QuestionInterface;
use Qcm\Component\User\Model\SessionConfigurationInterface;

/**
 * Class UserSession
 */
abstract class UserSessionConfiguration implements SessionConfigurationInterface
{
    /**
     * @var \DateTime|null $datetime
     */
    protected $dateStart;

    /**
     * @var \DateTime|null $datetime
     */
    protected $dateEnd;

    /**
     * @var integer $timeout
     */
    protected $timeout;

    /**
     * integer $timePerQuestion
     */
    protected $timePerQuestion;

    /**
     * @var ArrayCollection $categories
     */
    protected $categories;

    /**
     * @var ArrayCollection $questions
     */
    protected $questions;

    /**
     * @var ArrayCollection $questionLevel
     */
    protected $questionsLevel;

    /**
     * @var ArrayCollection $answers
     */
    protected $answers;

    /**
     * @var integer $maxQuestions
     */
    protected $maxQuestions;

    /**
     * @var \DateTime $startAt
     */
    protected $startAt;

    /**
     * @var \DateTime $endAt
     */
    protected $endAt;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->questionsLevel = array();
        $this->answers = new ArrayCollection();
    }

    /**
     * Set date start
     *
     * @param \DateTime|null $date
     *
     * @return $this
     */
    public function setDateStart(\DateTime $date = null)
    {
        $this->dateStart = $date;

        return $this;
    }

    /**
     * Get date start
     *
     * @return \DateTime|null
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set date end
     *
     * @param \DateTime|null $date
     *
     * @return $this
     */
    public function setDateEnd(\DateTime $date = null)
    {
        $this->dateEnd = $date;

        return $this;
    }

    /**
     * Get date end
     *
     * @return \DateTime|null
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set max questions
     *
     * @param integer $number
     *
     * @return $this
     */
    public function setMaxQuestions($number)
    {
        $this->maxQuestions = $number;

        return $this;
    }

    /**
     * Get max questions
     *
     * @return integer
     */
    public function getMaxQuestions()
    {
        return $this->maxQuestions;
    }

    /**
     * Set timeout
     *
     * @param integer $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get timeout
     *
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set time per question
     *
     * @param integer $timeout
     *
     * @return $this
     */
    public function setTimePerQuestion($timeout)
    {
        $this->timePerQuestion = $timeout;

        return $this;
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
     * Get categories associated with this user session
     *
     * @return CategoryInterface[]|ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Does a category belongs to user session?
     *
     * @param CategoryInterface $category
     *
     * @return Boolean
     */
    public function hasCategory(CategoryInterface $category)
    {
        return $this->categories->contains($category);
    }

    /**
     * Is there any categories in user session?
     *
     * @return bool
     */
    public function hasCategories()
    {
        return !$this->categories->isEmpty();
    }

    /**
     * Add category
     *
     * @param CategoryInterface $category
     *
     * @return $this
     */
    public function addCategory(CategoryInterface $category)
    {
        if (! $this->hasCategory($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    /**
     * Remove category from user session
     *
     * @param CategoryInterface $category
     *
     * @return $this
     */
    public function removeCategory(CategoryInterface $category)
    {
        if ($this->hasCategory($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * Get questions associated with this user session
     *
     * @return AnswerInterface[]|ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Does a question belongs to user session?
     *
     * @param QuestionInterface $question
     *
     * @return Boolean
     */
    public function hasQuestion(QuestionInterface $question)
    {
        return $this->questions->contains($question);
    }

    /**
     * Is there any questions in user session?
     *
     * @return bool
     */
    public function hasQuestions()
    {
        return !$this->questions->isEmpty();
    }

    /**
     * Add question
     *
     * @param QuestionInterface $question
     *
     * @return $this
     */
    public function addQuestion(QuestionInterface $question)
    {
        if (!$this->hasQuestion($question)) {
            $this->questions->add($question);
        }

        return $this;
    }

    /**
     * Remove question from user session
     *
     * @param QuestionInterface $question
     *
     * @return $this
     */
    public function removeQuestion(QuestionInterface $question)
    {
        if ($this->hasQuestion($question)) {
            $this->questions->removeElement($question);
        }

        return $this;
    }

    /**
     * Erase questions
     *
     * @return $this
     */
    public function eraseQuestions()
    {
        $this->questions = new ArrayCollection();

        return $this;
    }

    /**
     * Set questions level
     *
     * @param array $questionsLevel
     *
     * @return $this
     */
    public function setQuestionsLevel($questionsLevel)
    {
        $this->questionsLevel = $questionsLevel;

        return $this;
    }

    /**
     * Get questions level
     *
     * @return array
     */
    public function getQuestionsLevel()
    {
        return $this->questionsLevel;
    }

    /**
     * Get answers associated with this user session
     *
     * @return AnswerInterface[]|ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Does a answer belongs to user session?
     *
     * @param AnswerInterface $answer
     *
     * @return Boolean
     */
    public function hasAnswer(AnswerInterface $answer)
    {
        return $this->answers->contains($answer);
    }

    /**
     * Is there any answer in user session?
     *
     * @return bool
     */
    public function hasAnswers()
    {
        return !$this->answers->isEmpty();
    }

    /**
     * Add answer
     *
     * @param AnswerInterface $answer
     *
     * @return $this
     */
    public function addAnswer(AnswerInterface $answer)
    {
        if (! $this->hasAnswer($answer)) {
            $this->answers->add($answer);
        }

        return $this;
    }

    /**
     * Remove answer from user session
     *
     * @param AnswerInterface $answer
     *
     * @return $this
     */
    public function removeAnswer(AnswerInterface $answer)
    {
        if ($this->hasAnswer($answer)) {
            $this->answers->removeElement($answer);
        }

        return $this;
    }

    /**
     * Set start date
     *
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setStartAt(\DateTime $date)
    {
        $this->startAt = $date;

        return $this;
    }

    /**
     * Get start date
     *
     * @return \Datetime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set end date
     *
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setEndAt(\DateTime $date)
    {
        $this->endAt = $date;

        return $this;
    }

    /**
     * Get end date
     *
     * @return \Datetime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }
}
