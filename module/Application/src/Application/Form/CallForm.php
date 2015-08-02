<?php

namespace Application\Form;

use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CallForm extends Form
{

    /**
     * Init form
     */
    public function __construct(ServiceManager $serviceLocator)
    {

        // we want to ignore the name passed
        parent::__construct('create_call');

        $this->setHydrator(new ClassMethodsHydrator());


        $fieldSet = new CallFieldset($serviceLocator);
        $fieldSet->setUseAsBaseFieldset(true);
        $this->add($fieldSet);


        $this->setValidationGroup(FormInterface::VALIDATE_ALL);
    }


}