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
        // In the dev environment we want the default exception handler.
        if ($this->kernel->getEnvironment() === 'dev') {
            return;
        }

        $exception = $event->getException();
        $response  = new Response();

        if ($exception instanceof NotFoundHttpException) {
            $response->setContent($this->templating->render('RednoseFrameworkBundle:Exception:404.html.twig'));
        } else {
            $response->setContent($this->templating->render('RednoseFrameworkBundle:Exception:500.html.twig'));
        }

        // Send the modified response object to the event
        $event->setResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array('onKernelException');
    }
}
