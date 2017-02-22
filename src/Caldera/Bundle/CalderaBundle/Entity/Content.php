<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Caldera\Bundle\CalderaBundle\EntityInterface\ArchiveableInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="content")
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\ContentRepository")
 */
class Content implements ViewableInterface, ArchiveableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="contents")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isPublicEditable = true;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $showInfobox = true;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $lastEditionDateTime = true;

    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="archive_contents")
     * @ORM\JoinColumn(name="archive_parent_id", referencedColumnName="id")
     */
    protected $archiveParent;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isArchived = false;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $archiveDateTime;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="archive_contents")
     * @ORM\JoinColumn(name="archive_user_id", referencedColumnName="id")
     */
    protected $archiveUser;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    protected $archiveMessage;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Content
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Content
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Content
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Content
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function __clone()
    {
        $this->id = null;
        $this->setIsArchived(true);
        $this->setArchiveDateTime(new \DateTime());
    }

    /**
     * Get isArchived
     *
     * @return boolean
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Set isArchived
     *
     * @param boolean $isArchived
     * @return Content
     */
    public function setIsArchived(bool $isArchived)
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    /**
     * Get archiveDateTime
     *
     * @return \DateTime
     */
    public function getArchiveDateTime()
    {
        return $this->archiveDateTime;
    }

    /**
     * Set archiveDateTime
     *
     * @param \DateTime $archiveDateTime
     * @return Content
     */
    public function setArchiveDateTime(\DateTime $archiveDateTime)
    {
        $this->archiveDateTime = $archiveDateTime;

        return $this;
    }

    /**
     * Get archiveParent
     *
     * @return Content
     */
    public function getArchiveParent()
    {
        return $this->archiveParent;
    }

    /**
     * Set archiveParent
     *
     * @param ArchiveableInterface $archiveParent
     * @return Content
     */
    public function setArchiveParent(ArchiveableInterface $archiveParent)
    {
        $this->archiveParent = $archiveParent;

        return $this;
    }

    /**
     * Get archiveUser
     *
     * @return User
     */
    public function getArchiveUser()
    {
        return $this->archiveUser;
    }

    /**
     * Set archiveUser
     *
     * @param User $archiveUser
     * @return Content
     */
    public function setArchiveUser(User $archiveUser)
    {
        $this->archiveUser = $archiveUser;

        return $this;
    }

    public function setArchiveMessage($archiveMessage)
    {
        $this->archiveMessage = $archiveMessage;

        return $this;
    }

    public function getArchiveMessage()
    {
        return $this->archiveMessage;
    }
    
    /**
     * Get isPublicEditable
     *
     * @return boolean
     */
    public function getIsPublicEditable()
    {
        return $this->isPublicEditable;
    }

    /**
     * Set isPublicEditable
     *
     * @param boolean $isPublicEditable
     * @return Content
     */
    public function setIsPublicEditable($isPublicEditable)
    {
        $this->isPublicEditable = $isPublicEditable;

        return $this;
    }

    /**
     * Get lastEditionDateTime
     *
     * @return \DateTime
     */
    public function getLastEditionDateTime()
    {
        return $this->lastEditionDateTime;
    }

    /**
     * Set lastEditionDateTime
     *
     * @param \DateTime $lastEditionDateTime
     * @return Content
     */
    public function setLastEditionDateTime($lastEditionDateTime)
    {
        $this->lastEditionDateTime = $lastEditionDateTime;

        return $this;
    }

    /**
     * Get showInfobox
     *
     * @return boolean
     */
    public function getShowInfobox()
    {
        return $this->showInfobox;
    }

    /**
     * Set showInfobox
     *
     * @param boolean $showInfobox
     * @return Content
     */
    public function setShowInfobox($showInfobox)
    {
        $this->showInfobox = $showInfobox;

        return $this;
    }

    /**
     * @return $this
     */
    public function incViews()
    {
        ++$this->views;

        return $this;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function setViews($views)
    {
        $this->views = $views;
    }

    public function archive(User $user): ArchiveableInterface
    {
        $archivedContent = clone $this;

        $archivedContent
            ->setIsArchived(true)
            ->setArchiveDateTime(new \DateTime())
            ->setArchiveParent($this)
            ->setArchiveUser($user)
            ->setArchiveMessage($this->archiveMessage);

        $this->archiveMessage = '';

        return $archivedContent;
    }
}