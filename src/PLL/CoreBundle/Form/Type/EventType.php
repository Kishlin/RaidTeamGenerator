<?php

namespace PLL\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',      TextType::class)
            ->add('date',      DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => array('class' => 'date')
            ))
            ->add('time',      TimeType::class, array(
                'widget' => 'single_text',
                'input' => 'string',
                'attr' => array('class' => 'time')
            ))
            ->add('compositions', EntityType::class, array(
                'class'        => 'PLLCoreBundle:Composition',
                'choice_label' => 'name',
                'multiple'     => true,
                'required'     => false
            ))
            ->add('players', EntityType::class, array(
                'class'        => 'PLLCoreBundle:Player',
                'choice_label' => 'name',
                'multiple'     => true,
                'required'     => false
            ))
            ->add('save',      SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PLL\CoreBundle\Entity\Event'
        ));
    }
}
