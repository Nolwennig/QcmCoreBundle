<?php

namespace Qcm\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UserSessionConfigurationFormType
 */
class UserSessionConfigurationFormType extends AbstractType
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
     * @var array $defaultConfiguration
     */
    private $defaultConfiguration;

    /**
     * Construct
     *
     * @param string $class
     * @param string $validationGroup
     * @param array  $defaultConfiguration
     */
    public function __construct($class, $validationGroup, $defaultConfiguration)
    {
        $this->class = $class;
        $this->validationGroup = $validationGroup;
        $this->defaultConfiguration = $defaultConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('timeout', 'text', array(
                'label' => 'qcm_core.label.timeout',
                'required' => true
            ))
            ->add('questions', 'collection', array(
                'label'        => false,
                'type'         => 'qcm_core_answer',
                'allow_add'    => true,
                'allow_delete' => true,
                'cascade_validation' => true
            ));

        $builder->get('timeout')->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            if (isset($this->defaultConfiguration['timeout'])) {
                $event->setData($this->defaultConfiguration['timeout']);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' =>$this->validationGroup,
            'data_class' => $this->class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'qcm_core_user_session_configuration';
    }
}
