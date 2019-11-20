<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="pin", propertyType="string")
 */
class BoundingBoxQuery extends AbstractQuery implements ElasticQueryInterface
{
    /**
     * @Constraints\NotNull()
     * @Constraints\Type("float")
     * @Constraints\Range(min="-90", max="90")
     * @var float $northLatitude
     */
    protected $northLatitude;

    /**
     * @Constraints\NotNull()
     * @Constraints\Type("float")
     * @Constraints\Range(min="-90", max="90")
     * @var float $southLatitude
     */
    protected $southLatitude;

    /**
     * @Constraints\NotNull()
     * @Constraints\Type("float")
     * @Constraints\Range(min="-180", max="180")
     * @var float $eastLongitude
     */
    protected $eastLongitude;

    /**
     * @Constraints\NotNull()
     * @Constraints\Type("float")
     * @Constraints\Range(min="-180", max="180")
     * @var float $westLongitude
     */
    protected $westLongitude;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="bbNorthLatitude")
     */
    public function setNorthLatitude(float $northLatitude): BoundingBoxQuery
    {
        $this->northLatitude = $northLatitude;

        return $this;
    }

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="bbSouthLatitude")
     */
    public function setSouthLatitude(float $southLatitude): BoundingBoxQuery
    {
        $this->southLatitude = $southLatitude;

        return $this;
    }

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="bbEastLongitude")
     */
    public function setEastLongitude(float $eastLongitude): BoundingBoxQuery
    {
        $this->eastLongitude = $eastLongitude;

        return $this;
    }

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="bbWestLongitude")
     */
    public function setWestLongitude(float $westLongitude): BoundingBoxQuery
    {
        $this->westLongitude = $westLongitude;

        return $this;
    }
    
    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        $geoQuery = new \Elastica\Query\GeoBoundingBox('pin',
            [
                [$this->northLatitude, $this->southLatitude],
                [$this->westLongitude, $this->eastLongitude],
            ]);

        return $geoQuery;
    }
}