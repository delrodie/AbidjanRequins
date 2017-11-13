<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DepartementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array(
              'attr'  => array(
                  'class' => 'form-control',
                  'autocomplete'  => 'off',
                  'data-error'  => "Le nom du departement",
              )
        ))
            ->add('couleur', TextType::class, array(
              'attr'  => array(
                  'class' => 'form-control',
                  'autocomplete'  => 'off',
                  'data-error'  => "La couleur du departement",
                   //'value'  => "e2ddcf",
              )
        ))
            ->add('type', ChoiceType::class, array(
              'choices' => array(
                'EQUIPE REGIONALE '  => 'Regionale',
                'DISTRICT '  => 'District',
              ),
              'attr'  => array(
                  'class' => 'form-control',
              )
        ))
            //->add('slug')->add('publiePar')->add('modifiePar')->add('publieLe')->add('modifieLe')
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Departement'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_departement';
    }


}
