<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Facebook\FacebookEventRideApi;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideEstimateType;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideType;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\RideEstimate;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Weather;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class RideController extends AbstractController
{
    public function listAction(Request $request)
    {
        $ridesResult = $this->getRideRepository()->findRidesInInterval();

        $rides = array();

        foreach ($ridesResult as $ride) {
            $rides[$ride->getFormattedDate()][] = $ride;
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Ride:list.html.twig', 
            array(
                'rides' => $rides
            )
        );
    }

    public function showAction(Request $request, $citySlug, $rideDate)
    {
        $city = $this->getCheckedCity($citySlug);
        $rideDateTime = $this->getCheckedDateTime($rideDate);
        $ride = $this->getCheckedRide($city, $rideDateTime);
        
        $nextRide = $this->getRideRepository()->getNextRide($ride);
        $previousRide = $this->getRideRepository()->getPreviousRide($ride);

        /**
         * @var Weather $weather
         */
        $weather = $this->getWeatherRepository()->findCurrentWeatherForRide($ride);

        if ($weather) {
            $weatherForecast = $weather->getTemperatureEvening() . ' °C, ' . $weather->getWeatherDescription();
        } else {
            $weatherForecast = null;
        }

        if ($this->getUser()) {
            $participation = $this->getParticipationRepository()->findParticipationForUserAndRide($this->getUser(), $ride);
        } else {
            $participation = null;
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Ride:show.html.twig', 
            array(
                'city' => $city, 
                'ride' => $ride,
                'tracks' => $this->getTrackRepository()->findSuitableTracksByRide($ride),
                'photos' => $this->getPhotoRepository()->findPhotosByRide($ride),
                'subrides' => $this->getSubrideRepository()->getSubridesForRide($ride),
                'nextRide' => $nextRide,
                'previousRide' => $previousRide,
                'dateTime' => new \DateTime(),
                'weatherForecast' => $weatherForecast,
                'participation' => $participation
            )
        );
    }

    public function renderPhotosTabAction(Request $request, Ride $ride)
    {
        $photos = $this->getPhotoRepository()->findPhotosByRide($ride);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Ride:Tabs/GalleryTab.html.twig',
            [
                'ride' => $ride,
                'photos' => $photos,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function renderTracksTabAction(Request $request, Ride $ride)
    {
        $tracks = $this->getTrackRepository()->findSuitableTracksByRide($ride);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Ride:Tabs/TracksTab.html.twig',
            [
                'ride' => $ride,
                'tracks' => $tracks,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function renderPostsTabAction(Request $request, Ride $ride)
    {
        return $this->render(
            'CalderaCriticalmassSiteBundle:Ride:Tabs/PostsTab.html.twig',
            [
                'ride' => $ride,
            ]
        );
    }

    public function renderSubridesTabAction(Request $request, Ride $ride)
    {
        $subrides = $this->getSubrideRepository()->getSubridesForRide($ride);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Ride:Tabs/SubridesTab.html.twig',
            [
                'ride' => $ride,
                'subrides' => $subrides,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function renderStatisticTabAction(Request $request, Ride $ride)
    {
        return $this->render(
            'CalderaCriticalmassSiteBundle:Ride:Tabs/StatisticTab.html.twig',
            [
                'ride' => $ride,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function addestimateAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $rideEstimate = new RideEstimate();
        $rideEstimate->setUser($this->getUser());
        $rideEstimate->setRide($ride);

        $estimateForm = $this->createForm(
            new RideEstimateType(),
            $rideEstimate,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_ride_addestimate',
                    [
                        'citySlug' => $ride->getCity()->getMainSlugString(),
                        'rideDate' => $ride->getFormattedDate()
                    ]
                )
            ]
        );

        $estimateForm->handleRequest($request);

        if ($estimateForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($estimateForm->getData());
            $em->flush();

            /**
             * @var RideEstimateService $estimateService
             */
            $estimateService = $this->get('caldera.criticalmass.statistic.rideestimate');
            $estimateService->calculateEstimates($ride);
        }

        return $this->redirectToRoute($ride);
    }

    public function renderDetailsTabAction(Request $request, Ride $ride)
    {
        /**
         * @var Weather $weather
         */
        $weather = $this->getWeatherRepository()->findCurrentWeatherForRide($ride);

        if ($weather) {
            $weatherForecast = $weather->getTemperatureEvening() . ' °C, ' . $weather->getWeatherDescription();
        } else {
            $weatherForecast = null;
        }

        $estimateForm = $this->createForm(
            new RideEstimateType(),
            new RideEstimate(),
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_ride_addestimate',
                    [
                        'citySlug' => $ride->getCity()->getMainSlugString(),
                        'rideDate' => $ride->getFormattedDate()
                    ]
                )
            ]
        );

        $location = $this->getLocationRepository()->findLocationForRide($ride);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Ride:Tabs/DetailsTab.html.twig',
            [
                'ride' => $ride,
                'dateTime' => new \DateTime(),
                'estimateForm' => $estimateForm->createView(),
                'weatherForecast' => $weatherForecast,
                'location' => $location
            ]
        );
    }
}
