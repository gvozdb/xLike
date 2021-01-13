<?php
$properties = array();
$tmp = array(
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

    'guest' => array(
        'type' => 'combo-boolean',
        'value' => true,
    ),
    'ip' => array(
        'type' => 'combo-boolean',
        'value' => true,
    ),
    'tpl' => array(
        'type' => 'textfield',
        'value' => 'tpl.xLike',
    ),

    'mode' => array(
        'type' => 'list',
        'options' => array(
            array('text' => 'db', 'value' => 'db'),
            array('text' => 'local', 'value' => 'local'),
        ),
        'value' => 'db',
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