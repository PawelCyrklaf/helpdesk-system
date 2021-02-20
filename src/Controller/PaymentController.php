<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Service\PaymentService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractFOSRestController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function add(Request $request, Payment $payment)
    {

    }

    public function update(Request $request, Payment $payment)
    {

    }

    public function remove(Payment $payment)
    {

    }

    public function details(Payment $payment)
    {

    }

    public function list(Request $request)
    {

    }
}
