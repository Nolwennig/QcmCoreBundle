<?php

namespace Qcm\Bundle\CoreBundle\Listener;

use Qcm\Component\User\Model\UserInterface;
use Qcm\Component\User\Model\UserSessionInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserSessionConfigurationListener
 */
class UserSessionConfigurationListener
{
    /**
     * @var Request $request
     */
    protected $request;

    /**
     * @var EntityRepository $repository
     */
    protected $repository;

    /**
     * Construct
     *
     * @param Request          $request
     * @param EntityRepository $repository
     */
    public function __construct(Request $request, EntityRepository $repository)
    {
        $this->request = $request;
        $this->repository = $repository;
    }

    /**
     * Find and Set user
     *
     * @param ResourceEvent $event
     */
    public function setUser(ResourceEvent $event)
    {
        /** @var UserSessionInterface|null $resource */
        $resource = $event->getSubject();

        if (is_null($resource)) {
            return;
        }

        /** @var UserInterface $user */
        if (!$user = $this->repository->find($this->request->get('user'))) {
            throw new NotFoundHttpException('User not found.');
        }

        if ($user->hasRole(UserInterface::ROLE_ADMIN)) {
            throw new NotFoundHttpException("Can't create a questionnaire for administrator.");
        }

        $resource->setUser($user);
    }

    /**
     * Update configuration
     *
     * @param ResourceEvent $event
     */
    public function updateConfiguration(ResourceEvent $event)
    {
        $resource = $event->getSubject();
        $configuration = clone $resource->getConfiguration();
        $resource->setConfiguration($configuration);
    }
}
