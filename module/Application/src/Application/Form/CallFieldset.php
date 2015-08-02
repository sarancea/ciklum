<?php

namespace Application\Form;

use Application\Entity\Call;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Hydrator\ClassMethods;

class CallFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function __construct(ServiceManager $serviceLocator)
    {
        $this->serviceManager = $serviceLocator;

        parent::__construct('call');


        $hydrator = new ClassMethods();

        $this
            ->setHydrator($hydrator)
            ->setObject(new Call());


        $this->add([
            'name' => 'customer_id',
            'type' => 'Text'
        ]);

        $this->add([
            'name' => 'subject',
            'type' => 'Text'
        ]);

        $this->add([
            'name' => 'content',
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
            'subject' => [
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
            'content' => [
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
            'customer_id' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'DoctrineModule\Validator\ObjectExists',
                        'options' => array(
                            'object_repository' => $entityManager->getRepository('Application\Entity\Customer'),
                            'fields' => 'id'
                        )
                    ]
                ]
            ],
        ];
    }

}