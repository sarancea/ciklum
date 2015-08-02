<?php
namespace Application\Controller;

use Application\Library\Controller\ApiAbstract;

class CustomersController extends ApiAbstract
{
    /**
     * @var array
     */
    protected $allowedOptions = ['OPTIONS', 'POST', 'GET', 'PUT'];


    /**
     *
     * The entity name
     * @var string
     */
    protected $entityClass = 'Application\Entity\Customer';


    /**
     *
     * The form class
     * @var string
     */
    protected $formClass = 'Application\Form\CustomerForm';
}