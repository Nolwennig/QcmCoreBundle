<?php

namespace Qcm\Bundle\CoreBundle\Question;

use Doctrine\ORM\EntityManager;
use Qcm\Component\Question\Model\QuestionInterface;
use Qcm\Component\User\Model\UserSessionInterface;
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
    }

    /**
     * Get questionnaire configuration
     *
     * @return array
     */
    public function getQuestionnaireConfiguration()
    {
        return $this->session->get('questionnaire');
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

        if ($this->isStarted($userSession)) {
            return;
        }

        $configuration['startAt'] = new \DateTime();
        $userSession->setConfiguration($configuration);

        $this->manager->persist($userSession);
        $this->manager->flush();

        $this->session->set('questionnaire', $configuration);
    }

    /**
     * Return if questionnaire is started
     *
     * @param UserSessionInterface $userSession
     *
     * @return boolean
     */
    public function isStarted(UserSessionInterface $userSession = null)
    {
        if (is_null($userSession)) {
            return !is_null($this->session->get('questionnaire'));
        }

        $configuration = $userSession->getConfiguration();

        return (isset($configuration['startAt']) && !empty($configuration['startAt'])) &&
            !is_null($this->session->get('questionnaire'));
    }

    /**
     * Set the next question
     */
    public function getNextQuestion()
    {
        $nextQuestionId = $this->session->get('question') + 1;

        $this->session->set('question', $nextQuestionId);
    }

    /**
     * Get the previous question
     *
     * @return QuestionInterface
     */
    public function getPrevQuestion()
    {
        $questionId = $this->session->get('question', 1);

        if ($questionId <= 1) {
            return $questionId;
        }

        $this->session->set('question', $questionId - 1);

        return $this->getQuestion();
    }

    /**
     * Get current question
     *
     * @return QuestionInterface
     */
    public function getQuestion()
    {
        $this->getPrevQuestion();
        $questionId = $this->session->get('question', 1) - 1;
        $configuration = $this->session->get('questionnaire');

        $question = $this->manager->getRepository('QcmPublicBundle:Question')->findOneBy(array(
            'id' => $configuration['questions'][$questionId]
        ));

        return $question;
    }

    public function endQuestionnaire()
    {

    }

    public function saveStep()
    {

    }
}
