<?php

namespace Qcm\Bundle\CoreBundle\Doctrine\ORM;

use Qcm\Component\User\Model\UserInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class UserSessionRepository
 */
class UserSessionRepository extends EntityRepository
{
    /**
     * Get questionnaires by user
     *
     * @param UserInterface $user
     *
     * @return mixed|\Pagerfanta\Pagerfanta
     */
    public function getQuestionnairesByUser(UserInterface $user)
    {
        return $this->createPaginator(array('user' => $user));
    }
}
