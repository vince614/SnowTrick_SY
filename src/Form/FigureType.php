<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Group;
use App\Entity\Image;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FigureType
 * @package App\Form
 */
class FigureType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('groupe',
                EntityType::class,
                [
                    'class'         => Group::class,
                    'choice_label'  => 'groupe',
                    'multiple'      => true,
                    'required'      => false,
                    'query_builder' => function (EntityRepository $entityRepository) {
                        return $entityRepository->createQueryBuilder('g')
                            ->select('g')
                            ->orderBy('name', 'ASC');
                    }
                ]
            )
            ->add('slug')
            ->add('groupFigure')
            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
