<?php

namespace ItemSetCalendar\Service\ControllerPlugin;

use Interop\Container\ContainerInterface;
use ItemSetCalendar\Mvc\Controller\Plugin\ItemSetCalendar;
use Zend\ServiceManager\Factory\FactoryInterface;

class ItemSetCalendarFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        $entityManager = $services->get('Omeka\EntityManager');
        $apiAdapterManager = $services->get('Omeka\ApiAdapterManager');

        return new ItemSetCalendar($entityManager, $apiAdapterManager);
    }
}
