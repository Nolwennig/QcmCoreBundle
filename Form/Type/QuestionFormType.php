<?php

namespace Qcm\Bundle\CoreBundle\Form\Type;

use Qcm\Component\Question\Model\QuestionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
                    QuestionInterface::TYPE_RADIO => 'qcm_core.label.' . QuestionInterface::TYPE_RADIO,
                ),
                'label' => 'qcm_core.label.answer_type'
            ))
            ->add('enabled', null, array(
                'label' => 'qcm_core.label.enabled'
            ))
            ->add('answers', 'collection', array(
                'label'        => 'qcm_core.label.answers',
                'type'         => 'qcm_core_answer',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'cascade_validation' => true
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
        return 'qcm_core_question';
    }
}
