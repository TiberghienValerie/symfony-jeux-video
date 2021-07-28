<?php

namespace App\Form;


use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('objet', TextType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a object',
                    ])
                    ],
                'label' => 'Objet du message',
                'attr' => [
                    'size' => 50,
                ]
            ])
            ->add('message', TextareaType::class,[
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a message',
                    ])
                ],
                'label' => 'Contenu du message',
                    'attr' => [
                    'rows' => 5,
                    'cols' => 150,
                    ]
                ])
            ->add('save',SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
