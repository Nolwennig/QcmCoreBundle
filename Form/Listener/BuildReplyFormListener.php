<?php

namespace Qcm\Bundle\CoreBundle\Form\Listener;

use Doctrine\Common\Collections\ArrayCollection;
use Qcm\Component\Answer\Checker\AnswerCheckerLocatorInterface;
use Qcm\Component\Answer\Model\AnswerInterface;
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
     * @var QuestionInterface $question
     */
    private $question;

    /**
     * @var FormFactoryInterface $factory
     */
    private $factory;

    /**
     * Construct
     *
     * @param AnswerCheckerLocatorInterface $checkerLocator
     * @param QuestionInterface             $question
     * @param FormFactoryInterface          $factory
     */
    public function __construct(AnswerCheckerLocatorInterface $checkerLocator, QuestionInterface $question, FormFactoryInterface $factory)
    {
        $this->checkerLocator = $checkerLocator;
        $this->question = $question;
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
        /** @var QuestionInterface $configuration */
        $configuration = $event->getData();

        if (null === $configuration || empty($configuration['questions'])) {
            return;
        }

        $this->addAnswersFields($event->getForm(), $configuration);
    }

    /**
     * Pre bind
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (empty($data) || !array_key_exists('type', $data)) {
            return;
        }

        $this->addAnswersFields($event->getForm(), $data['type']);
    }

    /**
     *  Add answers fields
     *
     * @param FormInterface $form
     * @param array         $configuration
     */
    protected function addAnswersFields(FormInterface $form, $configuration)
    {
        /** @var ArrayCollection[AnswerInterface] $answers */
        $answers = $this->question->getAnswers();

        $form->add('answers', $this->question->getType(), $options);
    }

    /**
     * Get answer data
     *
     * @param AnswerInterface $answer
     * @param array           $configuration
     *
     * @return array
     */
    private function getAnswerData(AnswerInterface $answer, $configuration)
    {
        if (!isset($configuration['answers'][$answer->getId()])) {
            return array();
        }

        //array('choices' => $choices, 'mapped' => false, 'multiple' => false, 'expanded' => true);

        return array('data' => $configuration['answers'][$answer->getId()]);
    }
}
