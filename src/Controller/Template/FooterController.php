<?php declare(strict_types=1);

namespace App\Controller\Template;

use App\Controller\AbstractController;
use App\Entity\BlogPost;
use App\Entity\City;
use App\Entity\Promotion;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends AbstractController
{
    public function blogPostListAction(RegistryInterface $registry): Response
    {
        $blogPostList = $registry->getRepository(BlogPost::class)->findAll();

        return $this->render('Template/Includes/_footer_blog_post_list.html.twig', [
            'blogPostList' => $blogPostList,
        ]);
    }

    public function promotionListAction(RegistryInterface $registry): Response
    {
        $promotionList = $registry->getRepository(Promotion::class)->findAll();

        return $this->render('Template/Includes/_footer_promotion_list.html.twig', [
            'promotionList' => $promotionList,
        ]);
    }

    public function cityListAction(RegistryInterface $registry): Response
    {
        $cityList = $registry->getRepository(City::class)->findPopularCities();

        return $this->render('Template/Includes/_footer_city_list.html.twig', [
            'cityList' => $cityList,
        ]);
    }
}