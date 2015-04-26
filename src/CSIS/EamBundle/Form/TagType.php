<?php

namespace CSIS\EamBundle\Form;

use CSIS\EamBundle\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TagType
 */
class TagType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tag', 'text');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($builder) {
            $data = $event->getData();
            $form = $event->getForm();

            if ($data && $data->getId()) {
                $form->add($builder->getFormFactory()->createNamed('status', 'choice', null, array(
                    'choices' => array(
                        Tag::ACCEPTED => 'Validé',
                        Tag::REFUSED => 'Refusé'
                    ),
                    'multiple' => false,
                    'auto_initialize' => false,
                    )
                ));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CSIS\EamBundle\Entity\Tag'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'csis_eambundle_tagtype';
    }
}
