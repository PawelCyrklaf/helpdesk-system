<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $method = $event->getRequest()->getMethod();
        $content = $event->getRequest()->getContent();

        if ($method == Request::METHOD_POST || $method == Request::METHOD_PUT) {
            if ($content == null || empty($content) || !isset($content)) {
                throw new BadRequestHttpException('Request body cannot be null');
            }
        }
        return true;
    }
}