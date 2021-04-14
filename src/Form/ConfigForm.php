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
    }
}
