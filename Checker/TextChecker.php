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
    public function getOptions($answers, $data)
    {
        return array(
            'mapped' => false,
            'label' => false,
            'row_class' => 'col-sm-12',
            'data' => array_shift($data)
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
