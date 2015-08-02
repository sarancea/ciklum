<?php

namespace Application\Form;

use Application\Library\Form\AbstractForm;
use Zend\InputFilter\InputFilter;

class ModelFilterForm extends AbstractForm
{


    /**
     * Init form
     * @param null $name
     */
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct($name);

        //Limit the request
        $this->add([
            'name' => 'rows',
            'type' => 'Text'
        ]);


        //The page number
        $this->add([
            'name' => 'page',
            'type' => 'Text'
        ]);

        //The page number
        $this->add([
            'name' => 'sidx',
            'type' => 'Text'
        ]);

        //The page number
        $this->add([
            'name' => 'sord',
            'type' => 'Text'
        ]);

        $this->setDefaults([
            'rows' => 25,
            'page' => 1,
        ]);
    }

    /**
     * @param array $defaults
     */
    public function setDefaults($defaults)
    {
        $this->defaults = array_merge($this->defaults, $defaults);
    }


    /**
     * Set up input filter for the form
     *
     * @return null|InputFilter|\Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }
        $inputFilter = parent::getInputFilter();


        $inputFilter->add([
            'name' => 'rows',
            'required' => false,
            'filters' => [
                [
                    'name' => 'Int',
                ],
            ]
        ]);

        $inputFilter->add([
            'name' => 'page',
            'required' => false,
            'filters' => [
                [
                    'name' => 'Int',
                ],
            ]
        ]);

        $inputFilter->add([
            'name' => 'order',
            'required' => false,
            'filters' => [
                [
                    'name' => 'StripTags',
                ],
                [
                    'name' => 'StripNewLines',
                ],
                [
                    'name' => 'StringTrim',
                ],
                [
                    'name' => 'StringToLower',
                ],
            ]
        ]);

        return $this->inputFilter;
    }


}