<?php declare(strict_types=1);

namespace Tests\Controller\Api\RideApi;

use Tests\Controller\Api\AbstractApiControllerTest;

class OrderByParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Get 10 rides ordered by dateTime ascending.
     */
    public function testRideListOrderByDateTimeAscending(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?orderBy=dateTime&orderDirection=ASC');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 25.03.2011","description":null,"dateTime":1301079600,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 24.06.2011","description":null,"dateTime":1308942000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 29.07.2011","description":null,"dateTime":1311966000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Mainz 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":50.001452,"longitude":8.276696,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass London 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":51.50762,"longitude":-0.114708,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Esslingen 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":48.739864,"longitude":9.30718,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Berlin 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":52.500472,"longitude":13.423083,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.02.2015","description":null,"dateTime":1422817200,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Berlin 01.02.2015","description":null,"dateTime":1422817200,"location":null,"latitude":52.500472,"longitude":13.423083,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }

    /**
     * @testdox Get 10 rides ordered by dateTime descending.
     */
    public function testRideListOrderByDateTimeDescending(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?orderBy=dateTime&orderDirection=DESC');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 24.09.2050","description":null,"dateTime":2547658800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":"kidical-mass-hamburg-2035","title":"Critical Mass Hamburg 24.06.2035","description":null,"dateTime":2066324400,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass London 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":51.50762,"longitude":-0.114708,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Berlin 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":52.500472,"longitude":13.423083,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Esslingen 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":48.739864,"longitude":9.30718,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Mainz 01.12.2029","description":null,"dateTime":1890846000,"location":null,"latitude":50.001452,"longitude":8.276696,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.11.2029","description":null,"dateTime":1888254000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Berlin 01.11.2029","description":null,"dateTime":1888254000,"location":null,"latitude":52.500472,"longitude":13.423083,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass London 01.11.2029","description":null,"dateTime":1888254000,"location":null,"latitude":51.50762,"longitude":-0.114708,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }

    /**
     * @testdox Providing invalid order direction will not break things.
     */
    public function testRideListOrderByDateTimeInvalidOrder(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?orderBy=dateTime&orderDirection=FOO');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.03.2015","description":null,"dateTime":1425236400,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.03.2016","description":null,"dateTime":1456858800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.08.2016","description":null,"dateTime":1470078000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.09.2016","description":null,"dateTime":1472756400,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.03.2017","description":null,"dateTime":1488394800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.08.2017","description":null,"dateTime":1501614000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.02.2018","description":null,"dateTime":1517511600,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.12.2018","description":null,"dateTime":1543690800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.08.2019","description":null,"dateTime":1564686000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }

    /**
     * @testdox Providing invalid fields will not break api.
     */
    public function testRideListOrderByInvalidOrder(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/ride?orderBy=invalidField&orderDirection=DESC');

        $expectedContent = '[{"slug":null,"title":"Critical Mass Hamburg 01.01.2015","description":null,"dateTime":1420138800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.03.2015","description":null,"dateTime":1425236400,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.03.2016","description":null,"dateTime":1456858800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.08.2016","description":null,"dateTime":1470078000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.09.2016","description":null,"dateTime":1472756400,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.03.2017","description":null,"dateTime":1488394800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.08.2017","description":null,"dateTime":1501614000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.02.2018","description":null,"dateTime":1517511600,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.12.2018","description":null,"dateTime":1543690800,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null},{"slug":null,"title":"Critical Mass Hamburg 01.08.2019","description":null,"dateTime":1564686000,"location":null,"latitude":53.566676,"longitude":9.984711,"estimatedParticipants":null,"estimatedDistance":null,"estimatedDuration":null}]';

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIdLessJsonEquals($expectedContent, $client->getResponse()->getContent());
    }
}
