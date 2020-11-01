<?php

namespace App\Library\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class BaseController extends AbstractController
{
    /**
     * @param FormInterface $form
     * @param bool $showFields
     * @return array
     */
    public function getFormErrorMessages(FormInterface $form, bool $showFields = true): array
    {
        $errors = [];
        /** @var FormError $error */
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        if ($showFields === true) {
            /** @var Form $child */
            foreach ($form->all() as $child) {
                if (!$child->isValid()) {
                    $options = $child->getConfig()->getOptions();
                    $label = $options['label'] ? $options['label'] : ucwords($child->getName());
                    $errors[$label] = implode('; ', $this->getFormErrorMessages($child));
                }
            }
        }

        return $errors;
    }

    /**
     * @param FormInterface $form
     * @param bool $showFields
     * @return string
     */
    public function getFormErrorMessagesList(FormInterface $form, bool $showFields = true): string
    {
        $baseList = '<ol class="error-list">%s</ol>';
        $elements = '';

        $errors = $this->getFormErrorMessages($form, $showFields);
        foreach ($errors as $error) {
            $elements = sprintf('%s%s', $elements, sprintf('<li>%s</li>', $error));
        }

        return sprintf($baseList, $elements);
    }

    /**
     * @return User|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function getUserInstance()
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('security_login');
        }

        return $user;
    }
}
