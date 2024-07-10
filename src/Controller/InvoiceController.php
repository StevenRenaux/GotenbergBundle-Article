<?php

namespace App\Controller;

use Faker\Factory;
use Sensiolabs\GotenbergBundle\Enumeration\PaperSize;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invoice', name: 'invoice_')]
class InvoiceController extends AbstractController
{
    #[Route('/pdf', 'pdf')]
    public function pdf(GotenbergPdfInterface $gotenbergPdf): Response
    {
        $invoiceData = $this->invoiceData();

        return $gotenbergPdf
            ->html()
            ->content('content.html.twig', [
                'purchases' => $invoiceData['purchases'],
                'invoice' => $invoiceData['invoice'],
            ])
            ->landscape()
            ->paperStandardSize(PaperSize::A4)
            ->generate()
        ;
    }

    private function invoiceData(): array
    {
        $factory = Factory::create();

        $allPurchases = [];
        for ($i = 0; $i < 20; $i++) {
            $allPurchases[] = [
                'orderId' => $factory->unixTime(),
                'period' => $factory->dateTimeBetween('- 1 week')->format('Y-m-d') . ' - ' . $factory->dateTime('now')->format('Y-m-d'),
                'description' => $factory->sentence(),
                'price' => $factory->randomFloat(2, 1),
                'quantity' => $factory->randomDigitNotZero(),
                'total' => $factory->randomFloat(2, 1),
            ];
        }

        return [
            'invoice' => [
                'id' => $factory->unixTime(),
                'date' => $factory->dateTime()->format('Y-m-d'),
                'due_date' => $factory->dateTime('+1 week')->format('Y-m-d'),
                'sub_total' => $factory->randomFloat(2, 1),
                'total' => $factory->randomFloat(2, 1),
            ],
            'client' => [
                'name' => $factory->company(),
                'address' => $factory->address(),
                'city' => $factory->city(),
            ],
            'purchases' => $allPurchases
        ];
    }
}
