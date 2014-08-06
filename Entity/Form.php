<?php

/*
 * This file is part of the RednoseFrameworkBundle package.
 *
 * (c) RedNose <http://www.rednose.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rednose\FrameworkBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rednose\FrameworkBundle\Model\Form as BaseForm;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Default ControlForm
 *
 * @ORM\Entity
 * @ORM\Table(name="rednose_framework_form")
 *
 * @Serializer\XmlNamespace(uri="http://rednose.nl/schema/form")
 * @Serializer\XmlRoot("form")
 * @Serializer\AccessorOrder("custom", custom = {"id", "name", "identity", "content", "controls"})
 */
class Form extends BaseForm
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     * @Serializer\Groups({"file", "list", "details"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Please enter a name.")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
     protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $identity;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\NotBlank(message="Please enter a form caption.")
     *
     * @Serializer\XmlAttribute
     * @Serializer\Groups({"file", "details"})
     */
    protected $caption;

    /**
     * @ORM\OneToMany(
     *   targetEntity="FormSection",
     *   orphanRemoval=true,
     *   mappedBy="form",
     *   cascade={"persist", "remove"})
     * @ORM\OrderBy({"sortOrder" = "ASC"})
     *
     * @Serializer\SerializedName("sections")
     * @Serializer\Groups({"file", "details"})
     * @Serializer\XmlList(inline = true, entry = "section")
     */
    protected $sections;

    // -- Serializer Methods ---------------------------------------------------

    /**
     * @Serializer\PostDeserialize
     */
    public function postDeserialize()
    {
        if ($this->sections) {
            $sortOrder = 0;

            foreach ($this->sections as $section) {
                $section->setForm($this);
                $section->setSortOrder($sortOrder++);
            }
        }
    }
}
