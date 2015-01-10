<?php

namespace Rednose\FrameworkBundle\Tests\Serializer;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\SchemaTool;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SerializerTest extends WebTestCase
{
    /**
     * @var string
     */
    protected $xml = '';

    /**
     * @var string
     */
    protected $json = '';

    /**
     * @var EntityManager
     */
    protected $em = null;

    /**
     * @var boolean
     */
    protected static $schemaSetUp = false;

    public function setUp()
    {
        $client = static::createClient();

        $this->serializer = $client->getContainer()->get('serializer');

        $this->xml  = file_get_contents(__DIR__ . '/../Fixtures/datadictionary/test_dictionary.xml');
        $this->json = file_get_contents(__DIR__ . '/../Fixtures/datadictionary/test_dictionary.json');

        if ($this->em === null) {
            $this->em = $client->getContainer()->get('doctrine')->getManager();

            if (static::$schemaSetUp === false) {
                $metadataFactory = $this->em->getMetadataFactory();

                $classes = array(
                    $metadataFactory->getMetaDataFor('Rednose\FrameworkBundle\Entity\DataDictionary'),
                    $metadataFactory->getMetaDataFor('Rednose\FrameworkBundle\Entity\DataControl')
                );

                $st = new SchemaTool($this->em);
                $st->dropSchema($classes);
                $st->createSchema($classes);

                static::$schemaSetUp = true;
            }
        }
    }

    public function testCanCreateNewObjectFromXML()
    {
        $context = new DeserializationContext();
        $context->setGroups(array('file'));

        $dataDictionary = $this->serializer->deserialize(
            $this->xml, 'Rednose\FrameworkBundle\Entity\DataDictionary', 'xml', $context
        );

        $this->assertObject($dataDictionary, $this->json);

        return $dataDictionary;
    }


    public function testCanSerializeObjectFromJSON()
    {
        $dataDictionary = $this->deserializeFromJson('Rednose\FrameworkBundle\Entity\DataDictionary', $this->json);

        $this->assertObject($dataDictionary, $this->json);

        return $dataDictionary;
    }

    /**
     * @depends testCanCreateNewObjectFromXML
     */
    public function testCanSerializeObjectToJSON($dataDictionary)
    {
        $json = $this->serializeToJson($dataDictionary);

        $this->assertEquals($json, $this->json);

        return $json;
    }

    /**
     * @depends testCanCreateNewObjectFromXML
     */
    public function testCanSerializeObjectToXML($dataDictionary)
    {
        $domPrimary   = new \DOMDocument('1.0', 'UTF-8');
        $domSecondary = new \DOMDocument('1.0', 'UTF-8');

        $domPrimary->formatOutput = true;
        $domPrimary->preserveWhiteSpace = false;
        $domSecondary->formatOutput = true;
        $domSecondary->preserveWhiteSpace = false;

        $context = new SerializationContext();
        $context->setGroups(array('file'));

        $xml = $this->serializer->serialize(
            $dataDictionary, 'xml', $context
        );

        $domPrimary->loadXML($this->xml);
        $domSecondary->loadXML($xml);

        $this->assertEquals($domPrimary->saveXML(), $domSecondary->saveXML());
    }

    /**
     * @depends testCanCreateNewObjectFromXML
     */
    public function testCanPersistSerializedEntity($dataDictionary)
    {
        $id = $dataDictionary->getId();

        $this->em->persist($dataDictionary);
        $this->em->flush();

        $this->assertTrue($id !== $dataDictionary->getId());

        return $dataDictionary->getId();
    }

