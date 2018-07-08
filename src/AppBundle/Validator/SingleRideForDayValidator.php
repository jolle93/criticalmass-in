<?php declare(strict_types=1);

namespace AppBundle\Validator;

use AppBundle\Entity\Ride;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SingleRideForDayValidator extends ConstraintValidator
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @var Ride $ride
     */
    public function validate($ride, Constraint $constraint): void
    {
        if (!$ride->getId()) {
            // ride is created, there may not be any rides at this date
            $maxRidesPerDay = 0;
        } else {
            // ride is edited, there may be the previous saved entity
            $maxRidesPerDay = 1;
        }

        $city = $ride->getCity();

        $rideList = $this->registry->getRepository(Ride::class)->findRidesForCity($city);

        $foundRidesForSameDay = 0;

        /** @var Ride $oldRide */
        foreach ($rideList as $oldRide) {
            if ($oldRide->getDateTime()->format('Y-m-d') === $ride->getDateTime()->format('Y-m-d')) {
                ++$foundRidesForSameDay;
            }

            if ($foundRidesForSameDay > $maxRidesPerDay) {
                $this
                    ->context
                    ->buildViolation($constraint->message)
                    ->atPath('dateTime')
                    ->addViolation();

                break;
            }
        }
    }
}
