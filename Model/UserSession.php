<?php

namespace Qcm\Bundle\CoreBundle\Model;

use Qcm\Component\User\Model\SessionConfigurationInterface;
use Qcm\Component\User\Model\UserInterface;
use Qcm\Component\User\Model\UserSessionInterface;

/**
 * Class UserSession
 */
abstract class UserSession implements UserSessionInterface
{
    /**
     * @var integer $id
     */
    protected $id;

    /**
     * @var UserInterface $user
     */
    protected $user;

    /**
     * @var SessionConfigurationInterface $configuration
     */
    protected $configuration;

    /**
     * @var boolean $valid
     */
    protected $valid;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param UserInterface $user
     *
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the user configuration
     *
     * @param SessionConfigurationInterface $configuration
     *
     * @return mixed
     */
    public function setConfiguration(SessionConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Get the user configuration
     *
     * @return SessionConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
