<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\EventListener;

use Rednose\FrameworkBundle\HttpFoundation\MessageResponse;
use Rednose\FrameworkBundle\Message\Message;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Kernel;

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
        $request   = $event->getRequest();

        if ($this->kernel->getEnvironment() === 'test') {
            return;
        }

        $accept = AcceptHeader::fromString($request->headers->get('Accept'));

        if ($request->headers->get('Content-Type') === 'application/json' || $accept->has('application/json')) {
            $event->setResponse(new MessageResponse(new Message($exception->getMessage(), Message::ERROR_TYPE)));

            return;
        }

        // In the dev and test environment we want the default exception handler.
        if ($this->kernel->getEnvironment() !== 'prod') {
            return;
        }

        $response  = new Response();

        if ($exception instanceof NotFoundHttpException) {
            $response->setContent($this->templating->render('RednoseFrameworkBundle:Exception:404.html.twig'));
        } else {
            $msg = 'Unknown error';

            if ($exception->getMessage()) {
                $msg = $exception->getMessage();
            }

            $response->setContent($this->templating->render('RednoseFrameworkBundle:Exception:500.html.twig', array('msg' => $msg)));
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
