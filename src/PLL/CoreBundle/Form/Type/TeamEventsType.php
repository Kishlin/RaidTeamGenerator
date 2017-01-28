<?php

namespace PLL\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use PLL\CoreBundle\Repository\EventRepository;

class TeamEventsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $guild_id = $options['guild_id'];

        $builder
            ->add('event',      EntityType::class, array(
                'label'              => 'team.label.events',
                'translation_domain' => 'messages',
                'class'         => 'PLLCoreBundle:Event',
                'choice_label'  => 'name',
                'multiple'      => false,
                'required'      => true,
                'query_builder' => function(EventRepository $repository) use($guild_id) {
                    return $repository->getEventsForGuildQuery($guild_id);
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
