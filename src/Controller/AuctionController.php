<?php

namespace App\Controller;
use App\Document\Auction;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class AuctionController extends AbstractController
{
    /**
     * @Route("/auction", name="auction")
     */
    public function index(DocumentManager $dm): Response
    {
        $auctions = $dm->getRepository(Auction::class)->findAll();
        if (! $auctions) {
            throw $this->createNotFoundException('No auctions found in DB');
        }
        return $this->render('auction/index.html.twig', [
            'auctions' => $auctions,

        ]);
    }
    /**
     * @Route("/product/list", name="productList")
     */
    public function listAction(DocumentManager $dm)
    {
        $products = $dm->getRepository(Product::class)->findAll();

        if (! $products) {
            throw $this->createNotFoundException('No product found in DB');
        }

        return $this->render('product/productList.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/auction/create", name="create")
     */
    public function create(Request $request, DocumentManager $dm): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('minPrice', IntegerType::class)
            ->add('send', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //here will be method, that saving data from form in base(array)
            $data = $form->getData();
            $auction = new Auction();
            $auction->setName($data['name']);
            $auction->setMinPrice($data['minPrice']);

            $dm->persist($auction);
            $dm->flush();
        }

        return $this->render('/auction/form_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
