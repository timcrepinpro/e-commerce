<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\OrderRepository;
use App\Repository\CategoryRepository;
use Dompdf\Dompdf;
use Dompdf\Options;



final class BillController extends AbstractController
{
    #[Route('/editor/order/{id}/bill', name: 'app_bill')]
    public function index($id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);
        if (!$order) {
            throw $this->createNotFoundException('Commande introuvable');
        }

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont','Arial');

        $domPdf = new Dompdf($pdfOptions);

        $html = $this->renderView('bill/index.html.twig', [
           'order' =>$order,
        ]);

        $domPdf->loadHtml($html);
        $domPdf->render();

        // On renvoie le PDF via une Response Symfony (affichage dans le navigateur)
        return new Response($domPdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="facture-'.$order->getId().'.pdf"',
        ]);
    }


}
