<?php
namespace Application\Library\Controller;

use Application\Form\ModelFilterForm;
use Application\Library\Exception\ValidationException;
use Closure;
use Doctrine\Entity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\MvcEvent;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\JsonModel;


/**
 * Class ApiAbstract
 * @package Application\Library\Controller
 */
abstract class ApiAbstract extends AbstractRestfulController
{

    /**
     * The list of allowed methods
     * Default value [HEAD, OPTIONS]
     * @var array
     */
    protected $allowedOptions = ['HEAD', 'OPTIONS'];

    /**
     *
     * The entity name
     * @var string
     */
    protected $entityClass = null;

    /**
     *
     * The form name
     * @var string
     */
    protected $formClass = null;

    /**
     * Returns the list of allowed methods
     * @return mixed|\Zend\Stdlib\ResponseInterface
     */
    public function options()
    {
        /** @var Response $response */
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Allow', implode(',', $this->allowedOptions));

        return $response;
    }

    /**
     * Return list of resources
     *
     * @param callable $queryBuilderCallback
     * @param callable $queryCallback
     * @throws \Application\Library\Exception\ValidationException
     * @return mixed
     */
    public function getList(\Closure $queryBuilderCallback = null, \Closure $queryCallback = null)
    {
        $form = new ModelFilterForm();
        $form->setData($this->params()->fromQuery());

        if (!$form->isValid()) {
            throw new ValidationException($form->getMessages(), 400);
        }

        $limit = $form->getData()['rows'];
        $page = $form->getData()['page'];

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('c')
            ->from($this->entityClass, 'c');

        if (!is_null($queryBuilderCallback)) {
            $queryBuilderCallback($queryBuilder);
        }


        $query = $queryBuilder->getQuery()
            ->setHydrationMode(Query::HYDRATE_ARRAY);

        if (!is_null($queryCallback)) {
            $queryCallback($query);
        }


        $paginator = new Paginator(
            new DoctrinePaginator(new ORMPaginator($query))
        );
        $paginator->setCurrentPageNumber($page)
            ->setItemCountPerPage($limit);

        $return = array(
            'page' => $paginator->getCurrentPageNumber(),
            'total' => ceil($paginator->getTotalItemCount() / $limit),
            'records' => $paginator->getTotalItemCount(),
            'rows' => $paginator->getCurrentItems()->getArrayCopy(),
        );

        return new JsonModel($return);
    }

    /**
     * @return EntityManager
     */
    public
    function getEntityManager()
    {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }

    /**
     * Return single resource
     *
     * @param  mixed $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return mixed
     */
    public
    function get($id)
    {
        $entityManager = $this->getEntityManager();


        $entity = $entityManager->find($this->entityClass, $id);

        if (!$entity) {
            throw new EntityNotFoundException('Entity not found', 404);
        }

        $hydrator = new ClassMethods();

        return new JsonModel($hydrator->extract($entity));
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

        /** @var Entity $matchInfo */
        $entity = $form->getData();


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


        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $hydrator = new ClassMethods();

        return new JsonModel($hydrator->extract($entity));

    }


    /**
     * Delete an existing resource
     *
     * @param  mixed $id
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @return mixed
     */
    public
    function delete($id)
    {
        $entity = $this->getEntityManager()->find($this->entityClass, $id);

        if (!$entity) {
            throw new EntityNotFoundException('Entity not found', 404);
        }
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();


        /** @var Response $response */
        $response = $this->getResponse();
        $response->setStatusCode(204);

        return $response;
    }


    /**
     * Set the event manager instance used by this context
     *
     * @param EventManagerInterface $events
     *
     * @return $this|\Zend\Mvc\Controller\AbstractController
     */
    public
    function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $this->events = $events;

        // Register a listener at high priority
        $events->attach('dispatch', function (MvcEvent $e) {

            if (!isset($this->entityClass)) {
                throw new \Exception('Controller is not properly set up. "entityClass" is missing', 500);
            }

            //Check is method is NOT allowed
            if (!in_array($e->getRequest()->getMethod(), $this->allowedOptions)) {

                /** @var Response $response */
                $response = $e->getResponse();

                // Return 405
                $response->setStatusCode(405);

                //Indicate what _is_ allowed
                $response->getHeaders()->addHeaderLine('Allow', implode(',', $this->allowedOptions));

                return $response;
            }

            return true;

        }, 10);

        return $this;
    }
}