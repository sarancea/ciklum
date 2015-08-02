<?php

namespace Application\Library\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

abstract class AbstractForm extends Form
{

    /** @var  InputFilter */
    protected $inputFilter;

    /** @var  array */
    protected $defaults = [];

    /**
     * Init form
     * @param null $name
     */
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct($name);

    }


    /**
     * @param array|\ArrayAccess|\Traversable $data
     * @return Form
     */
    public function setData($data)
    {

        //Set default values
        foreach ($this->getDefaults() as $index => $value) {
            if (!isset($data[$index])) {
                $data[$index] = $value;
            }
        }
        return parent::setData($data);
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
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
        $inputFilter = new InputFilter();


        $this->inputFilter = $inputFilter;

        return $this->inputFilter;
    }


}