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
     *
     * @return array
     */
    public function getRandomQuestions($category, $limit)
    {
        $query = $this->createQueryBuilder('q')
            ->addSelect('RAND() as HIDDEN rand')
            ->where('q.category = :category')
            ->andWhere('q.enabled = 1')
            ->setParameter('category', $category)
            ->orderBy('rand');

        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }

        $categories = $query->getQuery()->getResult();

        return $categories;
    }

    /**
     * Get missing questions
     *
     * @param array   $categories
     * @param integer $limit
     * @param array   $questions
     *
     * @return array
     */
    public function getMissingQuestions($categories, $limit, $questions)
    {
        $questions = $this->createQueryBuilder('q')
            ->addSelect('RAND() as HIDDEN rand')
            ->where('q.category IN(:category)')
            ->andWhere('q.enabled = 1')
            ->andWhere('q.id NOT IN (:questions)')
            ->setParameter('category', $categories)
            ->setParameter('questions', $questions)
            ->setMaxResults($limit)
            ->orderBy('rand')
            ->getQuery()
            ->getResult();

        return $questions;
    }
}
