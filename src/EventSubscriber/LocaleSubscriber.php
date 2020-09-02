<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleSubscriber implements EventSubscriberInterface
{
    public function onRequestEvent(RequestEvent $event)
    {
        $request = $event->getRequest();
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            if ($lang = $request->get('lang')) {
                if (in_array($lang, ['ro', 'en'])) {
                    $request->setLocale($lang);
                }
            } else {
                $request->setLocale($request->getSession()->get('_locale', 'en'));
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => ['onRequestEvent', 17]
        ];
    }
}
