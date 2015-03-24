<?php

namespace Qcm\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class UserSessionRepository
 */
class UserRepository extends EntityRepository
{
    /**
     * Get users list
     *
     * @param array $orderBy
     *
     * @return mixed|\Pagerfanta\Pagerfanta
     */
    public function getUsers($orderBy = null)
    {
        $users = $this->createQueryBuilder('o')
            ->where('o.enabled = 1');

        $this->applySorting($users, $orderBy);

        return $users
            ->getQuery()
            ->getResult();
    }
}
