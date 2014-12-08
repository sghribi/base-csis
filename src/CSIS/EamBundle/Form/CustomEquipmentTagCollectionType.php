<?php

namespace CSIS\EamBundle\Form;


use CSIS\EamBundle\Entity\EquipmentTag;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomEquipmentTagCollectionType extends CollectionType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ($options['allow_add'] && $options['prototype']) {
            $prototype = $builder->getFormFactory()->createNamed(
                $options['prototype_name'],
                $options['type'],
                new EquipmentTag(), // this data that will be used in your event listener to create your wanted prototype
                array_replace(array(
                    'label' => $options['prototype_name'] . 'label__',
                ), $options['options'])
            );

            $builder->setAttribute('prototype', $prototype);
        }
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'custom_equipment_tag_collection';
    }
}
