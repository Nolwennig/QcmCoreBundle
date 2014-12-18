<?php

namespace Qcm\Bundle\CoreBundle\Listener;

use Qcm\Component\User\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class UserRegistrationListener
 */
class UserRegistrationListener
{
    /**
     * @var UserManagerInterface $userManager
     */
    protected $userManager;

    /**
     * Construct
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Update password
     *
     * @param GenericEvent $event
     */
    public function updatePassword(GenericEvent $event)
    {
        $this->userManager->updatePassword($event->getSubject());
    }
}
