<?php

namespace App\Form;


use App\Entity\Proprietaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;


class ProprietaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('raisonSociale', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a raisonSociale',
                    ])],
            ])
            ->add('adresse1', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a adresse 1',
                    ])],
            ])
            ->add('adresse2', TextType::class, [
                'required' => false
            ])
            ->add('adresse3', TextType::class, [
                'required' => false
            ])
            ->add('cp', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a cp',
                    ])],
            ])
            ->add('ville', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a ville',
                    ])],
            ])
            ->add('telephone', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a telephone',
                    ])],
            ])
            ->add('email', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a email',
                    ]),
                    new Email([
                        'message' => 'The email "{{ value }}" is not a valid email.',
                    ])

                ],
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Proprietaire::class,
        ]);
    }
}
