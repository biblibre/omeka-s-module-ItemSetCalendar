<?php
namespace ItemSetCalendar\Form;

use Omeka\Form\Element\ItemSetSelect;
use Laminas\Form\Form;

class ConfigForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => ItemSetSelect::class,
            'name' => 'item_sets',
            'options' => [
                'label' => 'Item sets', // @translate
                'disable_group_by_owner' => true,
            ],
            'attributes' => [
                'id' => 'item-sets',
                'multiple' => true,
                'class' => 'chosen-select',
            ],
        ]);

        $this->add([
            'name' => 'itemsetcalendar_show_empty_months',
            'type' => 'Laminas\Form\Element\Checkbox',
            'options' => [
                'label' => 'Show empty months', // @translate
                'info' => 'Show all months even when they contain no items', // @translate
            ],
        ]);
    }
}
