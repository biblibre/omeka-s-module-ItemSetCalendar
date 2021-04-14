<?php

namespace ItemSetCalendar\Controller\Site;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ItemController extends AbstractActionController
{
    public function browseAction()
    {
        $itemSetId = $this->params()->fromRoute('item-set-id');
        $itemSets = $this->settings()->get('itemsetcalendar_item_sets', []);
        if (!in_array($itemSetId, $itemSets)) {
            return $this->forward()->dispatch('Omeka\Controller\Site\Item', $this->params()->fromRoute());
        }

        $view = new ViewModel;

        $itemSet = $this->api()->read('item_sets', $itemSetId)->getContent();
        $view->setVariable('itemSet', $itemSet);

        $date = $this->params()->fromQuery('date');
        $year = $this->params()->fromQuery('year');

        if (isset($date)) {
            $items = $this->itemSetCalendar()->getItemsByDate($itemSetId, $date);
            $view->setVariable('items', $items);
            $view->setVariable('date', $date);
        } elseif (isset($year)) {
            $items = $this->itemSetCalendar()->getItems($itemSetId, $year);

            $itemsByDay = [];
            $itemsByMonth = [];
            $itemsByYear = [];
            foreach ($items as $item) {
                $dateValues = $item->value('dcterms:date', ['all' => true, 'default' => []]);
                foreach ($dateValues as $dateValue) {
                    $date = $dateValue->value();
                    $matches = [];
                    if (preg_match('/^\s*(\d{4})-(\d{2})-(\d{2})\s*$/', $date, $matches)) {
                        if ($matches[1] === $year) {
                            $month = (int) $matches[2];
                            $day = (int) $matches[3];
                            $itemsByDay[$month][$day][$item->id()] = $item;
                        }
                    } elseif (preg_match('/^\s*(\d{4})-(\d{2})\s*$/', $date, $matches)) {
                        if ($matches[1] === $year) {
                            $month = (int) $matches[2];
                            $itemsByMonth[$month][$item->id()] = $item;
                        }
                    } elseif (preg_match('/^\s*(\d{4})\s*$/', $date, $matches)) {
                        if ($matches[1] === $year) {
                            $itemsByYear[$item->id()] = $item;
                        }
                    }
                }
            }

            $view->setVariable('itemsByDay', $itemsByDay);
            $view->setVariable('itemsByMonth', $itemsByMonth);
            $view->setVariable('itemsByYear', $itemsByYear);
            $view->setVariable('year', $year);
        } else {
            $years = $this->itemSetCalendar()->getYears($itemSetId);
            $view->setVariable('years', $years);
        }

        return $view;
    }
}
