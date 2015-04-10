<?php

namespace Qcm\Bundle\CoreBundle\Doctrine\ORM;

use Qcm\Component\Category\Model\CategoryInterface;
use Qcm\Component\User\Model\UserSessionInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class CategoryRepository
 */
class CategoryRepository extends EntityRepository
{
    /**
     * Get category list by questionnaire
     *
     * @param UserSessionInterface $userSession
     *
     * @return array
     */
    public function getCategoryByUserSession(UserSessionInterface $userSession)
    {
        $configuration = $userSession->getConfiguration();
        $categories = $this->createQueryBuilder('c')
            ->select('c.name')
            ->where('c.id IN (:categories)')
            ->setParameter('categories', $configuration->getCategories())
            ->getQuery()
            ->getResult();

        $categories = array_map(function($category) {
            /** @var CategoryInterface $category */

            return $category->getName();
        }, $categories);

        return $categories;
    }
}
