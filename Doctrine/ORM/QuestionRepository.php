<?php

namespace Qcm\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class QuestionRepository
 */
class QuestionRepository extends EntityRepository
{
    /**
     * Get random questions
     *
     * @param integer $category
     * @param integer $limit
     * @param array   $questionsLevel
     * @param array   $excludeQuestions
     *
     * @return array
     */
    public function getRandomQuestions($category, $limit, $questionsLevel = array(), $excludeQuestions = array())
    {
        $query = $this->createQueryBuilder('q')
            ->addSelect('RAND() as HIDDEN rand, q, a')
            ->leftJoin('q.answers', 'a')
            ->where('q.category = :category')
            ->andWhere('q.enabled = 1')
            ->setParameter('category', $category)
            ->orderBy('rand');

        if (!empty($questionsLevel)) {
            $query->andWhere('q.level IN(:level)')
                ->setParameter('level', $questionsLevel);
        }

        if (!empty($excludeQuestions)) {
            $query->andWhere('q.id NOT IN(:questions)')
                ->setParameter('questions', $excludeQuestions);
        }

        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }

        $questions = $query->getQuery()->getResult();

        return $questions;
    }

    /**
     * Get missing questions
     *
     * @param array   $categories
     * @param integer $limit
     * @param array   $questions
     * @param array   $questionsLevel
     *
     * @return array
     */
    public function getMissingQuestions($categories, $limit, $questions, $questionsLevel = array())
    {
        $query = $this->createQueryBuilder('q')
            ->addSelect('RAND() as HIDDEN rand')
            ->where('q.category IN(:category)')
            ->andWhere('q.enabled = 1')
            ->andWhere('q.id NOT IN (:questions)')
            ->setParameter('category', $categories)
            ->setParameter('questions', $questions)
            ->setMaxResults($limit)
            ->orderBy('rand');

        if (!empty($questionsLevel)) {
            $query->andWhere('q.level IN(:level)')
                ->setParameter('level', $questionsLevel);
        }

        $questions = $query->getQuery()->getResult();

        return $questions;
    }
}
