<?php

namespace Qcm\Bundle\CoreBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Qcm\Component\Answer\Model\AnswerInterface;
use Qcm\Component\Configuration\Model\ConfigurationInterface;
use Qcm\Component\Question\Model\QuestionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class QuestionFormType
 */
class QuestionFormType extends AbstractType
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
     * @var ConfigurationInterface $configuration
     */
    private $configuration;

    /**
     * Construct
     *
     * @param string                 $class
     * @param string                 $validationGroup
     * @param ConfigurationInterface $configuration
     */
    public function __construct($class, $validationGroup, ConfigurationInterface $configuration)
    {
        $this->class = $class;
        $this->validationGroup = $validationGroup;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'entity', array(
                'empty_value' => 'qcm_core.label.choose_option',
                'class' => 'Qcm\Bundle\PublicBundle\Entity\Category',
                'property' => 'name',
                'label' => 'qcm_core.label.category'
            ))
            ->add('name', null, array(
                'label' => 'qcm_core.label.title'
            ))
            ->add('type', 'choice', array(
                'empty_value' => 'qcm_core.label.choose_option',
                'choices' => array(
                    QuestionInterface::TYPE_CHECKBOX => 'qcm_core.label.' . QuestionInterface::TYPE_CHECKBOX,
                    QuestionInterface::TYPE_TEXT => 'qcm_core.label.' . QuestionInterface::TYPE_TEXT,
                    QuestionInterface::TYPE_CHOICE => 'qcm_core.label.' . QuestionInterface::TYPE_CHOICE,
                ),
                'label' => 'qcm_core.label.answer_type'
            ))
            ->add('enabled', null, array(
                'label' => 'qcm_core.label.enabled'
            ))
            ->add('answers', 'collection', array(
                'label'        => false,
                'type'         => 'qcm_core_answer',
                'allow_add'    => true,
                'allow_delete' => true,
                'cascade_validation' => true
            ));

        $questionLevel = $this->configuration->getQuestionLevel();
        if (!is_null($questionLevel) || $questionLevel > 0) {
            $builder->add('level', 'choice', array(
                'empty_value' => 'qcm_core.label.choose_option',
                'label' => 'qcm_core.label.question_level',
                'required' => false,
                'choices' => $questionLevel
            ));
        }

        $builder->get('answers')->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
            $type = $event->getForm()->getParent()->get('type');

            if ($event->getData()->isEmpty()) {
                $type->addError(new FormError('qcm_core.answer.empty'));

                return;
            }

            if (QuestionInterface::TYPE_TEXT === $type->getData()) {
                if ($event->getData()->count() > 1) {
                    $type->addError(new FormError('qcm_core.question.type.' . QuestionInterface::TYPE_TEXT . '.length'));
                } else if (!$event->getData()->first()->isValid()) {
                    $type->addError(new FormError('qcm_core.question.type.' . QuestionInterface::TYPE_TEXT . '.valid'));
                }
            }

            if (QuestionInterface::TYPE_CHECKBOX === $type->getData()) {
                if ($event->getData()->count() <= 1) {
                    $type->addError(new FormError('qcm_core.question.type.' . QuestionInterface::TYPE_CHECKBOX . '.length'));
                }
            }

            if (QuestionInterface::TYPE_CHOICE === $type->getData()) {
                if ($event->getData()->count() > 1) {
                    $isValid = 0;
                    foreach ($event->getData() as $answer) {
                        if ($answer->isValid()) {
                            $isValid++;
                        }
                    }

                    if ($isValid > 1) {
                        $type->addError(new FormError('qcm_core.question.type.' . QuestionInterface::TYPE_CHOICE . '.count'));
                    } else if ($isValid == 0) {
                        $type->addError(new FormError('qcm_core.question.type.' . QuestionInterface::TYPE_CHOICE . '.is_valid'));
                    }
                } else {
                    $type->addError(new FormError('qcm_core.question.type.' . QuestionInterface::TYPE_CHOICE . '.length'));
                }
            }

            /** @var AnswerInterface $answer */
            foreach ($event->getData() as $answer) {
                $answer->setQuestion($event->getForm()->getParent()->getData());
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'validation_groups' =>$this->validationGroup,
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'qcm_core_question';
    }
}
