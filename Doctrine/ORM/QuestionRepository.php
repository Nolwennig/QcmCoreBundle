<?php

namespace Qcm\Bundle\CoreBundle\Doctrine\ORM;

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
}
