<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Subscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubscriptionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $subscription = new Subscription();
        $subscription->setName("Basic Subscription");
        $subscription->setDescription("This is basic subscription");
        $subscription->setDuration(30);
        $subscription->setPrice(100);

        $manager->persist($subscription);

        $subscription = new Subscription();
        $subscription->setName("Premium Subscription");
        $subscription->setDescription("This is premium subscription");
        $subscription->setDuration(30);
        $subscription->setPrice(300);

        $manager->persist($subscription);

        $manager->flush();
    }
}
