<?php

namespace PLL\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompositionType extends AbstractType
{
    private function getBossChoices()
    {
        $options = array("valegardian", "gorseval", "sabetha", "slothasor", "camp", "matthias", "escort", "keepconstruct", "xera");

        $full_array = array();
        foreach ($options as $option) {
            $full_array['boss.'.$option] = 'boss.'.$option;
        }
        return $full_array;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label'              => 'composition.label.name',
                'translation_domain' => 'messages',
            ))
            ->add('boss', ChoiceType::class, array(
                'label'              => 'composition.label.boss',
                'translation_domain' => 'messages',
                'choices' => $this->getBossChoices(),
                'choice_translation_domain' => 'messages'
            ))
            ->add('save', SubmitType::class, array(
                'label'              => 'composition.button.submit',
                'translation_domain' => 'messages',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PLL\CoreBundle\Entity\Composition'
        ));
    }
}
