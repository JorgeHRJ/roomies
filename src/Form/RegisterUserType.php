<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterUserType extends AbstractType
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param TranslatorInterface $translator
     */
    public function __construct(UserPasswordEncoderInterface $encoder, TranslatorInterface $translator)
    {
        $this->encoder = $encoder;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'attr' => ['placeholder' => 'security.register.form.name.placeholder'],
                    'required' => true,
                    'label' => 'security.register.form.name.label'
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'attr' => ['placeholder' => 'security.register.form.email.placeholder'],
                    'required' => true,
                    'label' => 'security.register.form.email.label'
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'mapped' => false,
                    'type' => PasswordType::class,
                    'first_options' => [
                        'attr' => ['placeholder' => 'security.register.form.first_password.placeholder'],
                        'label' => 'security.register.form.first_password.label'
                    ],
                    'second_options' => [
                        'attr' => ['placeholder' => 'security.register.form.second_password.placeholder'],
                        'label' => 'security.register.form.second_password.label'
                    ],
                    'required' => true,
                    'invalid_message' => 'security.register.form.password_not_matching',
                ]
            )
            ->add('submit', SubmitType::class, [
                'label' => 'security.register.form.submit'
            ])
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var User $user */
                $user = $event->getForm()->getData();

                $newPassword = $event->getForm()->get('password')->getData();
                if ($newPassword !== null) {
                    if (strlen($newPassword) < 8) {
                        $event->getForm()->addError(
                            new FormError($this->translator->trans(
                                'security.register.form.password_not_long',
                                [],
                                'security'
                            ))
                        );
                        return;
                    }

                    $newPassword = $this->encoder->encodePassword($user, $newPassword);
                    $user->setPassword($newPassword);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'security'
        ]);
    }
}
