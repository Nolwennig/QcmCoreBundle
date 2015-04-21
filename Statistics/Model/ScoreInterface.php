<?php

namespace Qcm\Bundle\CoreBundle\Statistics\Model;

/**
 * Interface ScoreInterface
 */
interface ScoreInterface
{
    /**
     * Increment valid answer
     *
     * @return $this
     */
    public function addValid();

    /**
     * Get number of valid answers
     *
     * @return mixed
     */
    public function getValid();

    /**
     * Increment partial valid answer
     *
     * @return $this
     */
    public function addPartial();

    /**
     * Get number of partial valid answers
     *
     * @return float
     */
    public function getPartial();

    /**
     * Increment not valid answer
     * @return $this
     */
    public function addNotValid();

    /**
     * Get number of not valid answers
     *
     * @return float
     */
    public function getNotValid();

    /**
     * Get score
     *
     * @return array
     */
    public function getScore();
}
