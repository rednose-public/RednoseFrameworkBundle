<?php

namespace Rednose\FrameworkBundle\Request\ParamConverter;

use Rednose\FrameworkBundle\Model\OrganizationManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrganizationParamConverter implements ParamConverterInterface
{
    /**
     * @var OrganizationManagerInterface
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param OrganizationManagerInterface $manager
     */
    public function __construct(OrganizationManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request                $request       The request
     * @param ConfigurationInterface $configuration Contains the name, class and options of the object
     *
     * @return boolean True if the object has been successfully set, else false
     */
    public function apply(Request $request, ConfigurationInterface $configuration)
    {
        $id = $request->query->get('organization_id');

        if (!$id) {
            return;
        }

        $asset = $this->manager->findOrganizationById($id);

        if (!$asset) {
            throw new NotFoundHttpException();
        }

        $request->attributes->set($configuration->getName(), $asset);
    }

    /**
     * Checks if the object is supported.
     *
     * @param ConfigurationInterface $configuration Should be an instance of ParamConverter
     *
     * @return boolean True if the object is supported, else false
     */
    public function supports(ConfigurationInterface $configuration)
    {
        return $configuration->getClass() === 'Rednose\FrameworkBundle\Model\OrganizationInterface';
    }
}

