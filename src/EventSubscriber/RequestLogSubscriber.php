<?php

namespace App\EventSubscriber;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class RequestLogSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private bool $logEnabled;

    public function __construct(Security $security, bool $logEnabled)
    {

        $this->security = $security;
        $this->logEnabled = $logEnabled;
    }

    public static function getSubscribedEvents() : array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event) : void
    {
        if (!$this->logEnabled) {
            return;
        }
        $request = $event->getRequest();
        $method = $request->getMethod();
        $uri = $request->getRequestUri();
        $user = $this->security->getUser();
        $logger = new Logger('User');
        $logger->pushHandler(new StreamHandler('var/log/user.log'));
        if ($user && method_exists($user, 'getLog') && $user->getLog() === true) {
            if ($method === 'GET' || $method === 'POST'){
                $logger->info(sprintf('User %s сделал %s запрос %s',
                        $user->getUserIdentifier(),
                        $method,
                        $uri
                    )
                );

            }
        }
    }
}