    /**
     * @depends testCanPersistSerializedEntity
     */
    public function testUpdateAndKeepAllAssociatedEntities($dataDictionaryId)
    {
        $dataDictionary = $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneById($dataDictionaryId);

        $entityJson = $this->serializeToJson($dataDictionary, 'details');
        $entityJson = json_decode($entityJson);
        $entityJson->controls[0]->children[0]->type = 'boolean';
        $entityJson = json_encode($entityJson, JSON_PRETTY_PRINT);

        $updatedDataDictionary = $this->deserializeFromJson('Rednose\FrameworkBundle\Entity\DataDictionary', $entityJson, 'details');

        $this->em->persist($updatedDataDictionary);
        $this->em->flush();

        return array($dataDictionaryId, $entityJson);
    }

    /**
     * Check the persistend state in a different test to make sure doctrine fetched a new instance of the object.
     *
     * @depends testUpdateAndKeepAllAssociatedEntities
     */
    public function testUpdateAndKeepAllAssociatedEntitiesPersisted($state)
    {
        $dataDictionary = $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneById($state[0]);

        $this->assertObject($dataDictionary, $state[1]);

        return $state[0];
    }

    /**
     * @depends testUpdateAndKeepAllAssociatedEntitiesPersisted
     */
    public function testUpdateSinglePropertyUsingContextAndKeepAllAssociatedEntities($dataDictionaryId)
    {
        $entityJson = new \stdClass;
        $entityJson->name = 'Renamed-Test-Dictionary';
        $entityJson = json_encode($entityJson);

        $updatedDataDictionary = $this->deserializeFromJson('Rednose\FrameworkBundle\Entity\DataDictionary', $entityJson, 'details', $dataDictionaryId);

        $this->em->persist($updatedDataDictionary);
        $this->em->flush();

        return array($dataDictionaryId, $entityJson);
    }

    /**
     * Check the persistend state in a different test to make sure doctrine fetched a new instance of the object.
     *
     * @depends testUpdateSinglePropertyUsingContextAndKeepAllAssociatedEntities
     */
    public function testUpdateSinglePropertyUsingContextAndKeepAllAssociatedEntitiesPersisted($state)
    {
        $dataDictionary = $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneById($state[0]);

        $this->assertObject($dataDictionary, $state[1]);
        $this->assertTrue($dataDictionary->getControls()->count() > 0);
        $this->assertTrue($dataDictionary->getControls()->get(0)->getChildren(0)->count() > 0);
    }

    /**
     * @depends testUpdateAndKeepAllAssociatedEntitiesPersisted
     */
    public function testRemoveChildEntities($dataDictionaryId)
    {
        $entityJson = new \stdClass;
        $entityJson->controls = []; // Delete all controls
        $entityJson = json_encode($entityJson);

        $updatedDataDictionary = $this->deserializeFromJson('Rednose\FrameworkBundle\Entity\DataDictionary', $entityJson, 'details', $dataDictionaryId);

        $this->em->persist($updatedDataDictionary);
        $this->em->flush();

        return array($dataDictionaryId, $entityJson);
    }

    /**
     * Check the persistend state in a different test to make sure doctrine fetched a new instance of the object.
     *
     * @depends testRemoveChildEntities
     */
    public function testRemoveChildEntitiesPersisted($state)
    {
        $dataDictionary = $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneById($state[0]);

        $this->assertTrue($dataDictionary->getControls()->count() === 0);
    }

    /**
     * @depends testUpdateAndKeepAllAssociatedEntitiesPersisted
     */
    public function testAddChildEntities($dataDictionaryId)
    {
        $fixture = json_decode($this->json);

        $entityJson = new \stdClass;
        $entityJson->controls = $fixture->controls; // Brand new shiny controls
        $entityJson = json_encode($entityJson);

        $updatedDataDictionary = $this->deserializeFromJson('Rednose\FrameworkBundle\Entity\DataDictionary', $entityJson, 'details', $dataDictionaryId);

        $this->em->persist($updatedDataDictionary);
        $this->em->flush();

        return array($dataDictionaryId, $entityJson);
    }

