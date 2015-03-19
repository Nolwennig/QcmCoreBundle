<?php

namespace Qcm\Bundle\CoreBundle\Form\Listener;

use Qcm\Bundle\CoreBundle\Question\QuestionInteract;
use Qcm\Component\Answer\Checker\AnswerCheckerInterface;
use Qcm\Component\Answer\Checker\AnswerCheckerLocatorInterface;
use Qcm\Component\Question\Model\QuestionInterface;
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
        /** @var array $configuration */
        $configuration = $event->getData();

        if (null === $configuration || empty($configuration['questions'])) {
            return;
        }

        $this->addAnswersFields($event->getForm(), $this->getQuestionData());
    }

    /**
     * Pre bind
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('answers', $data)) {
            return;
        }

        if (is_string($data['answers'])) {
            $data['answers'] = array($data['answers']);
        }

        $this->addAnswersFields($event->getForm(), $data['answers']);
    }

    /**
     *  Add answers fields
     *
     * @param FormInterface $form
     * @param array         $data
     */
    protected function addAnswersFields(FormInterface $form, $data)
    {
        $question = $this->questionInteract->getQuestion();

        /** @var AnswerCheckerInterface $checker */
        $checker = $this->checkerLocator->get($question->getType());
        $options = $checker->getOptions($question->getAnswers()->getValues(), $data);

        $form->add('answers', $checker->getType(), $options)->setData($data);
    }

    /**
     * Get question data
     *
     * @return array
     */
    private function getQuestionData()
    {
        $configuration = $this->questionInteract->getUserConfiguration();
        $answer = array_search($this->questionInteract->getQuestion()->getId(), $configuration['questions']);
        $data = array();

        if (false !== $answer) {
            $questionId = $configuration['questions'][$answer];
            if (isset($configuration['answers'][$questionId])) {
                $data = $configuration['answers'][$questionId]['data'];
            }
        }

        return $data;
    }
}
