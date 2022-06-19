<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\MedicalResult;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MedicalResultFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('agreementNumber', TextType::class, ['label' => 'Numer umowy', 'required' => true])
            ->add('resultDocumentId', TextType::class, ['label' => 'Id dokumentu z wynikiem', 'required' => true])
            ->add(
                'requiredDecisionDate',
                DateTimeType::class,
                ['label' => 'Data wymagalności podjęcia decyzji', 'required' => true, 'widget' => 'single_text',
                    'format' => "yyyy-MM-dd'T'HH:mm:ss'Z'", 'html5' => false, 'input' => 'datetime_immutable']
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MedicalResult::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
