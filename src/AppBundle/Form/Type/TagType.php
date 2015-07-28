<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            'text',
            array(
                'label' => 'Name',
            )
        );
        $builder->add(
            'description',
            'textarea',
            array(
                'label' => 'Beschreibung',
            )
        );
        $builder->add(
            'color',
            'text',
            array(
                'label' => 'Farbe',
            )
        );
        $builder->add(
            'create',
            'submit',
            array(
                'label' => 'Tag erstellen',
                'attr' => array('class' => 'btn btn-primary'),
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Tag',
            )
        );
    }

    public function getName()
    {
        return 'app_create_tag';
    }
}
