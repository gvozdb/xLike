<?php
/** @var modX $modx */
/** @var xLike $xl */

$path = MODX_CORE_PATH . 'components/xlike/model/xlike/';
if (!is_object($modx->xlike)) {
    $xl = $modx->getService('xlike', 'xlike', $path);
} else {
    $xl = $modx->xlike;
}
$className = 'xl' . $modx->event->name;
$modx->loadClass('xlPlugin', $xl->config['pluginsPath'], true, true);
$modx->loadClass($className, $xl->config['pluginsPath'], true, true);
if (class_exists($className)) {
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
} else {
    // Удаляем событие у плагина, если такого класса не существует
    $event = $modx->getObject('modPluginEvent', array(
        'pluginid' => $modx->event->plugin->get('id'),
        'event' => $modx->event->name,
    ));
    if ($event instanceof modPluginEvent) {
        $event->remove();
    }
}
return;