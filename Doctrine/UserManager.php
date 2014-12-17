<?php

namespace Qcm\Bundle\CoreBundle\Doctrine;

use Qcm\Bundle\CoreBundle\Model\UserManager as BaseUserManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * Class UserManager
 */
class UserManager extends BaseUserManager
{
    /**
     * @var ObjectManager $objectManager
     */
    protected $objectManager;

    /**
     * @var string $class
     */
    protected $class;

    /**
     * @var ObjectRepository $repository
     */
    protected $repository;

    /**
     * Construct
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param ObjectManager           $objectManager
     * @param string                  $class
     */
    public function __construct(EncoderFactoryInterface $encoderFactory, ObjectManager $objectManager, $class)
    {
        parent::__construct($encoderFactory);

        $this->objectManager = $objectManager;
        $this->repository = $objectManager->getRepository($class);

        $metadata = $objectManager->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }
}
