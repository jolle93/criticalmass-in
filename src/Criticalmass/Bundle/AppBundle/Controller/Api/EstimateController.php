<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Api;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\RideEstimate;
use Criticalmass\Bundle\AppBundle\Model\CreateEstimateModel;
use Criticalmass\Bundle\AppBundle\Repository\ParticipationRepository;
use Criticalmass\Bundle\AppBundle\Statistic\RideEstimate\RideEstimateService;
use Criticalmass\Bundle\AppBundle\Traits\RepositoryTrait;
use Criticalmass\Bundle\AppBundle\Traits\UtilTrait;
use FOS\ElasticaBundle\Finder\FinderInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\User\UserInterface;

class EstimateController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    /**
     * You can add an estimation of ride participants like this:
     *
     * <pre>{
     *   "latitude": 53.549280,
     *   "longitude": 9.979589,
     *   "estimation": 6554,
     *   "dateTime": 1506710306
     * }</pre>
     *
     * You can also provide a city instead of coordinates:
     *
     * <pre>{
     *   "citySlug": "hamburg",
     *   "estimation": 6554,
     *   "dateTime": 1506710306
     * }</pre>
     *
     * If you do not provide <code>dateTime</code> it will use the current time.
     * 
     * @ApiDoc(
     *  resource=true,
     *  description="Adds an estimation to statistic"
     * )
     */
    public function createAction(Request $request, UserInterface $user): Response
    {
        $estimateModel = $this->deserializeRequest($request, CreateEstimateModel::class);

        $rideEstimation = $this->createRideEstimate($estimateModel);

        $this->getManager()->persist($rideEstimation);
        $this->getManager()->flush();

        $this->recalculateEstimates($rideEstimation);

        $view = View::create();
        $view
            ->setData($rideEstimation)
            ->setFormat('json')
            ->setStatusCode(200)
        ;

        return $this->handleView($view);
    }

    protected function createRideEstimate(CreateEstimateModel $model): RideEstimate
    {
        if (!$model->getDateTime()) {
            $model->setDateTime(new \DateTime());
        }

        $ride = $this->guessRide($model);

        $estimate = new RideEstimate();

        $estimate
            ->setEstimatedParticipants($model->getEstimation())
            ->setLatitude($model->getLatitude())
            ->setLongitude($model->getLongitude())
            ->setDateTime($model->getDateTime())
            ->setRide($ride)
        ;

        return $estimate;
    }

    protected function guessRide(CreateEstimateModel $model): ?Ride
    {
        $ride = null;

        if ($model->getCitySlug()) {
            $city = $this->getCityBySlug($model->getCitySlug());

            if (!$city) {
                return null;
            }

            $ride = $this->getRideRepository()->findCityRideByDate($city, $model->getDateTime());
        } elseif ($model->getLatitude() && $model->getLongitude()) {
            $ride = $this->findNearestRide($model);
        }

        return $ride;
    }

    protected function findNearestRide(CreateEstimateModel $model): ?Ride
    {
        /** @var FinderInterface $finder */
        $finder = $this->container->get('fos_elastica.finder.criticalmass.ride');

        $geoFilter = new \Elastica\Filter\GeoDistance(
            'pin',
            [
                'lat' => $model->getLatitude(),
                'lon' => $model->getLongitude()
            ],
            '25km'
        );

        $dateTimeFilter = new \Elastica\Filter\Term(['simpleDate' => $model->getDateTime()->format('Y-m-d')]);

        $boolFilter = new \Elastica\Filter\BoolAnd([$geoFilter, $dateTimeFilter]);

        $filteredQuery = new \Elastica\Query\Filtered(new \Elastica\Query\MatchAll(), $boolFilter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(1);
        $query->setSort(
            [
                '_geo_distance' =>
                    [
                        'pin' =>
                            [
                                $model->getLatitude(),
                                $model->getLongitude()
                            ],
                        'order' => 'asc',
                        'unit' => 'km'
                    ]
            ]
        );

        $results = $finder->find($query, 1);

        if (is_array($results)) {
            return array_pop($results);
        }

        return null;
    }

    protected function recalculateEstimates(RideEstimate $rideEstimate): void
    {
        if ($rideEstimate->getRide()) {
            /**
             * @var RideEstimateService $estimateService
             */
            $estimateService = $this->get('caldera.criticalmass.statistic.rideestimate');
            $estimateService->calculateEstimates($rideEstimate->getRide());
        }
    }
}