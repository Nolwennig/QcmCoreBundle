<?php

namespace Qcm\Bundle\CoreBundle\Form\Listener;

use Qcm\Bundle\CoreBundle\Question\QuestionInteract;
use Qcm\Component\Answer\Checker\AnswerCheckerInterface;
use Qcm\Component\Answer\Checker\AnswerCheckerLocatorInterface;
use Qcm\Component\User\Model\SessionConfigurationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * This listener adds answers form to a question
 */
class BuildReplyFormListener implements EventSubscriberInterface
{
    /**
     * @var AnswerCheckerLocatorInterface $checkerLocator
     */
    private $checkerLocator;

    /**
     * @var QuestionInteract $questionInteract
     */
    private $questionInteract;

    /**
     * @var FormFactoryInterface $factory
     */
    private $factory;

    /**
     * Construct
     *
     * @param AnswerCheckerLocatorInterface $checkerLocator
     * @param QuestionInteract              $questionInteract
     * @param FormFactoryInterface          $factory
     */
    public function __construct(AnswerCheckerLocatorInterface $checkerLocator, QuestionInteract $questionInteract, FormFactoryInterface $factory)
    {
        $this->checkerLocator = $checkerLocator;
        $this->questionInteract = $questionInteract;
        $this->factory = $factory;
    }

    /**
     * Event
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        );
    }

    /**
     * Pre set data
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        /** @var SessionConfigurationInterface $configuration */
        $configuration = $event->getData();

        if (empty($configuration) || empty($configuration) && $event->getForm()->has('answers')) {
            return;
        }

        $this->addAnswersFields($event->getForm());
        $this->addFlag($event);
    }

    /**
     * Pre bind
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data)) {
            $data = array();
        }

        if (!empty($data) && is_string($data['answers'])) {
            $data['answers'] = array($data['answers']);
        }

        $this->addAnswersFields($event->getForm());
        $this->addFlag($event);
    }

    /**
     *  Add answers fields
     *
     * @param FormInterface $form
     */
    protected function addAnswersFields(FormInterface $form)
    {
        $question = $this->questionInteract->getQuestion();
        $data = $this->getQuestionData();

        /** @var AnswerCheckerInterface $checker */
        $checker = $this->checkerLocator->get($question->getType());
        $options = $checker->getOptions($question->getAnswers()->getValues(), $data);
        $form->add('answers', $checker->getType(), $options);
    }

    /**
     * Add flag
     *
     * @param FormEvent $event
     */
    protected function addFlag(FormEvent $event)
    {
        $configuration = $this->questionInteract->getUserConfiguration();
        $data = $this->getQuestionData();

        if (!is_null($configuration->getTimeout()) && $configuration->getQuestions()->count() - count($configuration->getAnswers()) > 0) {
            $event->getForm()->add('flag', 'checkbox', array(
                'label' => 'qcm_core.questions.reply_later',
                'data' => isset($data['flag']) ? $data['flag'] : false
            ));
        }
    }

    /**
     * Get question data
     *
     * @return array
     */
    private function getQuestionData()
    {
        $configuration = $this->questionInteract->getUserConfiguration();
        $data = $configuration->getAnswers()->get($this->questionInteract->getCurrentQuestion());

        return is_null($data) ? array() : $data;
    }
}
