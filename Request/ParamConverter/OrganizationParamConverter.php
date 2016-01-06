<?php

namespace Rednose\FrameworkBundle\Request\ParamConverter;

use Rednose\FrameworkBundle\Model\OrganizationManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
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
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === 'Rednose\FrameworkBundle\Model\OrganizationInterface';
    }
}

