<?php

namespace App\Form;

use App\Entity\Expense;
use App\Entity\User;
use App\Library\DataTransformer\TagNameToExpenseTagTransformer;
use App\Service\ContextService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ExpenseType extends AbstractType
{
    /** @var ContextService */
    private $contextService;

    /** @var TagNameToExpenseTagTransformer */
    private $tagTransformer;

    public function __construct(ContextService $contextService, TagNameToExpenseTagTransformer $tagTransformer)
    {
        $this->contextService = $contextService;
        $this->tagTransformer = $tagTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'file',
                FileType::class,
                [
                    'mapped' => false,
                    'required' => false,
                    'label' => 'expense.form.file.label',
                    'constraints' => new File(
                        [
                            'mimeTypes' => ['image/jpeg', 'image/png', 'application/pdf',],
                            'mimeTypesMessage' => 'expense.form.file.mime_types_message',
                            'maxSize' => '5M',
                            'maxSizeMessage' => 'expense.form.file.max_size_message'
                        ]
                    )
                ]
            )
            ->add('amount', TextType::class, [
                'required' => true,
                'label' => 'expense.form.amount.label',
                'attr' => [
                    'placeholder' => 'expense.form.amount.placeholder'
                ]
            ])
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'expense.form.title.label',
                'attr' => [
                    'placeholder' => 'expense.form.title.placeholder'
                ]
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'expense.form.description.label',
                'attr' => [
                    'placeholder' => 'expense.form.description.placeholder'
                ]
            ])
            ->add('paidBy', EntityType::class, [
                'class' => User::class,
                'choices' => $options['home_users'],
                'required' => true,
                'label' => 'expense.form.paid_by.label',
                'attr' => [
                    'placeholder' => 'expense.form.paid_by.placeholder'
                ]
            ])
            ->add('paidAt', DateTimeType::class, [
                'required' => false,
                'html5' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'expense.form.paid_at.label',
                'attr' => [
                    'placeholder' => 'expense.form.paid_at.placeholder'
                ]
            ])
            ->add('expenseUsers', CollectionType::class, [
                'entry_type' => ExpenseUserType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype' => true
            ])
            ->add('tags', ChoiceType::class, [
                'mapped' => false,
                'multiple' => true,
                'label' => 'expense.form.tags.label',
                'attr' => [
                    'placeholder' => 'expense.form.tags.placeholder'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'expense.form.submit.label'
            ])
        ;

        $builder->get('tags')->resetModelTransformers();
        $builder->get('tags')->resetViewTransformers();
        $builder->get('tags')->addModelTransformer($this->tagTransformer);
        $builder->get('tags')->addViewTransformer($this->tagTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expense::class,
            'translation_domain' => 'expense',
            'home_users' => []
        ]);
        $resolver->setRequired('home_users');
        $resolver->setAllowedTypes('home_users', 'array');
    }
}
