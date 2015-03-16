<?php

namespace Qcm\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Qcm\Component\Answer\Model\AnswerInterface;
use Qcm\Component\Category\Model\CategoryInterface;
use Qcm\Component\Question\Model\QuestionInterface;

/**
 * Class Question
 */
abstract class Question implements QuestionInterface
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var CategoryInterface $category
     */
    protected $category;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var bool $enabled
     */
    protected $enabled;

    /**
     * @var integer $level
     */
    protected $level;

    /**
     * Answers
     *
     * @var Collection|AnswerInterface[]
     */
    protected $answers;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->enabled = true;
        $this->answers = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set question name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get question name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set category
     *
     * @param CategoryInterface|null $category
     *
     * @return $this
     */
    public function setCategory(CategoryInterface $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get question category
     *
     * @return CategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set type of question
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return list of types
     *
     * @return array
     */
    public function getTypes()
    {
        return array(
            self::TYPE_RADIO,
            self::TYPE_CHECKBOX,
            self::TYPE_TEXT
        );
    }

    /**
     * Set enabled
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set level of question
     *
     * @param integer $level
     *
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Get answers associated with this question
     *
     * @return AnswerInterface[]|Collection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Does a answer belongs to question?
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
     * Is there any answers in question?
     *
     * @return bool
     */
    public function hasAnswers()
    {
        return !$this->answers->isEmpty();
    }

    /**
     * Add a answer to question
     *
     * @param AnswerInterface $answer
     *
     * @return $this
     */
    public function addAnswer(AnswerInterface $answer)
    {
        if (! $this->hasAnswer($answer)) {
            $answer->setQuestion($this);
            $this->answers->add($answer);
        }

        return $this;
    }

    /**
     * Remove answer from question
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
}
