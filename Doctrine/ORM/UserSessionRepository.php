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
        $now = new \DateTime();

        /** @var UserSessionInterface $questionnaire */
        foreach ($questionnaires as $questionnaire) {
            $configuration = $questionnaire->getConfiguration();

            if (!is_null($configuration->getEndAt())) {
                continue;
            }

            if ($configuration->getMaxQuestions() - $configuration->getQuestions()->count() == 0) {
                if (is_null($configuration->getStartAt()) || $configuration->getStartAt() <= $now) {
                    $resource[] = $questionnaire;
                }
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
