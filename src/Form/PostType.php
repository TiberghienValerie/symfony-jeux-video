<?php

namespace App\Form;


use App\Entity\Post;
use App\Entity\PostCategory;
use App\Enum\PostEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;


class PostType extends AbstractType
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
            ->add('content', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a content',
                    ])],
            ])
            ->add('postCategory', EntityType::class, [
                'class' => PostCategory::class,
                'choice_label' => 'name'
            ])
            ->add('status', ChoiceType::class, [
                'choices' => array(
                    'Fermé' => 0,
                    'Ouvert' => 1
                )
                ]
            )
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
