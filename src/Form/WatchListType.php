<?php

namespace App\Form;

use App\Entity\WatchList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WatchListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom de la liste *',
                'required' => true,
                'attr' => [
                    'class' => 'form-control bg-dark text-light',
                    'placeholder' => 'Ex : À voir plus tard',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control bg-dark text-light',
                    'rows' => 3,
                    'placeholder' => 'Optionnel : décris ta liste',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WatchList::class,
        ]);
    }
}
