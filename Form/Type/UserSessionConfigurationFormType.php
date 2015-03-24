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
            ->add('categories', 'entity', array(
                'label' => 'qcm_core.label.category',
                'multiple' => true,
                'expanded' => true,
                'empty_value' => 'qcm_core.label.choose_option',
                'class' => 'Qcm\Bundle\PublicBundle\Entity\Category',
                'property' => 'name'
            ))
            ->add('dateStart', 'datetime', array(
                'label' => 'qcm_core.label.date_start',
                'required' => false,
                'widget' => 'single_text',
                'format' => 'YYYY-MM-DD H:mm',
            ))
            ->add('timerChoice', 'choice', array(
                'label' => 'qcm_core.label.time_choice',
                'empty_value' => 'qcm_core.label.choose_option',
                'choices' => array(
                    'time_per_question' => 'qcm_core.label.time_per_question',
                    'timeout' => 'qcm_core.label.timeout'
                ),
                'mapped' => false
            ))
            ->add('timeout', 'integer', array(
                'label' => 'qcm_core.label.timeout',
                'required' => true
            ))
            ->add('timePerQuestion', 'integer', array(
                'label' => 'qcm_core.label.time_per_question',
                'required' => true
            ))
            ->add('maxQuestions', 'integer', array(
                'label' => 'qcm_core.label.max_questions',
                'required' => true
            ));

        /*$builder->get('maxQuestions')->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            if (isset($this->defaultConfiguration['max_questions'])) {
                $event->setData($this->defaultConfiguration['max_questions']);
            }
        });

        $builder->get('timeout')->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            if (isset($this->defaultConfiguration['timeout'])) {
                $event->setData($this->defaultConfiguration['timeout']);
            }
        });

        $builder->get('timePerQuestion')->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            if (isset($this->defaultConfiguration['time_per_question'])) {
                $event->setData($this->defaultConfiguration['time_per_question']);
            }
        });*/
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => $this->validationGroup,
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
