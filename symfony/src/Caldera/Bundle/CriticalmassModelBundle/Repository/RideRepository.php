<?php

namespace Caldera\Bundle\CriticalmassModelBundle\Repository;

use Application\Sonata\UserBundle\Entity\User;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\DateTime;

class RideRepository extends EntityRepository
{
    public function findCurrentRideForCity(City $city)
    {
        $dateTime = new \DateTime();

        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');

        $builder->where($builder->expr()->gte('ride.dateTime', '\''.$dateTime->format('Y-m-d h:i:s').'\''));
        $builder->andWhere($builder->expr()->eq('ride.city', $city->getId()));

        $builder->addOrderBy('ride.dateTime', 'DESC');

        $query = $builder->getQuery();
        $query->setMaxResults(1);

        $result = $query->getSingleResult();

        return $result;
    }

    public function findRecentRides($year = null, $month = null, $maxResults = null, $minParticipants = 0, $postShuffle = false)
    {
        $builder = $this->createQueryBuilder('ride');
        
        $builder->select('ride');
        
        $builder->where($builder->expr()->gte('ride.estimatedParticipants', $minParticipants));
        
        if ($month && $year) {
            $builder->andWhere($builder->expr()->eq('MONTH(ride.dateTime)', $month));
            $builder->andWhere($builder->expr()->eq('YEAR(ride.dateTime)', $month));
        }
        
        $builder->addOrderBy('ride.dateTime', 'DESC');
        
        $query = $builder->getQuery();
        
        if ($maxResults) {
            $query->setMaxResults($maxResults);
        }
        
        $result = $query->getResult();
        
        if ($postShuffle) {
            $result = array_rand($result);
        }
        
        return $result;
    }
    
    public function findRidesByLatitudeLongitudeDateTime($latitude, $longitude, \DateTime $dateTime)
    {
        $query = $this->getEntityManager()->createQuery('SELECT r AS ride FROM CalderaCriticalmassModelBundle:Ride r JOIN r.city c WHERE c.enabled = 1 AND SQRT((r.latitude - '.$latitude.') * (r.latitude - '.$latitude.') + (r.longitude - '.$longitude.') * (r.longitude - '.$longitude.')) < 0.1 AND DATE(r.dateTime) = \''.$dateTime->format('Y-m-d').'\' ORDER BY r.city DESC');

        $result = array();

        $tmp1 = $query->getResult();

        foreach ($tmp1 as $tmp2)
        {
            foreach ($tmp2 as $ride)
            {
                $result[] = $ride;
            }
        }

        return $result;
    }

    public function findCityRideByDate(City $city, \DateTime $dateTime)
    {
        $query = $this->getEntityManager()->createQuery('SELECT r AS ride FROM CalderaCriticalmassModelBundle:Ride r WHERE DATE(r.dateTime) = \''.$dateTime->format('Y-m-d').'\' AND r.city = '.$city->getId())->setMaxResults(1);

        $result = $query->getResult();

        $result = @array_pop($result);
        $result = @array_pop($result);

        return $result;
    }

    /**
     * Fetches all rides in a datetime range of three weeks before and three days after.
     *
     * @return array
     */
    public function findCurrentRides($order = 'ASC')
    {
        $startDateTime = new \DateTime();
        $startDateTimeInterval = new \DateInterval('P3W'); // three weeks ago
        $startDateTime->add($startDateTimeInterval);

        $endDateTime = new \DateTime();
        $endDateTimeInterval = new \DateInterval('P3D'); // three days after
        $endDateTime->sub($endDateTimeInterval);

        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->lte('ride.dateTime', '\''.$startDateTime->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->gte('ride.dateTime', '\''.$endDateTime->format('Y-m-d H:i:s').'\''));

        $builder->andWhere($builder->expr()->eq('ride.isArchived', '0'));

        $builder->orderBy('ride.dateTime', $order);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findEstimatedRides()
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->gt('ride.estimatedParticipants', 0));
        $builder->andWhere($builder->expr()->gt('ride.estimatedDuration', 0));
        $builder->andWhere($builder->expr()->gt('ride.estimatedDuration', 0));