    /**
     * Check the persistend state in a different test to make sure doctrine fetched a new instance of the object.
     *
     * @depends testAddChildEntities
     */
    public function testAddChildEntitiesPersisted($state)
    {
        $fixture = json_decode($this->json);

        $dataDictionary = $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneById($state[0]);

        $this->assertTrue($dataDictionary->getControls()->get(0)->getChildren()->count() === count($fixture->controls[0]->children));
    }

    /**
     * @depends testUpdateAndKeepAllAssociatedEntitiesPersisted
     */
    public function testReplaceChildEntities($dataDictionaryId)
    {
        $dataDictionary = $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneById($dataDictionaryId);

        $fixture = json_decode($this->json);

        $entityJson = $this->serializeToJson($dataDictionary, 'details');
        $entityJson = json_decode($entityJson);
        $entityJson->controls = $fixture->controls; // Brand new shiny controls
        $entityJson = json_encode($entityJson, JSON_PRETTY_PRINT);

        $updatedDataDictionary = $this->deserializeFromJson('Rednose\FrameworkBundle\Entity\DataDictionary', $entityJson, 'details', $dataDictionaryId);

        $this->em->persist($updatedDataDictionary);
        $this->em->flush();

        return array($dataDictionaryId, $entityJson);
    }

    /**
     * Check the persistend state in a different test to make sure doctrine fetched a new instance of the object.
     *
     * @depends testReplaceChildEntities
     */
    public function testReplaceChildEntitiesPersisted($state)
    {
        $fixture = json_decode($this->json);

        $dataDictionary = $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneById($state[0]);

        $this->assertTrue($dataDictionary->getControls()->get(0)->getChildren()->count() === count($fixture->controls[0]->children));
    }


    /**
     * @depends testUpdateAndKeepAllAssociatedEntitiesPersisted
     */
    public function testMoveChildEntityToDifferentParent($dataDictionaryId)
    {
        $dataDictionary = $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneById($dataDictionaryId);

        $entityJson = $this->serializeToJson($dataDictionary, 'details');
        $entityJson = json_decode($entityJson);

        $moveControl = $entityJson->controls[0]->children[0];
        $idMovedTo   = 0;
        $idMoved     = $moveControl->id;
        unset($entityJson->controls[0]->children[0]);

        foreach ($entityJson->controls[0]->children as $child) {
            if ($child->type === 'collection') {
                $idMovedTo         = $child->id;
                $child->children[] = $moveControl;
            }
        }
        $entityJson = json_encode($entityJson, JSON_PRETTY_PRINT);

        $updatedDataDictionary = $this->deserializeFromJson('Rednose\FrameworkBundle\Entity\DataDictionary', $entityJson, 'details', $dataDictionaryId);

        $this->em->persist($updatedDataDictionary);
        $this->em->flush();

        return array($dataDictionaryId, $idMoved, $idMovedTo);
    }

    /**
     * Check the persistend state in a different test to make sure doctrine fetched a new instance of the object.
     *
     * @depends testMoveChildEntityToDifferentParent
     */
    public function testMoveChildEntityToDifferentParentPersisted($ids)
    {
        $found = false;
        $dataDictionary = $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneById($ids[0]);

        foreach ($dataDictionary->getControls()->get(0)->getChildren() as $control) {
            if ($control->getId() === $ids[2]) {
                foreach ($control->getChildren() as $childControl) {
                    if ($childControl->getId() === $ids[1]) {
                        $found = true;
                    }
                }
            }
        }

        $this->assertTrue(true === $found);

        return $dataDictionary->getId();
    }

