<?php

namespace Qcm\Bundle\CoreBundle\Question\Generator;

use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use Qcm\Bundle\CoreBundle\Doctrine\ORM\QuestionRepository;
use Qcm\Component\Question\Generator\GeneratorInterface;
use Qcm\Component\User\Model\UserSessionInterface;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * Class QuestionGenerator
 */
class QuestionGenerator implements GeneratorInterface
{
    /**
     * @var EntityManager $manager
     */
    protected $manager;

    /**
     * @var FlashBag $flashBag
     */
    protected $flashBag;

    /**
     * @var Translator $translation
     */
    protected $translation;

    /**
     * @var JsonEncoder $serializer
     */
    protected $serializer;

    /**
     * Construct
     *
     * @param EntityManager $manager
     * @param FlashBag      $flashBag
     */
    public function __construct(EntityManager $manager, FlashBag $flashBag, Translator $translation, Serializer $serializer)
    {
        $this->manager = $manager;
        $this->flashBag = $flashBag;
        $this->translation = $translation;
        $this->serializer = $serializer;
    }

    /**
     * Create question pool
     *
     * @param ResourceEvent $event
     */
    public function create(ResourceEvent $event)
    {
        $this->generate($event->getSubject());
    }

    /**
     * Generate question
     *
     * @param UserSessionInterface $userSession
     */
    public function generate(UserSessionInterface $userSession)
    {
        $categories = $userSession->getConfiguration()->getCategories();
        $maxQuestions = $userSession->getConfiguration()->getMaxQuestions();
        $averagePerCategory = floor($maxQuestions/count($categories));

        foreach ($categories as $category) {
            /** @var QuestionRepository $questions */
            $questions = $this->manager->getRepository('QcmPublicBundle:Question')->getRandomQuestions(
                $category,
                $averagePerCategory
            );

            foreach ($questions as $question) {
                $userSession->getConfiguration()->addQuestion($question);
            }
        }

        $missingQuestions = $maxQuestions - count($userSession->getConfiguration()->getQuestions());

        if ($missingQuestions > 0) {
            /** @var QuestionRepository $questions */
            $questions = $this->manager->getRepository('QcmPublicBundle:Question')->getMissingQuestions(
                $categories,
                $missingQuestions,
                $userSession->getConfiguration()->getQuestions()
            );

            foreach ($questions as $question) {
                $userSession->getConfiguration()->addQuestion($question);
            }
        }

        $missingQuestions = $maxQuestions - count($userSession->getConfiguration()->getQuestions());
        if ($missingQuestions > 0) {
            $this->flashBag->add('danger', $this->translation->trans('qcm_core.questions.missing', array(
                '%questions%' => $missingQuestions
            )));
        }
    }
}
