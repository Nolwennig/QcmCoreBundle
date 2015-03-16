<?php
namespace Qcm\Bundle\CoreBundle\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use Qcm\Component\Answer\Checker\AnswerCheckerInterface;

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
    public function getOptions(ArrayCollection $answers)
    {
        $choices = array();

        foreach ($answers as $answer) {
            $choices[$answer->getId()] = $answer->getValue();
        }

        return array(
            'mapped' => false,
            'choices' => $choices,
            'multiple' => false,
            'expanded' => true
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
