<?php

namespace App\Form;

use App\Entity\Marque;
use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le titre ne peut pas être vide.',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le titre doit comporter au moins {{ limit }} caractères.',
                        'max' => 100,
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le titre du produit'],
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez une description'],
            ])
            ->add('quantiteStock', NumberType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'La quantité en stock ne peut pas être vide.',
                    ]),
                    new Positive([
                        'message' => 'La quantité en stock doit être un nombre positif.',
                    ]),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez la quantité en stock'],
            ])
            ->add('prixTtc', NumberType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le prix TTC ne peut pas être vide.',
                    ]),
                    new Positive([
                        'message' => 'Le prix TTC doit être un nombre positif.',
                    ]),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le prix TTC'],
            ])
            ->add('types', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Les types ne peuvent pas être vides.',
                    ])
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez les types de produit'],
            ])
            ->add('genre', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le genre ne peut pas être vide.',
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le genre ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le genre du produit'],
            ])
            ->add('marque', EntityType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Il faut sélectionner une marque associée.',
                    ]),
                ],
                'class' => Marque::class,
                'choice_label' => 'nom',
                'label' => 'Marque associée',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez une marque',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
