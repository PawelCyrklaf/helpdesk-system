<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Service\SubscriptionService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionController extends AbstractFOSRestController
{
    private SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function add(Request $request, Subscription $subscription)
    {

    }

    public function update(Request $request, Subscription $subscription)
    {

    }

    public function remove(Subscription $subscription)
    {

    }

    public function details(Subscription $subscription)
    {

    }

    public function list(Request $request)
    {

    }
}
