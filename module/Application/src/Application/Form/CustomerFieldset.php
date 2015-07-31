<?php

namespace Match\Form;

use Application\Entity\Customer;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Hydrator\ClassMethods;

class MatchFieldset extends Fieldset implements InputFilterProviderInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        parent::__construct('customer');


        $hydrator = new ClassMethods();

        $this
            ->setHydrator($hydrator)
            ->setObject(new Customer());


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

            ],
            'last_name' => [
                'required' => true,

            ],

            'phone' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $entityManager->getRepository('Application\Entity\Customer'),
                            'fields' => 'phone'
                        )
                    ]
                ]
            ],
        ];
    }

}