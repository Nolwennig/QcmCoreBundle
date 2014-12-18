<?php

namespace Qcm\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UserFormType
 */
class UserFormType extends AbstractType
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'label' => 'qcm_core.label.username'
            ))
            ->add('email', 'email', array(
                'label' => 'qcm_core.label.email'
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'first_options' => array(
                    'label' => 'qcm_core.label.password'
                ),
                'second_options' => array(
                    'label' => 'qcm_core.label.password_confirmation'
                )
            ))
            ->add('enabled', null, array(
                'label' => 'qcm_core.label.enabled'
            ));
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
    public function getName()
    {
        return 'qcm_core_user';
    }
}
