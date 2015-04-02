<?php

namespace Qcm\Bundle\CoreBundle\Question;

use Doctrine\ORM\EntityManager;
use Qcm\Component\Question\Model\QuestionInterface;
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
     * @var array $userConfiguration
     */
    protected $userConfiguration;

    /**
     * @var integer $currentQuestion
     */
    protected $currentQuestion;

    /**
     * @var integer $userSessionId
     */
    private $userSessionId;

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
        $this->userConfiguration = $session->get('configuration');
        $this->userSessionId = $session->get('user_session');

        if (is_null($this->session->get('question'))) {
            $this->session->set('question', 1);
        }

        $this->currentQuestion = $this->session->get('question');
    }

    /**
     * Get user configuration
     *
     * @return array
     */
    public function getUserConfiguration()
    {
        return $this->userConfiguration;
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

        if (isset($configuration['endAt']) && !empty($configuration['endAt'])) {
            $flashBag->add('info', 'qcm_public.questionnaire.already_completed');

            return;
        }

        if ($this->isStarted()) {
            return;
        }

        $this->session->set('user_session', $userSession->getId());
        $this->userSessionId = $userSession->getId();

        if (empty($configuration['startAt'])) {
            $configuration['startAt'] = new \DateTime();
        } else {
            $configuration['startAt'] = new \DateTime($configuration['startAt']['date']);
        }

        $this->updateConfigurationSession($configuration);
    }

    /**
     * Return if questionnaire has started
     *
     * @return boolean
     */
    public function isStarted()
    {
        return (isset($this->userConfiguration['startAt']) && !empty($this->userConfiguration['startAt'])) &&
            !is_null($this->session->get('configuration'));
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
            if (!empty($configuration['startAt']) && empty($configuration['endAt'])) {
                $this->startQuestionnaire($userSession);
                $this->getLastAnsweredQuestion();
                break;
            }
        }
    }

    /**
     * Update configuration session
     *
     * @param array $configuration
     *
     * @return $this
     */
    public function updateConfigurationSession($configuration)
    {
        $userSession = $this->manager->getRepository('QcmPublicBundle:UserSession')->findOneBy(array(
            'id' => $this->userSessionId
        ));

        $userSession->setConfiguration($configuration);
        $this->manager->persist($userSession);
        $this->manager->flush();

        $this->session->set('configuration', $configuration);
        $this->userConfiguration = $configuration;

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

        if (!isset($this->userConfiguration['questions'][$nextQuestionId - 1])) {
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
        $questionId = $this->currentQuestion - 1;

        $question = $this->manager->getRepository('QcmPublicBundle:Question')->findOneBy(
            array('id' => $this->userConfiguration['questions'][$questionId])
        );

        return $question;
    }

    /**
     * Get the last question answered
     */
    private function getLastAnsweredQuestion()
    {
        $configuration = $this->getUserConfiguration();
        $answers = $configuration['answers'];

        end($answers);
        $lastAnswer = key($answers);
        $questionId = array_search($lastAnswer, $configuration['questions']);

        if (!is_null($lastAnswer)) {
            $this->getSpecificQuestion($questionId +1);
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

        if (isset($configuration['questions'][$questionId - 1])) {
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
        $answers = $this->userConfiguration['answers'];
        $data = $form->getData();
        $answers[$question->getId()] = $data;

        $this->userConfiguration['answers'] = $answers;
        $this->updateConfigurationSession($this->userConfiguration);

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

        if (empty($configuration['endAt'])) {
            $now = new \DateTime();

            if (!is_null($endDate)) {
                $now = $endDate;
            }

            $configuration['endAt'] = $now;
            $this->updateConfigurationSession($configuration)
                ->clearQuestionnaire();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function clearQuestionnaire()
    {
        $this->session->remove('configuration');
        $this->session->remove('user_session');
        $this->session->remove('question');

        $this->userConfiguration = null;
        $this->currentQuestion = null;
        $this->userSessionId = null;

        return $this;
    }
}
