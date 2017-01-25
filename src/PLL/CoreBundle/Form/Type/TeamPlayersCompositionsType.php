<?php

namespace PLL\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use PLL\CoreBundle\Repository\CompositionRepository;
use PLL\CoreBundle\Repository\PlayerRepository;

class TeamPlayersCompositionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $guild_id = $options['guild_id'];

        $builder
            ->add('compositions', EntityType::class, array(
                'label'              => 'team.form.compositions',
                'translation_domain' => 'messages',
                'class'        => 'PLLCoreBundle:Composition',
                'choice_label' => 'name',
                'multiple'     => true,
                'required'     => true,
                'query_builder' => function(CompositionRepository $repository) use($guild_id) {
                    return $repository->getCompositionsForGuildQuery($guild_id);
                }
            ))
            ->add('players',      EntityType::class, array(
                'label'              => 'team.form.players',
                'translation_domain' => 'messages',
                'class'         => 'PLLCoreBundle:Player',
                'choice_label'  => 'name',
                'multiple'      => true,
                'required'      => true,
                'query_builder' => function(PlayerRepository $repository) use($guild_id) {
                    return $repository->getPlayersForGuildQuery($guild_id);
                }
            ))
            ->add('save', SubmitType::class, array(
                'label'              => 'team.button.build',
                'translation_domain' => 'messages',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('guild_id');
    }
}
