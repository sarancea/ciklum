<?php

namespace Auth\Form;

use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class CustomerForm extends Form
{

    /**
     * Init form
     */
    public function __construct()
    {

        // we want to ignore the name passed
        parent::__construct('create_customer');

        $this->setHydrator(new ClassMethodsHydrator());

        $this->add([
            'type' => 'Application\Form\CustomerFieldset',
            'options' => [
                'use_as_base_fieldset' => true,
            ],
        ]);


        $this->setValidationGroup(FormInterface::VALIDATE_ALL);

    }


}