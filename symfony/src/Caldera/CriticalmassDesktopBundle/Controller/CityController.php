<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityController extends Controller
{
    public function listAction()
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findCities();

        return $this->render('CalderaCriticalmassDesktopBundle:City:list.html.twig', array('cities' => $cities));
    }

    public function showAction(Request $request, $citySlug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

        if (!$city->getEnabled())
        {
            throw new NotFoundHttpException('Wir konnten keine Stadt unter der Bezeichnung "'.$citySlug.'" finden :(');
        }

        $rides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findBy(array('city' => $city->getId()), array('dateTime' => 'DESC'));

        if ($city->getCurrentRide())
        {
            array_shift($rides);
            // shift the first ride from the array as the first one is the current and should not be displayed at the recent rides list
        }

        return $this->render('CalderaCriticalmassDesktopBundle:City:show.html.twig', array('city' => $city, 'rides' => $rides));
    }

    public function addAction(Request $request)
    {
        $city = new \Caldera\CriticalmassCoreBundle\Entity\City();

        $form = $this->createFormBuilder($city)
            ->add('city', 'text')
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('url', 'text')
            ->add('facebook', 'text')
            ->add('twitter', 'text')
            ->add('longitude', 'hidden')
            ->add('latitude', 'hidden')
            ->add('cityPopulation', 'text')
            ->add('punchLine', 'text')
            ->add('longDescription', 'textarea')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->persist($archiveCity);
            $em->flush();
        }

        return $this->render('CalderaCriticalmassDesktopBundle:City:add.html.twig', array('city' => $city, 'form' => $form->createView()));
    }

    public function editAction(Request $request, $citySlug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

        $form = $this->createFormBuilder($city)
            ->add('city', 'text')
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('url', 'text')
            ->add('facebook', 'text')
            ->add('twitter', 'text')
            ->add('longitude', 'hidden')
            ->add('latitude', 'hidden')
            ->add('cityPopulation', 'text')
            ->add('punchLine', 'text')
            ->add('longDescription', 'textarea')
            ->getForm();

        $archiveCity = clone $city;
        $archiveCity->setArchiveUser($this->getUser());
        $archiveCity->setArchiveParent($city);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->persist($archiveCity);
            $em->flush();
        }

        return $this->render('CalderaCriticalmassDesktopBundle:City:edit.html.twig', array('city' => $city, 'form' => $form->createView()));
    }
}
