<?php

namespace Qcm\Bundle\CoreBundle\Model;

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

    public function __construct()
    {
        $this->enabled = true;
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
     * @param CategoryInterface $category
     *
     * @return $this
     */
    public function setCategory(CategoryInterface $category)
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
}
