<?php

namespace Application;

use Application\Library\Exception\ValidationException;
use Zend\Di\ServiceLocator;
use Zend\Http\Response;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Service\ViewJsonStrategyFactory;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\View\View;

class Module
{

    /**
     * Standard Module Config loader
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Standard Config autoloader
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                ]
            ],
        );
    }

    /**
     * Override default rendering strategy
     *
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        // attach the JSON view strategy
        $app = $e->getTarget();

        /** @var ServiceLocator $locator */
        $locator = $app->getServiceManager();

        /**
         * @var View $view
         */
        $view = $locator->get('ZendViewView');

        /**
         * @var ViewJsonStrategyFactory $strategy
         */
        $strategy = $locator->get('ViewJsonStrategy');

        $view->getEventManager()->attach($strategy, 100);

        $eventManager = $e->getApplication()->getEventManager();

        $moduleRouteListener = new ModuleRouteListener();

        $moduleRouteListener->attach($eventManager);

        //Add listener to check the response
        // Allow possibility to return a simple array
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
            $this,
            'onDispatch'
        ));

        //Add listener for rendering the error
        $eventManager->attach(MvcEvent::EVENT_RENDER, array(
            $this,
            'onRenderError'
        ));

        //Add listener to finish the execution of request
        $eventManager->attach(MvcEvent::EVENT_FINISH, array(
            $this,
            'onFinish'
        ));
    }

    /**
     * Handling result validation
     *
     * @param MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        $currentResult = $e->getResult();

        if (!($currentResult instanceof JsonModel)) {

            if ($currentResult instanceof ViewModel) {
                $newModel = new JsonModel($currentResult->getVariables());
                $e->setResult($newModel);
                $e->setViewModel($newModel);
            }
        }

    }


    /**
     * Manage headers on request finish
     *
     * @param MvcEvent $e
     */
    public function onFinish(MvcEvent $e)
    {
        /**
         *
         * @var Response $response
         */
        $response = $e->getResponse();

        if ($response instanceof Response) {

            $response->setContent($response->getContent() . PHP_EOL); //append EOL
            $headers = $response->getHeaders();

            // Change the header if error occurred
            if ($response->getStatusCode() >= 400) {
                $headers->addHeaderLine('Content-Type', 'application/api-problem+json');
            }

            // overwrite PHP header
            $headers->addHeaderLine('X-Powered-By', 'Application');

        } elseif ($response instanceof \Zend\Console\Response) {
            // Handling console requests
            if ($e->getResult() instanceof JsonModel) {
                echo $e->getResult()->serialize();
            }
        }

    }

    /**
     * Render JSON response on Error
     *
     * @param MvcEvent $e
     */
    public function onRenderError(MvcEvent $e)
    {
        // must be an error
        if (!$e->isError()) {
            return;
        }

        // if we have a JsonModel in the result, then do nothing
        $currentModel = $e->getResult();
        if ($currentModel instanceof JsonModel) {
            return;
        }

        // create a new JsonModel - use application/api-problem+json fields.
        /**
         *
         * @var Response $response
         */
        $response = $e->getResponse();


        /** @var \Exception $exception */
        $exception = $e->getParam('exception');

        // Create a new ViewModel
        $model = new JsonModel(array(
            "httpStatus" => $exception ? $exception->getCode() : ($response instanceof Response ? $response->getStatusCode() : 500),
            "title" => $e->getError(),
        ));


        if ($exception && $exception instanceof ValidationException) {
            $model->httpStatus = $exception->getCode();
            if ($response instanceof Response) {
                $response->setStatusCode($model->httpStatus);
            }
            $model->title = 'validation-exception';

            //We can have many validation errors
            $model->validationMessages = ($exception instanceof ValidationException ? $exception->getMessages() : $exception->getMessage());
        }

        // Add a detailed info about the error, if it exists
        if (isset($_SERVER['APPLICATION_ENV']) && $_SERVER['APPLICATION_ENV'] === 'development' && $e->getError()) {
            switch ($e->getError()) {
                case 'error-controller-cannot-dispatch':
                    $model->detail = 'The requested controller was unable to dispatch the request.';
                    break;
                case 'error-controller-not-found':
                    $model->detail = 'The requested controller could not be mapped to an existing controller class.';
                    break;
                case 'error-controller-invalid':
                    $model->detail = 'The requested controller was not dispatchable.';
                    break;
                case 'error-router-no-match':
                    $model->detail = 'The requested URL could not be matched by routing.';
                    break;
                default:
                    $model->title = get_class($exception);
                    $model->detail = [
                        'message' => ($exception instanceof ValidationException ? $exception->getMessages() : $exception->getMessage()),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                    ];
                    break;
            }
        }

        // set our new view model
        $model->setTerminal(true);
        $e->setResult($model);
        $e->setViewModel($model);
    }

}