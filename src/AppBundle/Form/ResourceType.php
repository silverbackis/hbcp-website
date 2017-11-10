<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Entity\Resource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ResourceType extends AbstractType
{
    private $em;
    private $serializer;

    public function __construct(EntityManagerInterface $em, Serializer $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('resourceType', EntityType::class, [
                'class' => \AppBundle\Entity\ResourceType::class,
                'choice_label' => 'name',
                'placeholder' => 'Unset',
                'required' => false
            ])
            ->add('topCategory', EntityType::class, [
                'class' => Category::class,
                'placeholder' => '-- Select a top level category --',
                'choice_label' => function (Category $category) {
                    return $category->getBreadcrumbs(true);
                },
                'choice_attr' => function (Category $category)
                {
                    return [
                        'data-choices' => $this->serializer->serialize(
                            $this->getChoicesForCategory($category),
                            'json',
                            ['groups' => ['cat_select']]
                        )
                    ];
                },
                'choices' => $this->em->getRepository(Category::class)->findDeepestChildren(true)
            ])
            ->add('pathType', ChoiceType::class, [
                'choices' => [
                    'Dropbox',
                    'Website',
                    'Tool'
                ],
                'choice_label' => function ($value) {
                    return $value;
                }
            ])
            ->add('path', TextType::class)
            ->add('submit', SubmitType::class)
        ;

        $formModifier = function (FormInterface $form, Category $category = null) {
            $choices = $category ? $this->getChoicesForCategory($category) : [];
            $form->add('category', EntityType::class, [
                'class' => Category::class,
                'choices' => $choices,
                'choice_label' => function (Category $category) {
                    if ($category->getFixed()) {
                        return 'General';
                    }
                    return $category->getName();
                }
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                dump($data->getTopCategory());
                $formModifier($event->getForm(), $data->getTopCategory());
            }
        );

        $builder->get('topCategory')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $form = $event->getForm();
                $formModifier($form->getParent(), $form->getData());
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Resource::class,
        ));
    }

    private function getChoicesForCategory (Category $category)
    {
        $collection = [$category];
        $choices = new ArrayCollection(array_merge($collection, $category->getChildren()->toArray()));
        return $choices;
    }
}