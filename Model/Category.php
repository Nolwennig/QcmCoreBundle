<?php

namespace Qcm\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Qcm\Component\Category\Model\CategoryInterface;
use Qcm\Component\Question\Model\QuestionInterface;

/**
 * Class Category
 */
abstract class Category implements CategoryInterface
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
     * @var string $description
     */
    protected $description;

    /**
     * Questions associated with this category
     *
     * @var Collection|QuestionInterface[]
     */
    protected $questions;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

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
     * Set name of category
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
     * Get name of category
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get questions
     *
     * @return Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Does a question belongs to category?
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
     * Is there any questions in category?
     *
     * @return bool
     */
    public function hasQuestions()
    {
        return !$this->questions->isEmpty();
    }

    /**
     * Add a question to category
     *
     * @param QuestionInterface $question
     *
     * @return $this
     */
    public function addQuestion(QuestionInterface $question)
    {
        if (! $this->hasQuestion($question)) {
            $question->setCategory($this);
            $this->questions->add($question);
        }

        return $this;
    }

    /**
     * Remove question from category
     *
     * @param QuestionInterface $question
     *
     * @return $this
     */
    public function removeQuestion(QuestionInterface $question)
    {
        if ($this->hasQuestion($question)) {
            $this->questions->removeElement($question);
            $question->setCategory(null);
        }

        return $this;
    }
}
