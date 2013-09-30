<?php

namespace Rednose\FrameworkBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use FOS\OAuthServerBundle\Controller\AuthorizeController as BaseAuthorizeController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Rednose\FrameworkBundle\Form\Model\Authorize;
use Rednose\FrameworkBundle\Entity\Client;

class AuthorizeController extends Controller
{
    /**
     * @see FOS\OAuthServerBundle\Controller\AuthorizeController::authorizeAction()
     */
    public function authorizeAction(Request $request)
    {
        if (!$request->get('client_id')) {
            throw new NotFoundHttpException("Client id parameter {$request->get('client_id')} is missing.");
        }

        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->findClientByPublicId($request->get('client_id'));

        if (!($client instanceof Client)) {
            throw new NotFoundHttpException("Client {$request->get('client_id')} is not found.");
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $form = $this->container->get('rednose_framework.authorize.form');
        $formHandler = $this->container->get('rednose_framework.authorize.form_handler');

        $authorize = new Authorize();

        if (($response = $formHandler->process($authorize)) !== false) {
            return $response;
        }

        return $this->container->get('templating')->renderResponse('RednoseFrameworkBundle:Authorize:authorize.html.twig', array(
            'form'   => $form->createView(),
            'client' => $client,
        ));
    }

    /**
     * Returns an access token based on a given username and password.
     * <p><strong>Example JSON response</strong></p>
     * <pre>{
     *     "access_token":"ZmE4N",
     *     "expires_in":3600,
     *     "token_type":"bearer",
     *     "scope":null,
     *     "refresh_token":"ZDkwM"
     * }</pre>
     *
     * @see FOS\OAuthServerBundle\Controller\TokenController::tokenAction()
     *
     * @RequestParam(name="grant_type", strict=true, requirements="String", description="The grant type.")
     * @RequestParam(name="client_id", strict=true, requirements="String", description="The unique id for the client application.")
     * @RequestParam(name="client_secret", strict=true, requirements="String", description="The secret for the client application.")
     * @RequestParam(name="username", strict=true, requirements="String", description="The user's username.")
     * @RequestParam(name="password", strict=true, requirements="String", description="The user's password.")
     *
     * @ApiDoc(
     *     section="OAuth 2.0"
     * )
     */
    public function tokenAction(Request $request)
    {
        return $this->forward('fos_oauth_server.controller.token:tokenAction', array(
            'request' => $request,
        ));
    }
}
