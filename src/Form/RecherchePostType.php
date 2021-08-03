<?php

namespace App\Form;



use App\Entity\GameCategory;
use App\Entity\PostCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RecherchePostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('objet', TextType::class,[
                'label' => 'Recherche',
                'required' => false,
                'attr' => [
                    'size' => 150,
                ]
            ])
            ->add('postCategory', EntityType::class, [
                'class' => PostCategory::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => true
            ])
            ->add('submit',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
