<?php

namespace Qcm\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
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
                'empty_value' => 'qcm_core.label.choose_option',
                'class' => 'Qcm\Bundle\PublicBundle\Entity\Category',
                'property' => 'name',
                'expanded' => true,
                'multiple' => true,
                'attr' => array(
                    'class' => 'form-control'
                )
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

        $questionLevel = $this->defaultConfiguration['question_level'];

        if (!empty($questionLevel)) {
            $builder->add('questionsLevel', 'choice', array(
                'empty_value' => 'qcm_core.label.choose_option',
                'label' => 'qcm_core.label.questions_level',
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => $questionLevel
            ));
        }

        $builder->get('maxQuestions')->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            if (is_null($event->getData()) && isset($this->defaultConfiguration['max_questions'])) {
                $event->setData($this->defaultConfiguration['max_questions']);
            }
        });

        $builder->get('timeout')->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            if (is_null($event->getForm()->getParent()->getData()) &&
                is_null($event->getData()) &&
                isset($this->defaultConfiguration['timeout'])
            ) {
                $event->setData($this->defaultConfiguration['timeout']);
            }
        });

        $builder->get('timePerQuestion')->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            if (is_null($event->getForm()->getParent()->getData()) &&
                is_null($event->getData()) &&
                isset($this->defaultConfiguration['time_per_question'])
            ) {
                $event->setData($this->defaultConfiguration['time_per_question']);
            }
        });

        $builder->get('timerChoice')->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            if (is_null($event->getData())) {
                $event->getForm()->addError(new FormError('qcm_core.user_session.timer_choice'));
            }
        });

        $builder->get('timeout')->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $timerChoice = $event->getForm()->getParent()->get('timerChoice')->getData();
            if (!is_null($timerChoice) &&
                'timeout' === $timerChoice &&
                is_null($event->getData())
            ) {
                $event->getForm()->addError(new FormError('qcm_core.user_session.timeout'));
            }
        });

        $builder->get('timePerQuestion')->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $timerChoice = $event->getForm()->getParent()->get('timerChoice')->getData();
            if (!is_null($timerChoice) &&
                'time_per_question' === $timerChoice &&
                is_null($event->getData())
            ) {
                $event->getForm()->addError(new FormError('qcm_core.user_session.timeout'));
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
            $timerChoice = $event->getForm()->get('timerChoice')->getData();

            if (is_null($timerChoice)) {
                return;
            }

            if ('time_per_question' === $timerChoice) {
                $event->getData()->setTimeout(null);
            } else {
                $event->getData()->setTimePerQuestion(null);
            }
        });
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
