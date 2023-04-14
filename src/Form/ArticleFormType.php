<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre de l'article"
            ])
            ->add('subtitle', TextType::class, [
                'label' => "Sous-titre de l'article"
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu'
            ])
            ->add('photo', FileType::class, [
                'label' => "Image d'illustration",
                'data_class' => null,
                'mapped' => false,
                'attr' => [
                    'class' => $options['photo'] !== null ? $options['photo'] : ''
                ],
                'constraints' => [
                    new Image([
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'maxSize' => '5M'
                    ]) 
                ]
            ]) 
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => "Category de l'article",
                'choice_label' => 'name',
                # on utilise le queryBuilder() pour récupérer les catégories qui n'ont pas été softDelete()
                'query_builder' => function (EntityRepository $er) {
                     return $er->createQueryBuilder('c')
                         ->where('c.deletedAt IS NULL')
                    ;
                
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['photo'] === null ? 'Créer' : 'Modifier',
                'validate' => false,
                'attr' => [
                    'class' => 'd-block mx-auto my-3 col-4 btn btn-primary'
                ]
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'allow_file_upload' => true,
            'photo' => null
        ]);
    }
}
