<?php
namespace Application\Library\Controller;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\MvcEvent;
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
     * @return mixed
     */
    public function getList()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('c')
            ->from($this->entityClass, 'c');

        $results = $queryBuilder->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        return new JsonModel($results);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
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
    public function get($id)
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
     * Set the event manager instance used by this context
     *
     * @param EventManagerInterface $events
     *
     * @return $this|\Zend\Mvc\Controller\AbstractController
     */
    public function setEventManager(EventManagerInterface $events)
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