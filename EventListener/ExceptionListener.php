<?php

namespace Rednose\FrameworkBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Exception handler for error pages.
 */
class ExceptionListener implements EventSubscriberInterface
{
    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * Constructor.
     *
     * @param Kernel          $kernel
     * @param EngineInterface $templating
     */
    public function __construct(Kernel $kernel, EngineInterface $templating)
    {
        $this->kernel     = $kernel;
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $response  = new Response();

        if ($event->getRequest()->headers->get('Content-Type') === 'application/json') {
            $err = array(
                'message' => $exception->getMessage(),
                'code'    => $exception->getCode(),
            );

            $response->setContent(json_encode($err));
            $response->headers->set('Content-Type', 'application/json');

            $event->setResponse($response);

            return;
        }

        // In the dev and test environment we want the default exception handler.
        if ($this->kernel->getEnvironment() !== 'prod') {
            return;
        }

        if ($exception instanceof NotFoundHttpException) {
            $response->setContent($this->templating->render('RednoseFrameworkBundle:Exception:404.html.twig'));
        } else {
            $response->setContent($this->templating->render('RednoseFrameworkBundle:Exception:500.html.twig'));
        }

        $event->setResponse($response);

        return;
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array('onKernelException');
    }
}
