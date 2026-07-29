<?php

namespace ItemSetCalendar\Listener;

use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\Mvc\MvcEvent;

class MvcListeners extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'onRoute']
        );
    }

    public function onRoute(MvcEvent $event)
    {
        $services = $event->getApplication()->getServiceManager();
        $logger = $services->get('Omeka\Logger');

        $routeMatch = $event->getRouteMatch();
        $isSite = $routeMatch->getParam('__SITE__');
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');
        $itemSetId = $routeMatch->getParam('item-set-id');

        if ($isSite && $controller === 'Omeka\\Controller\\Site\\Item' && $action === 'browse' && $itemSetId) {
            $settings = $services->get('Omeka\Settings');
            $itemSets = $settings->get('itemsetcalendar_item_sets', []);
            if (in_array($itemSetId, $itemSets)) {
                $routeMatch->setParam('controller', 'ItemSetCalendar\\Controller\\Site\\Item');
            }
        }
    }
}
