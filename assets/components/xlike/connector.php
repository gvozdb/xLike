<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var xLike $xLike */
$xLike = $modx->getService('xlike', 'xLike', $modx->getOption('xlike_core_path', null, $modx->getOption('core_path') .
                                                                                                   'components/xlike/') .
                                                      'model/xlike/');
$modx->lexicon->load('xlike:default');

// handle request
$corePath = $modx->getOption('xlike_core_path', null, $modx->getOption('core_path') . 'components/xlike/');
$path = $modx->getOption('processorsPath', $xLike->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));