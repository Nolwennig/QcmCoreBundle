<?php

namespace Qcm\Bundle\CoreBundle\Question;

use Doctrine\ORM\EntityManager;
use Qcm\Component\Question\Model\QuestionInterface;
use Qcm\Component\User\Model\SessionConfigurationInterface;
use Qcm\Component\User\Model\UserInterface;
use Qcm\Component\User\Model\UserSessionInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class Question Interact
 */
class QuestionInteract
{
    /**
     * @var RequestStack $request
     */
    private $request;

    /**
     * @var SessionInterface $session
     */
    protected $session;

    /**
     * @var EntityManager $manager
     */
    protected $manager;

    /**
     * @var UserSessionInterface $userSession
     */
    protected $userSession;

    /**
     * Construct
     *
     * @param RequestStack     $request
     * @param SessionInterface $session
     * @param EntityManager    $manager
     */
    public function __construct(RequestStack $request, SessionInterface $session, EntityManager $manager)
    {
        $this->request = $request->getCurrentRequest();
        $this->session = $session;
        $this->manager = $manager;
        $this->userSession = $session->get('user_session');
    }

    /**
     * Get user configuration
     *
     * @return SessionConfigurationInterface
     */
    public function getUserConfiguration()
    {
        return $this->userSession->getConfiguration();
    }

    /**
     * Get current question id
     *
     * @return integer
     */
    public function getCurrentQuestion()
    {
        return $this->session->get('question', 0);
    }

    /**
     * Start questionnaire
     *
     * @param UserSessionInterface $userSession
     */
    public function startQuestionnaire(UserSessionInterface $userSession)
    {
        /** @var FlashBag $flashBag */
        $flashBag = $this->session->getBag('flashes');
        $configuration = $userSession->getConfiguration();

        if (!is_null($configuration) && !is_null($configuration->getEndAt())) {
            $flashBag->add('info', 'qcm_public.questionnaire.already_completed');

            return;
        }

        if ($this->isStarted()) {
            return;
        }

        if (is_null($configuration->getStartAt())) {
            $configuration->setStartAt(new \DateTime());
            $userSession->setConfiguration($configuration);
        }

        $this->userSession = $userSession;

        $this->setQuestion($configuration->getQuestions()->key());
        $this->updateSessionConfiguration();
    }

    /**
     * Return if questionnaire has started
     *
     * @return boolean
     */
    public function isStarted()
    {
        return (!is_null($this->userSession) && !is_null($this->getUserConfiguration()->getStartAt())) &&
            !is_null($this->session->get('user_session'));
    }

    /**
     * Get current questionnaire has started
     *
     * @param UserInterface $user
     */
    public function getQuestionnaireStarted(UserInterface $user)
    {
        $userSessions = $this->manager->getRepository('QcmPublicBundle:UserSession')->findBy(array(
            'user' => $user
        ));

        foreach ($userSessions as $userSession) {
            $configuration = $userSession->getConfiguration();
            if (!is_null($configuration->getStartAt()) && is_null($configuration->getEndAt())) {
                $this->startQuestionnaire($userSession);
                $this->getLastAnsweredQuestion();
                break;
            }
        }
    }

    /**
     * Update user session
     *
     * @return $this
     */
    public function updateSessionConfiguration()
    {
        $configuration = clone $this->getUserConfiguration();
        $userSession = $this->manager->getRepository('QcmPublicBundle:UserSession')->findOneBy(array(
            'id' => $this->userSession->getId()
        ));

        $userSession->setConfiguration($configuration);
        $this->manager->persist($userSession);
        $this->manager->flush();

        $this->session->set('user_session', $userSession);

        return $this;
    }

    /**
     * Set the next question
     *
     * @return false|QuestionInterface
     */
    public function getNextQuestion()
    {
        $nextQuestionId = $this->getCurrentQuestion() + 1;
        $questions = $this->getUserConfiguration()->getQuestions();

        if (!isset($questions[$nextQuestionId])) {
            return false;
        }

        $this->setQuestion($nextQuestionId);

        return $this->getQuestion();
    }

    /**
     * Get the previous question
     *
     * @return QuestionInterface
     */
    public function getPrevQuestion()
    {
        $prevQuestionId = $this->getCurrentQuestion() - 1;

        if ($prevQuestionId < 0) {
            return $this->getQuestion();
        }

        if (!is_null($this->getUserConfiguration()->getTimeout())) {
            $this->setQuestion($prevQuestionId);
        }

        return $this->getQuestion();
    }

    /**
     * Set question id
     *
     * @param integer $questionId
     *
     * @return $this
     */
    private function setQuestion($questionId)
    {
        $this->session->set('question', $questionId);

        if (!is_null($this->getUserConfiguration()->getTimePerQuestion())) {
            $this->session->set('question_timeout', new \DateTime());
        }

        return $this;
    }

    /**
     * Get current question
     *
     * @return QuestionInterface
     */
    public function getQuestion()
    {
        return $this->getUserConfiguration()->getQuestions()->get($this->getCurrentQuestion());
    }

    /**
     * Get current question timeout
     *
     * @return \DateTime|null
     */
    public function getQuestionTimeout()
    {
        return $this->session->get('question_timeout', null);
    }

    /**
     * Get the last question answered
     */
    private function getLastAnsweredQuestion()
    {
        $configuration = $this->getUserConfiguration();
        $answer = $configuration->getAnswers()->toArray();
        end($answer);
        key($answer);

        if (empty($answer)) {
            $answer = $this->getCurrentQuestion();
        }

        $this->getSpecificQuestion($answer);
    }

    /**
     * Get specific question
     *
     * @param integer $questionId
     *
     * @return bool
     */
    public function getSpecificQuestion($questionId)
    {
        $configuration = $this->getUserConfiguration();
        $questions = $configuration->getQuestions();

        if (!is_null($questionId) &&
            isset($questions[$questionId]) &&
            !is_null($this->getUserConfiguration()->getTimeout())
        ) {
            $this->setQuestion($questionId);

            return true;
        }

        return false;
    }

    /**
     * Save current questionnaire step
     *
     * @param FormInterface $form
     *
     * @return $this
     */
    public function saveStep(FormInterface $form)
    {
        $answers = $this->getUserConfiguration()->getAnswers();
        $data = $form->getData();
        $answersData = $form->get('answers')->getData();

        if (is_null($answersData)) {
            $answersData = array();
        } else if (!is_array($answersData)) {
            $answersData = array($answersData);
        }

        $answers[$this->getCurrentQuestion()] = array_merge($answersData, $data);
        $this->getUserConfiguration()->setAnswers($answers);
        $this->updateSessionConfiguration();

        return $this;
    }

    /**
     * End current questionnaire
     *
     * @param null|\DateTime $endDate
     *
     * @return $this
     */
    public function endQuestionnaire(\DateTime $endDate = null)
    {
        $configuration = $this->getUserConfiguration();

        if (is_null($configuration->getEndAt())) {
            $now = new \DateTime();

            if (!is_null($endDate)) {
                $now = $endDate;
            }

            $configuration = $this->getUserConfiguration();
            $configuration->setEndAt($now);

            $this->updateSessionConfiguration()
                ->clearQuestionnaire();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function clearQuestionnaire()
    {
        $this->session->remove('user_session');
        $this->session->remove('question');

        $this->userSession = null;

        return $this;
    }
}
