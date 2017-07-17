<?php
/** @var modX $modx */
/** @var array $sources */

$settings = array();
$tmp = array(
    'main' => array(
        // 'some_setting' => array(
        //     'xtype' => 'combo-boolean',
        //     'value' => true,
        // ),
    ),
    'frontend' => array(
        'frontend_css' => array(
            'xtype' => 'textfield',
            'value' => '[[+cssUrl]]web/default.css',
        ),
        'frontend_js' => array(
            'xtype' => 'textfield',
            'value' => '[[+jsUrl]]web/default.js',
        ),
    ),
);

foreach ($tmp as $area => $rows) {
    foreach ($rows as $k => $v) {
        /** @var modSystemSetting $setting */
        $setting = $modx->newObject('modSystemSetting');
        $setting->fromArray(array_merge(array(
            'namespace' => PKG_NAME_LOWER,
            'area' => PKG_NAME_SHORT . '_' . $area,
            'key' => PKG_NAME_SHORT . '_' . $k,
        ), $v), '', true, true);

        $settings[] = $setting;
    }
}
unset($tmp, $area, $rows, $k, $v);

return $settings;