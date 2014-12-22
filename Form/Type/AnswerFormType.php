<?php

namespace Qcm\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AnswerFormType
 */
class AnswerFormType extends AbstractType
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
            /*->add('question', 'entity', array(
                'empty_value' => 'qcm_core.label.choose_option',
                'class' => 'Qcm\Bundle\PublicBundle\Entity\Question',
                'property' => 'name',
                'label' => 'qcm_core.label.question'
            ))*/
            ->add('value', null, array(
                'label' => 'qcm_core.label.value'
            ))
            ->add('valid', null, array(
                'label' => 'qcm_core.label.is_valid'
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
        return 'qcm_core_answer';
    }
}
