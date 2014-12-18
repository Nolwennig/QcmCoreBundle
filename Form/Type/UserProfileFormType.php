<?php

namespace Qcm\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UserProfileFormType
 */
class UserProfileFormType extends AbstractType
{
    /**
     * @var string $class
     */
    private $class;

    /**
     * @var string $validationGroup
     */
    private $validationGroup;

    /**
     * Construct
     *
     * @param string $class
     * @param string $validationGroup
     */
    public function __construct($class, $validationGroup)
    {
        $this->class = $class;
        $this->validationGroup = $validationGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'validation_groups' =>$this->validationGroup
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'qcm_core_user';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'qcm_core_user_profile';
    }
}
