<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\DataQuery\DataQueryManager\DataQueryManagerInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestToListConverter;
use App\Entity\City;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CityController extends BaseController
{
    /**
     * Get a list of critical mass cities.
     *
     * You may specify your query with the following parameters.
     *
     * <strong>Name</strong>
     *
     * Find a city by it's name with the <code>name</code> parameter.
     *
     * <strong>Regional query parameters</strong>
     *
     * Provide a <code>regionSlug</code> like <code>schleswig-holstein</code> to retrieve only cities of this region.
     *
     * <strong>List length</strong>
     *
     * The length of your results defaults to 10. Use <code>size</code> to request more or less results.
     *
     * <strong>Geo query parameters</strong>
     *
     * <ul>
     * <li>Radius query: Specify <code>centerLatitude</code>, <code>centerLongitude</code> and a <code>radius</code> to retrieve all results within this circle.</li>
     * <li>Bounding Box query: Fetch all cities in the box described by <code>bbNorthLatitude</code>, <code>bbEastLongitude</code> and <code>bbSouthLatitude</code>, <code>bbWestLongitude</code>.
     * </ul>
     *
     * <strong>Order parameters</strong>
     *
     * Sort the resulting list with the parameter <code>orderBy</code> and choose from one of the following properties:
     *
     * <ul>
     * <li><code>id</code></li>
     * <li><code>region</code></li>
     * <li><code>name</code></li>
     * <li><code>title</code></li>
     * <li><code>cityPopulation</code></li>
     * <li><code>latitude</code></li>
     * <li><code>longitude</code></li>
     * <li><code>updatedAt</code></li>
     * <li><code>createdAt</code></li>
     * </ul>
     *
     * Specify the order direction with <code>orderDirection=asc</code> or <code>orderDirection=desc</code>.
     *
     * You may use the <code>distanceOrderDirection</code> parameter in combination with the radius query to sort the result list by the city’s distance to the center coord.
     *
     * Apply <code>startValue</code> to deliver a value to start your ordered list with.
     *
     * @Operation(
     *     tags={"City"},
     *     summary="Returns a list of critical mass cities",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="Name of the city",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="regionSlug",
     *         in="body",
     *         description="Provide a region slug",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="centerLatitude",
     *         in="body",
     *         description="Latitude of a coordinate to search cities around in a given radius.",
     *         required=false,
     *         @SWG\Schema(type="number")
     *     ),
     *     @SWG\Parameter(
     *         name="centerLongitude",
     *         in="body",
     *         description="Longitude of a coordinate to search cities around in a given radius.",
     *         required=false,
     *         @SWG\Schema(type="number")
     *     ),
     *     @SWG\Parameter(
     *         name="radius",
     *         in="body",
     *         description="Radius to look around for cities.",
     *         required=false,
     *         @SWG\Schema(type="number")
     *     ),
     *     @SWG\Parameter(
     *         name="bbEastLongitude",
     *         in="body",
     *         description="East longitude of a bounding box to look for cities.",
     *         required=false,
     *         @SWG\Schema(type="number")
     *     ),
     *     @SWG\Parameter(
     *         name="bbWestLongitude",
     *         in="body",
     *         description="West longitude of a bounding box to look for cities.",
     *         required=false,
     *         @SWG\Schema(type="number")
     *     ),
     *     @SWG\Parameter(
     *         name="bbNorthLatitude",
     *         in="body",
     *         description="North latitude of a bounding box to look for cities.",
     *         required=false,
     *         @SWG\Schema(type="number")
     *     ),
     *     @SWG\Parameter(
     *         name="bbSouthLatitude",
     *         in="body",
     *         description="South latitude of a bounding box to look for cities.",
     *         required=false,
     *         @SWG\Schema(type="number")
     *     ),
     *     @SWG\Parameter(
     *         name="orderBy",
     *         in="body",
     *         description="Choose a property to sort the list by.",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="orderDirection",
     *         in="body",
     *         description="Sort ascending or descending.",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="distanceOrderDirection",
     *         in="body",
     *         description="Enable distance sorting in combination with radius query.",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="startValue",
     *         in="body",
     *         description="Start ordered list with provided value.",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="size",
     *         in="body",
     *         description="Length of resulting list. Defaults to 10.",
     *         required=false,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Parameter(
     *         name="extended",
     *         in="body",
     *         description="Set true to retrieve a more detailed list.",
     *         required=false,
     *         @SWG\Schema(type="boolean")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     */
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): Response
    {
        $queryParameterList = RequestToListConverter::convert($request);
        $cityList = $dataQueryManager->query($queryParameterList, City::class);

        $context = new Context();

        if ($request->query->has('extended') && true === $request->query->getBoolean('extended')) {
            $context->addGroup('extended-ride-list');
        }

        $context->addGroup('ride-list');

        $view = View::create();
        $view
            ->setContext($context)
            ->setData($cityList)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * Retrieve information for a city, which is identified by the parameter <code>citySlug</code>.
     *
     * @Operation(
     *     tags={"City"},
     *     summary="Shows a critical mass city",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("city", class="App:City")
     */
    public function showAction(City $city): Response
    {
        $view = View::create();
        $view
            ->setData($city)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
    }
}
