<?php

namespace Qcm\Bundle\CoreBundle\Question\Generator;

use Doctrine\ORM\EntityManager;
use Qcm\Component\Question\Generator\GeneratorInterface;
use Qcm\Component\User\Model\UserSessionInterface;
use Sylius\Component\Resource\Event\ResourceEvent;

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
     * Construct
     *
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
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
        $averagePerCategory = floor($maxQuestions/$categories->count());

        foreach ($categories as $category) {
            $questions = $this->manager->getRepository('QcmPublicBundle:Question')->getRandomQuestions($category, $averagePerCategory);

            foreach ($questions as $question) {
                $userSession->getConfiguration()->addQuestion($question);
            }
        }

        $this->manager->persist($userSession);
        $this->manager->flush();
    }
}
