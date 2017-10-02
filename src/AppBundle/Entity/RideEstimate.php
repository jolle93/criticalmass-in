<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RideEstimateRepository")
 * @ORM\Table(name="ride_estimate")
 */
class RideEstimate
{
    /**
     * ID der Entitaet.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="estimates", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * Tour, zu der diese Entitaet abgespeichert wurde.
     *
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="estimates", fetch="LAZY")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\OneToOne(targetEntity="Track", mappedBy="rideEstimate", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="track_id", referencedColumnName="id")
     */
    protected $track;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Assert\Regex("/^([0-9]{1,6})$/")
     */
    protected $estimatedParticipants;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Regex("/^([0-9]{1,2})([\.,]*)([0-9]{0,5})$/")
     */
    protected $estimatedDistance;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Regex("/^([0-9]{1,2})([\.,]*)([0-9]{0,4})$/")
     */
    protected $estimatedDuration;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationDateTime;

    public function __construct()
    {
        $this->creationDateTime = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstimatedParticipants(): ?int
    {
        return $this->estimatedParticipants;
    }

    public function setEstimatedParticipants(int $estimatedParticipants = null): RideEstimate
    {
        $this->estimatedParticipants = $estimatedParticipants;

        return $this;
    }

    public function getEstimatedDistance(): ?float
    {
        return $this->estimatedDistance;
    }

    public function setEstimatedDistance(float $estimatedDistance = null): RideEstimate
    {
        $this->estimatedDistance = $estimatedDistance;

        return $this;
    }

    public function getEstimatedDuration(): ?float
    {
        return $this->estimatedDuration;
    }

    public function setEstimatedDuration(float $estimatedDuration = null): RideEstimate
    {
        $this->estimatedDuration = $estimatedDuration;

        return $this;
    }

    public function getCreationDateTime(): \DateTime
    {
        return $this->creationDateTime;
    }

    public function setCreationDateTime(\DateTime $creationDateTime): RideEstimate
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user = null): RideEstimate
    {
        $this->user = $user;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride = null): RideEstimate
    {
        $this->ride = $ride;

        return $this;
    }

    public function getTrack(): ?Track
    {
        return $this->track;
    }

    public function setTrack(Track $track = null): RideEstimate
    {
        $this->track = $track;

        return $this;
    }
}
