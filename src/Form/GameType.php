<?php

namespace App\Form;

use App\Entity\Device;
use App\Entity\Forum;
use App\Entity\Game;
use App\Entity\GameCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a title',
                    ])],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => 5,
                    'cols' => 150,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a description',
                    ])
                ],
            ])
            ->add('price', NumberType::class, [
                'constraints' => [
                    new Positive([
                    'message' => 'This value should be positive',
                    ])
            ],
                    ]
            )
            ->add('pathImg',FileType::class,[
        'label' => 'Logo (Image file)',
        'required' => true,
        'mapped' => false,
        'constraints' => [
            new Image([
                'maxSize' => '1024k',
                'maxSizeMessage' => 'The file is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}',
                'mimeTypes' => [
                    'image/png',
                    'image/jpeg',
                ],
                'mimeTypesMessage' => 'The mime type of the file is invalid ({{ type }}). Allowed mime types are {{ types }}',
            ])
        ],
    ])
            ->add('forum', EntityType::class, [
                'class' => Forum::class,
                'choice_label' => 'title',
                'multiple' => false,
                'expanded' => false
            ])
            ->add('noteGlobal', HiddenType::class, [
                'data' => 0,
            ])
            ->add('gameCategory', EntityType::class, [
                'class' => GameCategory::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('device', EntityType::class, [
                'class' => Device::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('submit', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
