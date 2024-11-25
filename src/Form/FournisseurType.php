<?php

namespace App\Form;

use App\Entity\Fournisseur;
use App\Entity\Marque;
use App\Repository\MarqueRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FournisseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom ne peut pas être vide.',
                    ]),
                ],
                'label' => 'Nom du fournisseur',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez le nom du fournisseur'],
            ])
            ->add('marque', EntityType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Il faut sélectionner une marque.',
                    ]),
                ],
                'class' => Marque::class,
                'choice_label' => 'nom',
                'label' => 'Marque associée',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Sélectionnez une marque',
                'data' => $options['data']->getMarque(), // Sélectionner la marque actuelle du fournisseur
                'query_builder' => function (MarqueRepository $repo) use ($options) {
                    $fournisseur = $options['data'];
                    $query = $repo->createQueryBuilder('m')
                        ->where('m.fournisseur IS NULL');

                    if ($fournisseur && $fournisseur->getMarque()) {
                        $query->orWhere('m.id = :marque_id') // Inclure la marque actuelle dans les options
                            ->setParameter('marque_id', $fournisseur->getMarque()->getId());
                    }

                    return $query;
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fournisseur::class,
        ]);
    }
}
