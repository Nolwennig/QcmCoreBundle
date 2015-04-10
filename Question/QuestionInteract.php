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
     * @var integer $currentQuestion
     */
    protected $currentQuestion;

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

        if (!is_null($this->userSession) && is_null($this->session->get('question'))) {
            $this->session->set('question', $this->getUserConfiguration()->getQuestions()->key());
        }

        $this->currentQuestion = $this->session->get('question', null);
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

        $this->session->set('question', $configuration->getQuestions()->key());
        $this->userSession = $userSession;

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
        $configuration = $this->getUserConfiguration();
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
        $nextQuestionId = $this->currentQuestion + 1;
        $questions = $this->getUserConfiguration()->getQuestions();

        if (!isset($questions[$nextQuestionId - 1])) {
            return false;
        }

        $this->session->set('question', $nextQuestionId);
        $this->currentQuestion = $nextQuestionId;

        return $this->getQuestion();
    }

    /**
     * Get the previous question
     *
     * @return QuestionInterface
     */
    public function getPrevQuestion()
    {
        $prevQuestionId = $this->currentQuestion - 1;

        if ($prevQuestionId <= 0) {
            return $this->getQuestion();
        }

        $this->session->set('question', $prevQuestionId);
        $this->currentQuestion = $prevQuestionId;

        return $this->getQuestion();
    }

    /**
     * Get current question
     *
     * @return QuestionInterface
     */
    public function getQuestion()
    {
        return $this->getUserConfiguration()->getQuestions()->get($this->currentQuestion);
    }

    /**
     * Get the last question answered
     */
    private function getLastAnsweredQuestion()
    {
        $configuration = $this->getUserConfiguration();
        $answer = $configuration->getAnswers()->last();

        $lastAnswer = null;
        if (false !== $answer) {
            $questionId = $answer->getQuestion()->getId();
        }

        if (!is_null($lastAnswer)) {
            $this->getSpecificQuestion($questionId + 1);
        }
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

        if (isset($questions[$questionId])) {
            $this->session->set('question', $questionId);
            $this->currentQuestion = $questionId;

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
        $question = $this->getQuestion();
        $answers = $this->getUserConfiguration()->getAnswers();
        $data = $form->getData();
var_dump($form->get('answers')->getData());die;
        $answers[$question->getId()] = $data;

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
        $this->currentQuestion = null;

        return $this;
    }
}
