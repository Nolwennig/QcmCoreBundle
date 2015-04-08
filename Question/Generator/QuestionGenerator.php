<?php

namespace Qcm\Bundle\CoreBundle\Question\Generator;

use Doctrine\ORM\EntityManager;
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
     * Construct
     *
     * @param EntityManager $manager
     * @param FlashBag      $flashBag
     * @param Translator    $translation
     */
    public function __construct(EntityManager $manager, FlashBag $flashBag, Translator $translation)
    {
        $this->manager = $manager;
        $this->flashBag = $flashBag;
        $this->translation = $translation;
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
        /** @var SessionConfigurationInterface $configuration */
        $configuration = $userSession->getConfiguration();

        $this->cleanQuestions($configuration)
            ->getRandomQuestion($configuration)
            ->getMissingQuestions($configuration);
    }

    /**
     * Clean questions
     *
     * @param SessionConfigurationInterface $configuration
     *
     * @return $this
     */
    public function cleanQuestions(SessionConfigurationInterface $configuration)
    {
        $categoriesId = array();
        $questionsLevel = $configuration->getQuestionsLevel();

        foreach ($configuration->getCategories() as $category) {
            $categoriesId[] = $category->getId();
        }

        foreach ($configuration->getQuestions() as $question) {
            if (!in_array($question->getCategory()->getId(), $categoriesId)) {
                $configuration->removeQuestion($question);
            } else if (!empty($questionsLevel) && !in_array($question->getLevel(), $questionsLevel)) {
                $configuration->removeQuestion($question);
            }
        }

        if ($configuration->getMaxQuestions() < $configuration->getQuestions()->count()) {
            $questions = $configuration->getQuestions()->toArray();
            shuffle($questions);

            $questions = array_slice($questions, 0, $configuration->getQuestions()->count() - $configuration->getMaxQuestions());

            foreach ($questions as $question) {
                $configuration->removeQuestion($question);
            }
        }

        return $this;
    }

    /**
     * Get random questions by category
     *
     * @param SessionConfigurationInterface $configuration
     *
     * @return $this
     */
    public function getRandomQuestion(SessionConfigurationInterface $configuration)
    {
        /** @var QuestionRepository $questionRepository */
        $questionRepository = $this->manager->getRepository('QcmPublicBundle:Question');
        $maxQuestions = $configuration->getMaxQuestions() - count($configuration->getQuestions());
        $averagePerCategory = floor($maxQuestions/count($configuration->getCategories()));

        $questionsId = array_map(function($question) {
            return $question->getId();
        }, $configuration->getQuestions()->toArray());

        foreach ($configuration->getCategories() as $category) {
            $questions = $questionRepository->getRandomQuestions(
                $category->getId(),
                $averagePerCategory,
                $configuration->getQuestionsLevel(),
                $questionsId
            );

            foreach ($questions as $question) {
                $configuration->addQuestion($question);
            }
        }

        return $this;
    }

    /**
     * Get missing questions
     *
     * @param SessionConfigurationInterface $configuration
     *
     * @return $this
     */
    public function getMissingQuestions(SessionConfigurationInterface $configuration)
    {
        /** @var QuestionRepository $questionRepository */
        $questionRepository = $this->manager->getRepository('QcmPublicBundle:Question');
        $missingQuestions = $configuration->getMaxQuestions() - count($configuration->getQuestions());

        if ($missingQuestions > 0) {
            $questions = $questionRepository->getMissingQuestions(
                $configuration->getCategories(),
                $missingQuestions,
                $configuration->getQuestions(),
                $configuration->getQuestionsLevel()
            );

            foreach ($questions as $question) {
                $configuration->addQuestion($question);
            }
        }

        $missingQuestions = $configuration->getMaxQuestions() - count($configuration->getQuestions());

        if ($missingQuestions > 0) {
            $this->flashBag->add('danger', $this->translation->trans('qcm_core.questions.missing', array(
                '%questions%' => $missingQuestions
            )));
        }

        return $this;
    }
}
