<?php

namespace PLL\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class BuildType extends AbstractType
{
    private function getImgChoices()
    {
        $options = array("Berserker", "Chronomancer", "Daredevil", "Dragonhunter", "Druid", "Elementalist", "Engineer", "Guardian", "Herald", "Mesmer", "Necromancer", "Ranger", "Reaper", "Revenant", "Scrapper", "Tempest", "Thief", "Warrior");

        $full_array = array();
        foreach ($options as $option) {
            $full_array['build.main.'.$option] = $option;
        }
        return $full_array;
    }

    private function getSubChoices()
    {
        $options = array("None", "Aegis", "Bleeding", "Blind", "Burning", "Chilled", "Confusion", "Crippled", "Fear", "Fury", "Immobile", "Might", "Poison", "Protection", "Quickness", "Regeneration", "Resistance", "Retaliation", "Slow", "Stability", "Swiftness", "Taunt", "Torment", "Vigor", "Vulnerability", "Weakness");

        $full_array = array();
        foreach ($options as $option) {
            $full_array['build.main.'.$option] = $option;
        }
        return $full_array;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',      TextType::class)
            ->add('img',       ChoiceType::class, array(
                'choices' => $this->getImgChoices(),
                'choice_translation_domain' => 'messages'
            ))
            ->add('imgsub',    ChoiceType::class, array(
                'choices' => $this->getSubChoices()
            ))
            ->add('save',      SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PLL\CoreBundle\Entity\Build'
        ));
    }
}
