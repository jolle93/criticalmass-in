<?php

namespace Caldera\CriticalmassMobileBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
use Caldera\CriticalmassCoreBundle\Entity\Ride;
use Caldera\CriticalmassCoreBundle\Utility as Utility;

/**
 * Dieser Controller stellt realisiert den Administrationsbereich zur Touren-
 * verwaltung. Der Controller wurde automatisch ueber Symfonys CRUD-Faehigkei-
 * ten erzeugt und lediglich in den Templates sowie zum Verschicken von Be-
 * nachrichtigungen modifiziert.
 */
class RideController extends Controller implements Trackable
{

    /**
     * Lists all Ride entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CalderaCriticalmassMobileBundle:Ride')->findBy(array('city' => $this->getUser()->getCurrentCity()), array('date' => 'DESC'));

        return $this->render('CalderaCriticalmassMobileBundle:Ride:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Ride entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Ride();
        $form = $this->createForm(new RideType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_ride_show', array('id' => $entity->getId())));
        }

        return $this->render('CalderaCriticalmassMobileBundle:Ride:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Ride entity.
     *
     */
    public function newAction()
    {
        $entity = new Ride();
        $form   = $this->createForm(new RideType(), $entity);

        return $this->render('CalderaCriticalmassMobileBundle:Ride:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Ride entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ride entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CalderaCriticalmassMobileBundle:Ride:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Ride entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ride entity.');
        }

        $editForm = $this->createForm(new RideType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CalderaCriticalmassMobileBundle:Ride:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Ride entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Ride entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new RideType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_ride_edit', array('id' => $id)));
        }

        return $this->render('CalderaCriticalmassMobileBundle:Ride:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Ride entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Ride entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_ride'));
    }

    /**
     * Creates a form to delete a Ride entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

		/**
		 * Zeigt die Seite zum Verschicken von Push-Benachrichtigungen an.
		 *
		 * @param Integer $rideId: ID der Tour, fuer die Nachrichten verschickt wer-
		 * den sollen
		 */
		
		public function notificationsAction($rideId)
		{
			$ride = $this->getDoctrine()->getManager()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);

			return $this->render('CalderaCriticalmassMobileBundle:Ride:notifications.html.twig', array('ride' => $ride));
		}

		/**
		 * In dieser Methode werden nun endlich die Notifications verschickt. Es wird
		 * je nach Typ der Benachrichtigung eine entsprechende Instanz  der Benach-
		 * richtigung erstellt und an einen NotificationPusher zum Versand ueber-
		 * stellt.
		 *
		 * @param Integer $rideId: ID der Tour
		 * @param String $notificationType: Typ der Benachrichtigung
		 */
		public function sendnotificationsAction($rideId, $notificationType)
		{
			// Tour und Stadt auslesen
			$ride = $this->getDoctrine()->getManager()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);
			$users = $this->getDoctrine()->getManager()->getRepository('CalderaCriticalmassCoreBundle:User')->findByCurrentCity($ride->getCity());

			// je nach gewaehltem Benachrichtigungstyp verschiedene Instanzen erstellen
			switch ($notificationType)
			{
				case 'location':
					$notification = new Utility\Notifications\LocationPublishedNotification($ride);
					break;
				case 'time':
					$notification = new Utility\Notifications\TimePublishedNotification($ride);
					break;
				case 'ride':
					$notification = new Utility\Notifications\RideAnnouncementNotification($ride);
					break;
			}

			// NotificationPusher erstellen
			$np = new Utility\NotificationPusher\PushoverNotificationPusher($notification, $users);
			$np->setPushoverKey($this->container->getParameter('notifications.pushoverkey'));
			$np->sendNotification();

			return $this->render('CalderaCriticalmassMobileBundle:Ride:sendnotifications.html.twig', array('notificationsPusher' => $np));
		}
}
