<?php

namespace Qcm\Bundle\CoreBundle\Form\Type;

use Qcm\Bundle\CoreBundle\Form\Listener\BuildReplyFormListener;
use Qcm\Bundle\CoreBundle\Question\QuestionInteract;
use Qcm\Component\Answer\Checker\AnswerCheckerLocatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ReplyFormType
 */
class ReplyFormType extends AbstractType
{
    /**
     * @var AnswerCheckerLocatorInterface $checkerLocator
     */
    protected $checkerLocator;

    /**
     * @var QuestionInteract $questionInteract
     */
    private $questionInteract;

    /**
     * Construct
     *
     * @param AnswerCheckerLocatorInterface $checkerLocator
     * @param QuestionInteract              $questionInteract
     */
    public function __construct(AnswerCheckerLocatorInterface $checkerLocator, QuestionInteract $questionInteract)
    {
        $this->checkerLocator = $checkerLocator;
        $this->questionInteract = $questionInteract;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('flag', 'checkbox', array(
                'label' => 'qcm_core.questions.reply_later'
            ))
            ->addEventSubscriber(new BuildReplyFormListener($this->checkerLocator, $this->questionInteract, $builder->getFormFactory()));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'qcm_public_reply';
    }
}
