<?php

$properties = array();

$tmp = array(
    'tpl' => array(
        'type' => 'textfield',
        'value' => 'tpl.xLike',
    ),
    'guest' => array(
        'type' => 'combo-boolean',
        'value' => true,
    ),
    'parent' => array(
        'type' => 'numberfield',
        'value' => '',
    ),
    'class' => array(
        'type' => 'textfield',
        'value' => 'modResource',
    ),
    'list' => array(
        'type' => 'textfield',
        'value' => 'default',
    ),
);

foreach ($tmp as $k => $v) {
    $properties[] = array_merge(array(
        'name' => $k,
        'desc' => PKG_NAME_SHORT . '_prop_' . $k,
        'lexicon' => PKG_NAME_LOWER . ':properties',
    ), $v);
}

return $properties;