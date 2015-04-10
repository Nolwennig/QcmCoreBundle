<?php

namespace Qcm\Bundle\CoreBundle\Doctrine\ORM;

use Qcm\Component\User\Model\UserInterface;
use Qcm\Component\User\Model\UserSessionInterface;
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
        $questionnaires = $this->createQueryBuilder('us')
            ->where('us.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $resource = array();

        /** @var UserSessionInterface $questionnaire */
        foreach ($questionnaires as $questionnaire) {
            if ($questionnaire->getConfiguration()->getMaxQuestions() - $questionnaire->getConfiguration()->getQuestions()->count() == 0) {
                $resource[] = $questionnaire;
            }
        }

        return $resource;
    }

    /**
     * Get user session configuration
     *
     * @param integer $userSessionId
     *
     * @return mixed
     */
    public function getUserSessionConfiguration($userSessionId)
    {
        $resource = $this->findOneBy(array(
            'id' => $userSessionId
        ));

        return $resource->getConfiguration();
    }

    /**
     * Get questionnaire details
     *
     * @param integer $userSessionId
     *
     * @return mixed
     */
    public function getQuestionnaireDetails($userSessionId)
    {
        $questionnaire = $this->createQueryBuilder('us')
            ->select('us, c, a')
            ->leftJoin('us.configuration', 'c')
            ->leftJoin('s.answers', 'a')
            ->where('us.id = :id')
            ->setParameter('id', $userSessionId)
            ->getQuery()
            ->getOneOrNullResult();

        return $questionnaire;
    }
}
