<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory;

use App\Criticalmass\DataQuery\Query\BoundingBoxQuery;
use App\Criticalmass\DataQuery\Query\CityQuery;
use App\Criticalmass\DataQuery\Query\DateQuery;
use App\Criticalmass\DataQuery\Query\MonthQuery;
use App\Criticalmass\DataQuery\Query\RadiusQuery;
use App\Criticalmass\DataQuery\Query\RegionQuery;
use App\Criticalmass\DataQuery\Query\YearQuery;
use App\Entity\CitySlug;
use App\Entity\Region;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class QueryFactory
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function createFromRequest(Request $request): array
    {
        $queryList = [];

        if ($request->query->get('bbWestLongitude') && $request->query->get('bbEastLongitude') && $request->query->get('bbNorthLatitude') && $request->query->get('bbSouthLatitude')) {
            $westLongitude = (float)$request->query->get('bbWestLongitude');
            $eastLongitude = (float)$request->query->get('bbEastLongitude');
            $northLatitude = (float)$request->query->get('bbNorthLatitude');
            $southLatitude = (float)$request->query->get('bbSouthLatitude');

            $queryList[] = new BoundingBoxQuery($northLatitude, $southLatitude, $eastLongitude, $westLongitude);
        }

        if ($request->query->get('centerLatitude') && $request->query->get('centerLongitude') && $request->query->get('radius')) {
            $centerLatitude = (float)$request->query->get('centerLatitude');
            $centerLongitude = (float)$request->query->get('centerLongitude');
            $radius = (float)$request->query->get('radius');

            $queryList[] = new RadiusQuery($centerLatitude, $centerLongitude, $radius);
        }

        if ($request->query->get('year') && $request->query->get('month') && $request->query->get('day')) {
            $year = (int)$request->query->get('year');
            $month = (int)$request->query->get('month');
            $day = (int)$request->query->get('day');

            $queryList[] = new DateQuery($year, $month, $day);
        } elseif ($request->query->get('year') && $request->query->get('month')) {
            $year = (int)$request->query->get('year');
            $month = (int)$request->query->get('month');

            $queryList[] = new MonthQuery($year, $month);
        } elseif ($request->query->get('year')) {
            $year = (int)$request->query->get('year');

            $queryList[] = new YearQuery($year);
        }

        if ($request->query->get('region')) {
            $region = $this->registry->getRepository(Region::class)->findOneBySlug($request->query->get('region'));

            $queryList[] = new RegionQuery($region);
        }

        if ($request->query->get('citySlug')) {
            /** @var CitySlug $citySlug */
            $citySlug = $this->registry->getRepository(CitySlug::class)->findOneBySlug($request->query->get('citySlug'));

            $queryList[] = new CityQuery($citySlug->getCity());
        }

        return $queryList;
    }
}
