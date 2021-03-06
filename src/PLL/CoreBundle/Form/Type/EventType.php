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

use PLL\CoreBundle\Repository\CompositionRepository;
use PLL\CoreBundle\Repository\PlayerRepository;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $guild_id = $options['guild_id'];

        $builder
            ->add('name',      TextType::class, array(
                'label'              => 'event.label.name',
                'translation_domain' => 'messages',
            ))
            ->add('date',      DateType::class, array(
                'label'              => 'event.label.date',
                'translation_domain' => 'messages',
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr' => array('class' => 'date')
            ))
            ->add('time',      TimeType::class, array(
                'label'              => 'event.label.time',
                'translation_domain' => 'messages',
                'widget' => 'single_text',
                'input' => 'string',
                'attr' => array('class' => 'time')
            ))
            ->add('compositions', EntityType::class, array(
                'label'              => 'event.label.compositions',
                'translation_domain' => 'messages',
                'class'        => 'PLLCoreBundle:Composition',
                'choice_label' => 'name',
                'multiple'     => true,
                'required'     => false,
                'query_builder' => function(CompositionRepository $repository) use($guild_id) {
                    return $repository->getCompositionsForGuildQuery($guild_id);
                }
            ))
            ->add('players', EntityType::class, array(
                'label'              => 'event.label.players',
                'translation_domain' => 'messages',
                'class'         => 'PLLCoreBundle:Player',
                'choice_label'  => 'name',
                'multiple'      => true,
                'required'      => false,
                'query_builder' => function(PlayerRepository $repository) use($guild_id) {
                    return $repository->getPlayersForGuildQuery($guild_id);
                }
            ))
            ->add('save', SubmitType::class, array(
                'label'              => 'event.button.save',
                'translation_domain' => 'messages',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PLL\CoreBundle\Entity\Event'
        ));
        $resolver->setRequired('guild_id');
    }
}
