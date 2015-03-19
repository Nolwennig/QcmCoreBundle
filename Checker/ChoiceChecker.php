<?php
namespace Qcm\Bundle\CoreBundle\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use Qcm\Component\Answer\Checker\AnswerCheckerInterface;
use Qcm\Component\Answer\Model\AnswerInterface;

/**
 * Class ChoiceChecker
 */
class ChoiceChecker implements AnswerCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions($answers, $data)
    {
        $choices = array();
        /** @var AnswerInterface $answer */
        foreach ($answers as $answer) {
            $choices[$answer->getId()] = $answer->getValue();
        }

        return array(
            'mapped' => false,
            'choices' => $choices,
            'multiple' => false,
            'expanded' => true,
            'label' => false,
            'label_raw' => true,
            'row_class' => 'col-sm-12',
            'data' => array_shift($data)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'answer_choice';
    }
}
