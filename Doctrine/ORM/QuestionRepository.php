<?php

namespace Qcm\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Qcm\Bundle\CoreBundle\Entity\Category;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class QuestionRepository
 */
class QuestionRepository extends EntityRepository
{
    /**
     * Get random questions
     *
     * @param Category $category
     * @param integer  $limit
     *
     * @return array
     */
    public function getRandomQuestions(Category $category, $limit)
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
     * @param ArrayCollection $categories
     * @param integer         $limit
     * @param ArrayCollection $questions
     *
     * @return array
     */
    public function getMissingQuestions(ArrayCollection $categories, $limit, ArrayCollection $questions)
    {
        $categoryIds = array();
        $questionIds = array();

        foreach ($categories as $category) {
            $categoryIds[] = $category->getId();
        }

        foreach ($questions as $question) {
            $questionIds[] = $question->getId();
        }

        $querstions = $this->createQueryBuilder('q')
            ->addSelect('RAND() as HIDDEN rand')
            ->where('q.category IN(:category)')
            ->andWhere('q.enabled = 1')
            ->andWhere('q.id NOT IN (:questions)')
            ->setParameter('category', $categoryIds)
            ->setParameter('questions', $questionIds)
            ->setMaxResults($limit)
            ->orderBy('rand')
            ->getQuery()
            ->getResult();

        return $querstions;
    }
}
