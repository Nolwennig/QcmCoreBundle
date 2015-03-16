<?php
namespace Qcm\Bundle\CoreBundle\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use Qcm\Component\Answer\Checker\AnswerCheckerInterface;

/**
 * Class TextChecker
 */
class TextChecker implements AnswerCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(ArrayCollection $answers)
    {
        return array(
            'mapped' => false
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'answer_text';
    }
}
