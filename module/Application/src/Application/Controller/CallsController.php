<?php
namespace Application\Controller;

use Application\Library\Controller\ApiAbstract;
use Application\Library\Exception\ValidationException;
use Doctrine\Entity;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\JsonModel;

class CallsController extends ApiAbstract
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
    protected $entityClass = 'Application\Entity\Call';


    /**
     *
     * The form class
     * @var string
     */
    protected $formClass = 'Application\Form\CallForm';


    /**
     * Return list of resources
     *
     * @param callable $queryBuilderCallback
     * @param callable $queryCallback
     * @return mixed
     */
    public function getList(\Closure $queryBuilderCallback = null, \Closure $queryCallback = null)
    {

        $customerId = $this->params()->fromQuery('customer_id');

        $queryBuilderCallback = function (QueryBuilder $queryBuilder) use ($customerId) {
            $queryBuilder->select('c', 'cu');
            $queryBuilder->innerJoin('c.customer', 'cu');
            if (!empty($customerId)) {
                $queryBuilder->where('c.customer_id = :cid');
                $queryBuilder->setParameter('cid', $customerId);
            }
        };

        return parent::getList($queryBuilderCallback, $queryCallback);
    }

    /**
     * Create a new resource
     *
     * @param  mixed $data
     * @throws \Application\Library\Exception\ValidationException
     * @return mixed
     */
    public function create($data)
    {
        /** @var Form $form */
        $form = new $this->formClass($this->getServiceLocator());
        $form->setData($data);

        if (!$form->isValid()) {
            throw new ValidationException($form->getMessages(), 400);
        }

        $customer = $this->getEntityManager()->find('Application\Entity\Customer', $data['call']['customer_id']);


        /** @var Entity $matchInfo */
        $entity = $form->getData();

        $entity->setCustomer($customer);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $hydrator = new ClassMethods();

        return new JsonModel($hydrator->extract($entity));
    }

    /**
     * Update an existing resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Application\Library\Exception\ValidationException
     * @return mixed
     */
    public function update($id, $data)
    {
        $entity = $this->getEntityManager()->find($this->entityClass, $id);

        if (!$entity) {
            throw new EntityNotFoundException('Entity not found', 404);
        }

        /** @var Form $form */
        $form = new $this->formClass($this->getServiceLocator());
        $form->bind($entity);

        $form->setData($data);

        if (!$form->isValid()) {
            throw new ValidationException($form->getMessages(), 400);
        }

        if (isset($data['call']['customer_id'])) {
            $customer = $this->getEntityManager()->find('Application\Entity\Customer', $data['call']['customer_id']);

            /** @var Entity $matchInfo */
            $entity = $form->getData();

            $entity->setCustomer($customer);
        }

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $hydrator = new ClassMethods();

        return new JsonModel($hydrator->extract($entity));

    }

}