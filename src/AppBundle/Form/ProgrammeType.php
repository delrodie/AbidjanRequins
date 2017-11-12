<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProgrammeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activite', TextType::class, array(
                  'attr'  => array(
                      'class' => 'form-control',
                      'autocomplete'  => 'off',
                      'data-error'  => "L'activité est obligatoire",
                  )
            ))
            ->add('cible', TextType::class, array(
                  'attr'  => array(
                      'class' => 'form-control',
                      'autocomplete'  => 'off',
                      'data-error'  => "La cible doit être definie",
                  )
            ))
            ->add('lieu', TextType::class, array(
                  'attr'  => array(
                      'class' => 'form-control',
                      'autocomplete'  => 'off',
                      'data-error'  => "Le lieu est obligatoire",
                  )
            ))
            ->add('objectif', null, array(
                  'attr'  => array(
                      'class' => 'form-control',
                      'autocomplete'  => 'off',
                      'data-error'  => "L'objectif est obligatoire'",
                  )
            ))
            ->add('datedeb', TextType::class, array(
                  'attr'  => array(
                      'class' => 'form-control',
                      'autocomplete'  => 'off',
                      'data-error'  => "La date debut est obligatoire",
                  )
            ))
            ->add('datefin', TextType::class, array(
                  'attr'  => array(
                      'class' => 'form-control',
                      'autocomplete'  => 'off',
                      'data-error'  => "La date fin est obligatoire",
                  )
            ))
            ->add('flag', ChoiceType::class, array(
                  'choices' => array(
                    '-- '  => '',
                    'A TRAITER '  => 'A traiter',
                    'A REVOIR '  => 'A revoir',
                    'REJETER '  => 'Rejeter',
                    'VALIDER '  => 'valider',
                  ),
                  'attr'  => array(
                      'class' => 'form-control',
                      'data-error'  => "Veuillez vous pronocer",
                  )
            ))
            ->add('statut', null, array(
                  'attr'  => array(
                      'class' => 'custom-control-input',
                  )
            ))
            //->add('slug')->add('publiePar')->add('modifiePar')->add('publieLe')->add('modifieLe')
            ->add('departement', null, array(
                  'attr'  => array(
                      'class' => 'form-control',
                      'autocomplete'  => 'off',
                      'data-error'  => "Le department est obligatoire",
                  )
            ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Programme'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_programme';
    }


}
