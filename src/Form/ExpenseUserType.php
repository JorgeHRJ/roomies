<?php

namespace App\Form;

use App\Entity\ExpenseUser;
use App\Library\DataTransformer\UserToNumberTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseUserType extends AbstractType
{
    /** @var UserToNumberTransformer */
    private $userTransformer;

    public function __construct(UserToNumberTransformer $userTransformer)
    {
        $this->userTransformer = $userTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', CheckboxType::class, [
                'required' => false,
                'label' => 'expense_user.form.status.label'
            ])
            ->add('paidAt', DateTimeType::class, [
                'required' => false,
                'html5' => false,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'expense_user.form.paid_at.label',
                'attr' => [
                    'placeholder' => 'expense_user.form.paid_at.placeholder'
                ]
            ])
            ->add('user', HiddenType::class)
        ;

        $builder->get('user')->addViewTransformer($this->userTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExpenseUser::class,
            'translation_domain' => 'expense'
        ]);
    }
}