        $builder->andWhere($builder->expr()->eq('ride.isArchived', 0));

        $builder->orderBy('ride.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findRidesInInterval(\DateTime $startDateTime = null, \DateTime $endDateTime = null)
    {
        if (!$startDateTime)
        {
            $startDateTime = new \DateTime();
        }

        if (!$endDateTime)
        {
            $endDate = new \DateTime();
            $dayInterval = new \DateInterval('P1M');
            $endDateTime = $endDate->add($dayInterval);
        }

        $query = $this->getEntityManager()->createQuery("SELECT r AS ride FROM CalderaCriticalmassModelBundle:Ride r JOIN r.city c WHERE c.enabled = 1 AND r.isArchived = 0 AND r.dateTime >= '".$startDateTime->format('Y-m-d')."' AND r.dateTime <= '".$endDateTime->format('Y-m-d')."' ORDER BY r.dateTime ASC, c.city ASC");

        $result = array();

        $tmp1 = $query->getResult();

        foreach ($tmp1 as $tmp2)
        {
            foreach ($tmp2 as $ride)
            {
                $result[] = $ride;
            }
        }

        return $result;
    }

    public function findRidesByYearMonth($year, $month)
    {
        $startDateTime = new \DateTime($year.'-'.$month.'-01');
        $endDateTime = new \DateTime($year.'-'.$month.'-'.$startDateTime->format('t'));

        return $this->findRidesInInterval($startDateTime, $endDateTime);
    }

    public function findRidesByDateTimeMonth(\DateTime $dateTime)
    {
        $startDateTime = $dateTime;
        $endDateTime = new \DateTime($startDateTime->format('Y-m-t'));

        return $this->findRidesInInterval($startDateTime, $endDateTime);
    }

    public function findLatestForCitySlug($citySlug)
    {
        $query = $this->getEntityManager()->createQuery('SELECT r AS ride FROM CalderaCriticalmassModelBundle:Ride r JOIN CalderaCriticalmassModelBundle:City c WITH c.id = r.city JOIN CalderaCriticalmassModelBundle:CitySlug cs WITH cs.city = c.id WHERE cs.slug = \''.$citySlug.'\' GROUP BY r.city ORDER BY r.dateTime DESC');

        $result = $query->setMaxResults(1)->getResult();

        $result = array_pop($result);
        $result = array_pop($result);

        return $result;
    }

    public function findLatestRidesOrderByParticipants(\DateTime $startDateTime, \DateTime $endDateTime)
    {
        $query = $this->getEntityManager()->createQuery('SELECT r AS ride FROM CalderaCriticalmassModelBundle:Ride r WHERE r.dateTime >= \''.$startDateTime->format('Y-m-d H:i:s').'\' AND r.dateTime <= \''.$endDateTime->format('Y-m-d H:i:s').'\' ORDER BY r.estimatedParticipants DESC');

        $result = array();

        $tmp1 = $query->getResult();

        foreach ($tmp1 as $tmp2)
        {
            foreach ($tmp2 as $ride)
            {
                $result[$ride->getCity()->getMainSlugString()] = $ride;
            }
        }
        return $result;
    }

    /**
     * @param Ride $ride
     * @return Ride
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @author maltehuebner
     * @since 2015-09-18
     */
    public function getPreviousRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->lt('ride.dateTime', '\''.$ride->getDateTime()->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->eq('ride.city', $ride->getCity()->getId()));
        $builder->addOrderBy('ride.dateTime', 'DESC');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }

    /**
     * @param Ride $ride
     * @return Ride
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @author maltehuebner
     * @since 2015-09-18
     */
    public function getNextRide(Ride $ride)
    {
        $builder = $this->createQueryBuilder('ride');

        $builder->select('ride');
        $builder->where($builder->expr()->gt('ride.dateTime', '\''.$ride->getDateTime()->format('Y-m-d H:i:s').'\''));
        $builder->andWhere($builder->expr()->eq('ride.city', $ride->getCity()->getId()));
        $builder->addOrderBy('ride.dateTime', 'ASC');
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        $result = $query->getOneOrNullResult();

        return $result;
    }
}

