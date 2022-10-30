<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Location;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends BaseController
{
    /**
     * @Operation(
     *     tags={"Location"},
     *     summary="Retrieve a list of locations of a city",
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("city", class="App:City")
     * @Route("/{citySlug}/location", name="caldera_criticalmass_rest_location_list", methods={"GET"}, options={"expose"=true})
     */
    public function listLocationAction(City $city): JsonResponse
    {
        $locationList = $this->managerRegistry->getRepository(Location::class)->findLocationsByCity($city);

        return $this->createStandardResponse($locationList);
    }

    /**
     * Show details of a specified location.
     *
     * @Operation(
     *     tags={"Location"},
     *     summary="Show details of a location",
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="locationSlug",
     *         in="path",
     *         description="Slug of the location",
     *         required=true,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("location", class="App:Location")
     * @Route("/{citySlug}/location/{locationSlug}", name="caldera_criticalmass_rest_location_show", methods={"GET"}, options={"expose"=true})
     */
    public function showLocationAction(Location $location): JsonResponse
    {
        return $this->createStandardResponse($location);
    }
}
