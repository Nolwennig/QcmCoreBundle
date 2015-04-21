<?php

namespace Qcm\Bundle\CoreBundle\Statistics\Answers;

use Qcm\Bundle\CoreBundle\Statistics\Model\ScoreInterface;

/**
 * Class Score
 */
final class Score implements ScoreInterface
{
    const VALID = 'valid';
    const PARTIAL = 'partial';
    const NOT_VALID = 'not_valid';

    /**
     * @var array $validationScore
     */
    public static $validationScore = array(
        self::VALID => 0,
        self::PARTIAL => 0,
        self::NOT_VALID => 0
    );

    /**
     * Increment valid answer
     *
     * @return $this
     */
    public function addValid()
    {
        self::$validationScore[self::VALID]++;

        return $this;
    }

    /**
     * Get number of valid answers
     *
     * @return mixed
     */
    public function getValid()
    {
        return self::$validationScore[self::VALID];
    }

    /**
     * Increment partial valid answer
     *
     * @return $this
     */
    public function addPartial()
    {
        self::$validationScore[self::PARTIAL]++;

        return $this;
    }

    /**
     * Get number of partial valid answers
     *
     * @return float
     */
    public function getPartial()
    {
        return self::$validationScore[self::PARTIAL];
    }

    /**
     * Increment not valid answer
     * @return $this
     */
    public function addNotValid()
    {
        self::$validationScore[self::NOT_VALID]++;

        return $this;
    }

    /**
     * Get number of not valid answers
     *
     * @return float
     */
    public function getNotValid()
    {
        return self::$validationScore[self::NOT_VALID];
    }

    /**
     * Get score
     *
     * @return array
     */
    public function getScore()
    {
        return self::$validationScore;
    }
}
