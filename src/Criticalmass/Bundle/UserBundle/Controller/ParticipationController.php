<?php declare(strict_types=1);

namespace Criticalmass\Bundle\UserBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\Participation;
use Criticalmass\Component\Profile\Streak\StreakGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Criticalmass\Component\Profile\ParticipationTable\TableGeneratorInterface;

class ParticipationController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction(UserInterface $user, RegistryInterface $registry, TableGeneratorInterface $tableGenerator, StreakGeneratorInterface $streakGenerator): Response
    {
        $streakGenerator->setUser($user);

        $participationList = $this->getDoctrine()->getRepository(Participation::class)->findByUser($user, true);
        $participationTable = $tableGenerator->setUser($user)->generate()->getTable();

        return $this->render('UserBundle:Participation:list.html.twig', [
            'participationList' => $participationList,
            'currentStreak' => $streakGenerator->calculateCurrentStreak(new \DateTime(), true),
            'longestStreak' => $streakGenerator->calculateLongestStreak(),
        ]);
    }

    /**
     * @Security("is_granted('cancel', participation)")
     * @ParamConverter("participation", class="AppBundle:Participation", options={"id": "participationId"})
     */
    public function cancelAction(RegistryInterface $registry, Participation $participation): Response
    {
        $participation
            ->setGoingNo(true)
            ->setGoingMaybe(false)
            ->setGoingYes(false);

        $registry->getManager()->flush();

        return $this->redirectToRoute('criticalmass_user_participation_list');
    }

    /**
     * @Security("is_granted('delete', participation)")
     * @ParamConverter("participation", class="AppBundle:Participation", options={"id": "participationId"})
     */
    public function deleteAction(RegistryInterface $registry, Participation $participation): Response
    {
        $registry->getManager()->remove($participation);

        $registry->getManager()->flush();

        return $this->redirectToRoute('criticalmass_user_participation_list');
    }
}
