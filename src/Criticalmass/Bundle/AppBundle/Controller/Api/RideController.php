<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Api;

use Criticalmass\Bundle\AppBundle\Traits\RepositoryTrait;
use Criticalmass\Bundle\AppBundle\Traits\UtilTrait;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class RideController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns ride details"
     * )
     */
    public function showAction(string $citySlug, string $rideDate): Response
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns details of the next ride in the city"
     * )
     */
    public function showCurrentAction(string $citySlug): Response
    {
        $city = $this->getCheckedCity($citySlug);

        $ride = $this->getRideRepository()->findCurrentRideForCity($city);

        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * Get a list of critical mass rides.
     *
     * This list may be limited to city or region by providing a city or region slug. You may also limit the list to a specific month or a specific day.
     *
     * If you do not provide <code>year</code> and <code>month</code>, results will be limited to the current month.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Lists rides",
     *  parameters={
     *     {"name"="region", "dataType"="string", "required"=false, "description"="Provide a region slug"},
     *     {"name"="city", "dataType"="string", "required"=false, "description"="Provide a city slug"},
     *     {"name"="year", "dataType"="string", "required"=false, "description"="Limit the result set to this year. If not set, we will search in the current month."},
     *     {"name"="month", "dataType"="string", "required"=false, "description"="Limit the result set to this year. Must be combined with 'year'. If not set, we will search in the current month."},
     *     {"name"="day", "dataType"="string", "required"=false, "description"="Limit the result set to this day."}
     *  }
     * )
     */
    public function listAction(Request $request): Response
    {
        $region = null;
        $city = null;
        $dateTime = new \DateTime();
        $fullMonth = true;

        if ($request->query->get('region')) {
            $region = $this->getRegionRepository()->findOneBySlug($request->query->get('region'));

            if (!$region) {
                throw $this->createNotFoundException('Region not found');
            }
        }

        if ($request->query->get('city')) {
            $city = $this->getRegionRepository()->findOneBySlug($request->query->get('city'));

            if (!$city) {
                throw $this->createNotFoundException('Region not found');
            }
        }

        if ($request->query->get('year') && $request->query->get('month') && $request->query->get('day')) {
            $dateTime = new \DateTime(
                sprintf('%d-%d-%d',
                    $request->query->get('year'),
                    $request->query->get('month'),
                    $request->query->get('day')
                )
            );

            $fullMonth = false;
        } elseif ($request->query->get('year') && $request->query->get('month')) {
            $dateTime = new \DateTime(
                sprintf('%d-%d-01',
                    $request->query->get('year'),
                    $request->query->get('month')
                )
            );
        }

        $rideList = $this->getRideRepository()->findRides(
            $dateTime,
            $fullMonth,
            $city,
            $region
        );

        $context = new Context();
        $context
            ->addGroup('ride-list');

        $view = View::create();
        $view
            ->setData($rideList)
            ->setFormat('json')
            ->setStatusCode(200)
            ->setContext($context)
        ;

        return $this->handleView($view);
    }
}