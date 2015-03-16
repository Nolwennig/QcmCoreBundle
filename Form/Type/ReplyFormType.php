<?php

namespace Qcm\Bundle\CoreBundle\Form\Type;

use Qcm\Bundle\CoreBundle\Form\Listener\BuildReplyFormListener;
use Qcm\Bundle\CoreBundle\Question\QuestionInteract;
use Qcm\Component\Answer\Checker\AnswerCheckerLocatorInterface;
use Qcm\Component\Question\Model\QuestionInterface;
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
     * @var QuestionInterface $question
     */
    private $question;

    /**
     * Construct
     *
     * @param AnswerCheckerLocatorInterface $checkerLocator
     * @param QuestionInteract              $questionInteract
     */
    public function __construct(AnswerCheckerLocatorInterface $checkerLocator, QuestionInteract $questionInteract)
    {
        $this->checkerLocator = $checkerLocator;
        $this->question = $questionInteract->getQuestion();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildReplyFormListener($this->checkerLocator, $this->question, $builder->getFormFactory()));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'qcm_public_reply';
    }
}
