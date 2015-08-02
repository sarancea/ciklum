<?php

namespace Application\Form;

use Application\Entity\Customer;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Hydrator\ClassMethods;

class CustomerFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function __construct(ServiceManager $serviceLocator)
    {
        $this->serviceManager = $serviceLocator;

        parent::__construct('customer');


        $hydrator = new ClassMethods();

        $this
            ->setHydrator($hydrator)
            ->setObject(new Customer());


        $this->add([
            'name' => 'id',
            'type' => 'Hidden'
        ]);

        $this->add([
            'name' => 'first_name',
            'type' => 'Text'
        ]);

        $this->add([
            'name' => 'last_name',
            'type' => 'Text'
        ]);

        $this->add([
            'name' => 'phone',
            'type' => 'Text'
        ]);

        $this->add([
            'name' => 'address',
            'type' => 'Text'
        ]);

        $this->add([
            'name' => 'status',
            'type' => 'Text'
        ]);


    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {

        $entityManager = $this->serviceManager->get('Doctrine\ORM\EntityManager');


        return [
            'first_name' => [
                'required' => true,
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

            ],
            'last_name' => [
                'required' => true,
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

            ],
            'address' => [
                'required' => true,
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

            ],

            'phone' => [
                'required' => true,
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
            ],
        ];
    }

}