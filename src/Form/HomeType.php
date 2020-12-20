<?php

namespace App\Form;

use App\Entity\Home;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class HomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'avatar',
                FileType::class,
                [
                    'mapped' => false,
                    'required' => false,
                    'label' => 'home.form.avatar.label',
                    'constraints' => new File(
                        [
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png'
                            ],
                            'mimeTypesMessage' => 'home.form.avatar.mime_types_message',
                            'maxSize' => '5M',
                            'maxSizeMessage' => 'home.form.avatar.max_size_message'
                        ]
                    )
                ]
            )
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'home.form.name.label',
                'attr' => [
                    'placeholder' => 'home.form.name.placeholder'
                ]
            ])
            ->add('currency', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    '€' => '€',
                    '$' => '$',
                    '£' => '£'
                ],
                'label' => 'home.form.currency.label',
                'attr' => [
                    'placeholder' => 'home.form.currency.placeholder'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'home.form.submit.label'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Home::class,
            'translation_domain' => 'home'
        ]);
    }
}
