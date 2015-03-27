<?php

namespace Qcm\Bundle\CoreBundle\Doctrine\ORM;

use Qcm\Bundle\PublicBundle\Entity\UserSessionConfiguration;
use Qcm\Component\User\Model\UserInterface;
use Qcm\Component\User\Model\UserSessionInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Class UserSessionRepository
 */
class UserSessionRepository extends EntityRepository
{
    /**
     * Create new user session with default categories
     */
    public function createNewUserSession()
    {
        $className = $this->getClassName();
        $categories = $this->createQueryBuilder('us')
            ->select('c')
            ->from('QcmPublicBundle:Category', 'c')
            ->getQuery()
            ->getResult();

        /** @var UserSessionInterface $resource */
        $resource = new $className;
        $configuration = new UserSessionConfiguration();
        $resource->setConfiguration($configuration);

        foreach ($categories as $category) {
            $resource->getConfiguration()->addCategory($category);
        }

        return $resource;
    }

    /**
     * Get questionnaires by user
     *
     * @param UserInterface $user
     *
     * @return mixed|\Pagerfanta\Pagerfanta
     */
    public function getQuestionnairesByUser(UserInterface $user)
    {
        return $this->findBy(array('user' => $user));
    }
}
