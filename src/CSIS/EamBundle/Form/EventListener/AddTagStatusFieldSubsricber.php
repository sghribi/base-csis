<?php

namespace CSIS\EamBundle\Form\EventListener;

use CSIS\EamBundle\Entity\Tag;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddTagStatusFieldSubsricber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @param FormFactoryInterface $factory
     */
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // check if the product object is "new"
        // If you didn't pass any data to the form, the data is "null".
        // This should be considered a new "Product"
        if ($data && $data->getId()) {
            $form->add($this->factory->createNamed('status', 'choice', null, array(
                    'choices' => array(
                        Tag::ACCEPTED => 'Validé',
                        Tag::REFUSED => 'Refusé')
                    ,'multiple' => false,
                    'auto_initialize' => false)
            ));
        }
    }
}
