<?php

namespace ItemSetCalendar;

use ItemSetCalendar\Form\ConfigForm;
use Omeka\Module\AbstractModule;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;

class Module extends AbstractModule
{
    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);

        $acl = $this->getServiceLocator()->get('Omeka\Acl');
        $acl->allow(null, 'ItemSetCalendar\Controller\Site\Item');
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getConfigForm(PhpRenderer $renderer)
    {
        $services = $this->getServiceLocator();
        $settings = $services->get('Omeka\Settings');
        $forms = $services->get('FormElementManager');

        $form = $forms->get(ConfigForm::class);
        $form->setData([
            'item_sets' => $settings->get('itemsetcalendar_item_sets', []),
            'itemsetcalendar_show_empty_months' => $settings->get('itemsetcalendar_show_empty_months', false),
        ]);

        return $renderer->formCollection($form);
    }

    public function handleConfigForm(AbstractController $controller)
    {
        $services = $this->getServiceLocator();
        $settings = $services->get('Omeka\Settings');
        $forms = $services->get('FormElementManager');

        $form = $forms->get(ConfigForm::class);
        $form->setData($controller->params()->fromPost());
        if (!$form->isValid()) {
            $controller->messenger()->addErrors($form->getMessages());
            return false;
        }

        $formData = $form->getData();
        $settings->set('itemsetcalendar_item_sets', $formData['item_sets']);
        $settings->set('itemsetcalendar_show_empty_months', $formData['itemsetcalendar_show_empty_months'] ? true : false);

        return true;
    }
}
