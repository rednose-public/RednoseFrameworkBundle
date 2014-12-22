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

        $this->assertObject($dataDictionary, $entityJson);

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
    }

    /*
     * Test object properties based on provided xml using getters and setters.
     *
     * @param {object} $object
     * @param {string} $xml
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
     * @param json $json
     * @return string
     */
    private function deserializeFromJson($className, $json, $group = 'file')
    {
        $context = new DeserializationContext();
        $context->setGroups(array($group));

        $entity = $this->serializer->deserialize(
            $json, $className, 'json', $context
        );

        return $entity;
    }

    /*
     * Serialize entity and return json
     *
     * @param object $entity
     * @return string
     */
    private function serializeToJson($entity, $group = 'file')
    {
        $context = new SerializationContext();
        $context->setGroups(array($group));

        $json = $this->serializer->serialize(
            $entity, 'json', $context
        );

        return $json;
    }
}