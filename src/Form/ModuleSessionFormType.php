<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ModuleSessionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('module', EntityType::class, [
                'class' => Module::class,
                'choice_label' => 'intitule',
                'label' => 'Module  '
            ])
            ->add('nbJour', IntegerType::class, [
                'label' => 'Durée du module en JOURS '
            ])
            ->add('Ajouter', SubmitType::class, [
                'attr' => ['class' => 'uk-button']
            ])
        ;           
    }
}