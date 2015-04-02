<?php

namespace Qcm\Bundle\CoreBundle\Question\Generator;

use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use Qcm\Bundle\CoreBundle\Doctrine\ORM\QuestionRepository;
use Qcm\Component\Question\Generator\GeneratorInterface;
use Qcm\Component\User\Model\SessionConfigurationInterface;
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
     * @var Serializer $serializer
     */
    protected $serializer;

    /**
     * Construct
     *
     * @param EntityManager $manager
     * @param FlashBag      $flashBag
     * @param Translator    $translation
     * @param Serializer    $serializer
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
        /** @var QuestionRepository $questionRepository */
        $questionRepository = $this->manager->getRepository('QcmPublicBundle:Question');
        /** @var SessionConfigurationInterface $configuration */
        $configuration = $userSession->getConfiguration();
        $categories = $configuration->getCategories();
        $questionsLevel = $configuration->getQuestionsLevel();
        $maxQuestions = $configuration->getMaxQuestions();
        $averagePerCategory = floor($maxQuestions/count($categories));

        foreach ($categories as $category) {
            $questions = $questionRepository->getRandomQuestions(
                $category,
                $averagePerCategory,
                $questionsLevel
            );

            foreach ($questions as $question) {
                $configuration->addQuestion($question);
            }
        }

        $missingQuestions = $maxQuestions - count($configuration->getQuestions());

        if ($missingQuestions > 0) {
            $questions = $questionRepository->getMissingQuestions(
                $categories,
                $missingQuestions,
                $configuration->getQuestions(),
                $questionsLevel
            );

            foreach ($questions as $question) {
                $configuration->addQuestion($question);
            }
        }

        $missingQuestions = $maxQuestions - count($configuration->getQuestions());
        if ($missingQuestions > 0) {
            $this->flashBag->add('danger', $this->translation->trans('qcm_core.questions.missing', array(
                '%questions%' => $missingQuestions
            )));
        }
    }
}