    /**
     * @depends testMoveChildEntityToDifferentParentPersisted
     */
    public function testMoveChildEntityToDifferentParentAndRemoveOldParent($dataDictionaryId)
    {
        $dataDictionary = $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneById($dataDictionaryId);

        $entityJson = $this->serializeToJson($dataDictionary, 'details');
        $entityJson = json_decode($entityJson);

        // Find existing sub child
        foreach ($entityJson->controls[0]->children as $offset => $child) {
            if ($child->type === 'collection') {
                $idMoved   = $child->children[0]->id;
                $moveChild = $child->children[0];
                $moveChild->children[] = $child->children[1];

                // Remove child parent
                unset($entityJson->controls[0]->children[$offset]);

                break;
            }
        }
        // Create new unpersisted parent
        $newParent = new \stdClass;
        $newParent->name = 'NewTestParent';
        $newParent->type = 'collection';
        $newParent->children = array($moveChild);

        array_unshift($entityJson->controls, $newParent);

        $entityJson = json_encode($entityJson, JSON_PRETTY_PRINT);

        $updatedDataDictionary = $this->deserializeFromJson('Rednose\FrameworkBundle\Entity\DataDictionary', $entityJson, 'details', $dataDictionaryId);

        $this->em->persist($updatedDataDictionary);
        $this->em->flush();

        return array($dataDictionaryId, $idMoved);
    }

    /**
     * @depends testMoveChildEntityToDifferentParentAndRemoveOldParent
     */
    public function testMoveChildEntityToDifferentParentAndRemoveOldParentPersisted($ids)
    {
        $hasChild       = false;
        $childFound     = false;
        $newParentFound = false;

        $dataDictionary = $this->em->getRepository('Rednose\FrameworkBundle\Entity\DataDictionary')->findOneById($ids[0]);

        foreach ($dataDictionary->getControls() as $control) {
            if ($control->getName() === 'NewTestParent') {
                $newParentFound = true;

                if ($child = $control->getChildren()->get(0)) {
                    if ($child->getId() === $ids[1]) {
                        $childFound = true;
                        $hasChild   = ($child->getChildren()->count() > 0);
                    }
                }
            }

        }

        $this->assertEquals($dataDictionary->getControls()->count(), 2);
        $this->assertTrue(true === $newParentFound);
        $this->assertTrue(true === $childFound);
        $this->assertTrue(true === $hasChild);
    }

    /*
     * Test object properties based on provided json using getters and setters.
     *
     * @param object $object
     * @param string $json
     */
    private function assertObject($object, $json)
    {
        $reflectionObject = json_decode($json);

        $checkProperties = function ($self, $object, $reflectionObject) use (&$checkProperties) {
            $objectProperties = array_keys(get_object_vars($reflectionObject));

            foreach ($objectProperties as $property) {
                if ($property !== 'sort_order') {
                    $value = $object->{'get' . $property}();

                    if (is_string($value) || is_integer($value) || is_bool($value)) {
                        $self->assertEquals($value,  $reflectionObject->{$property});
                    } elseif ($value instanceOf Collection) {
                        if ($value->count() > 0) {
                            foreach ($reflectionObject->{$property} as $index => $childObject) {
                                $checkProperties($self, $value->get($index), $childObject);
                            }
                        }
                    }
                }
            }
        };


        $checkProperties(
            $this, $object, $reflectionObject
        );
    }

    /*
     * Deserialize entity
     *
     * @param string $className
     * @param string $json
     * @param string $group
     * @param integer $id
     * @return object
     */
    private function deserializeFromJson($className, $json, $group = 'file', $id = 0)
    {
        $context = new DeserializationContext();
        $context->setGroups(array($group));

        if ($id) {
            $context->setAttribute('id', $id);
        }

        $entity = $this->serializer->deserialize(
            $json, $className, 'json', $context
        );

        return $entity;
    }

    /*
     * Serialize entity and return json
     *
     * @param object $entity
     * @param string $group
     * @param integer $id
     * @return string
     */
    private function serializeToJson($entity, $group = 'file', $id = 0)
    {
        $context = new SerializationContext();
        $context->setGroups(array($group));

        if ($id) {
            $context->setAttribute('id', $id);
        }

        $json = $this->serializer->serialize(
            $entity, 'json', $context
        );

        return $json;
    }
}