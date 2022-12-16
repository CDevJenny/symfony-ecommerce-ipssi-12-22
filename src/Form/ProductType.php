<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Product;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProductType extends AbstractType
{
    public function __construct(protected AuthorizationCheckerInterface $authorization)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom du produit"
            ])
            ->add('excerpt', CKEditorType::class, [
                "label" => "Résumé",
                "config" => [
                    'uiColor' => '#ffffff',
                ]
            ])
            ->add('description', CKEditorType::class, [
                "label" => "Description",
                "config" => [
                    'uiColor' => '#ffffff',
                ]
            ])
            ->add('image', TextType::class, [
                "label" => "Image",
                "attr" => [
                    "class" => ""
                ]
            ])
            ->add('price', NumberType::class, [
                "label" => "Prix par produit",
                "attr" => [
                    "class" => ""
                ]
            ])
            ->add('status', ChoiceType::class, [
                "label" => "Statut",
                "choices" => [
                    "Disponible" => 2,
                    "Indisponible" => 1,
                    "Brouillon" => 0
                ]
            ])
            ->add('category', EntityType::class, [
                "class" => Category::class,
                "label" => "Catégorie",
                "choice_label" => "name"
            ])
            ->add('brand', EntityType::class, [
                "class" => Brand::class,
                "label" => "Marque",
                "choice_label" => "name"
            ]);
        if ($this->authorization->isGranted("ROLE_USER")) {
            $builder->add('quantity', IntegerType::class, [
                "label" => "Quantité",
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
