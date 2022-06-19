<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\FormInterface;

class FormUtils
{
    public static function getErrors(FormInterface $form): array
    {
        $errors = [];

        // Global
        foreach ($form->getErrors() as $error) {
            $errors['global'] = (string) $error->getMessage();
        }

        // Fields
        /** @var FormInterface $child */
        foreach ($form as $child) {
            foreach ($child as $element) {
                if ($element->isSubmitted() && !$element->isValid()) {
                    foreach ($element->getErrors() as $error) {
                        $errors[$child->getName()]['message'] = $error->getMessage();
                        $errors[$child->getName()]['messageTemplate'] = $error->getMessageTemplate();
                        $errors[$child->getName()]['messagePluralization'] = $error->getMessagePluralization();
                        $errors[$child->getName()]['messageParameters'] = $error->getMessageParameters();
                        $errors[$child->getName()]['cause'] = $errors[$child->getName()];
                        $errorCause = $error->getCause();
                        $errors[$child->getName()]['cause']['plural'] = $errorCause->getPlural();
                        $errors[$child->getName()]['cause']['propertyPath'] = $errorCause->getPropertyPath();
                        $errors[$child->getName()]['cause']['invalidValue'] = $errorCause->getInvalidValue();
                        $errors[$child->getName()]['cause']['constraint'] = serialize($errorCause->getConstraint());
                        $errors[$child->getName()]['cause']['code'] = $errorCause->getCode();
                        $errors[$child->getName()]['cause']['cause'] = $errorCause->getCause();
                    }
                }
            }

            if ($child->isSubmitted() && !$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()]['message'] = $error->getMessage();
                    $errors[$child->getName()]['messageTemplate'] = $error->getMessageTemplate();
                    $errors[$child->getName()]['messagePluralization'] = $error->getMessagePluralization();
                    $errors[$child->getName()]['messageParameters'] = $error->getMessageParameters();
                    $errors[$child->getName()]['cause'] = $errors[$child->getName()];

                    if ($errorCause = $error->getCause()) {
                        $errors[$child->getName()]['cause']['plural'] = $errorCause->getPlural();
                        $errors[$child->getName()]['cause']['propertyPath'] = $errorCause->getPropertyPath();
                        $errors[$child->getName()]['cause']['invalidValue'] = $errorCause->getInvalidValue();
                        $errors[$child->getName()]['cause']['constraint'] = serialize($errorCause->getConstraint());
                        $errors[$child->getName()]['cause']['code'] = $errorCause->getCode();
                        $errors[$child->getName()]['cause']['cause'] = $errorCause->getCause();
                    }
                }
            }
        }

        return $errors;
    }
}
