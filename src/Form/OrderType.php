<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Order;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('phoneNumber')
            ->add('adress')
            // createdAt est valorisé automatiquement dans le contrôleur
            //->add('createdAt', null, [
            //    'widget' => 'single_text',
            //])
            ->add('city', EntityType::class, [
                'class' => City::class,
                //'choice_label' => 'name',
                'choice_label' => function ($city) {
                    return $city->getZipCode() . " - " . $city->getName();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.zipCode', 'ASC')
                        ->addOrderBy('c.name', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
