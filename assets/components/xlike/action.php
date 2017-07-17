<?php

/** @var modX $modx */
/** @var xLike $xl */

// Подключаем MODX
if (!isset($modx)) {
    define('MODX_API_MODE', true);
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
    $modx->getService('error', 'error.modError');
    $modx->getRequest();
    $modx->setLogLevel(modX::LOG_LEVEL_ERROR);
    $modx->setLogTarget('FILE');
    $modx->error->message = null;
    $modx->lexicon->load('default');
}
$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : $modx->context->get('key');
if ($ctx != $modx->context->get('key')) {
    $modx->switchContext($ctx);
}

// Подключаем класс xLike
if (!$xl = $modx->getService('xlike', 'xLike', MODX_CORE_PATH . 'components/xlike/model/xlike/')) {
    exit($modx->toJSON(array('success' => false, 'message' => 'Class xLike not found')));
}
$xl->initialize($ctx, array('jsonResponse' => true));

//
if (empty($_REQUEST['action']) || empty($_REQUEST['propkey'])) {
    exit($xl->tools->failure('Access denied'));
} else {
    $propkey = $_REQUEST['propkey'];
}

// Load script properties
$sp = @$_SESSION['xLike']['properties'][$propkey];
if (empty($sp) || !is_array($sp) || empty($sp['class']) || empty($sp['list'])) {
    exit($xl->tools->failure('Access denied'));
}

switch ($_REQUEST['action']) {
    /**
     * Голосование
     */
    case 'vote':
        $parent = (int)$_REQUEST['parent'];
        $value = (int)$_REQUEST['value'];
        if (empty($parent) || empty($value)) {
            $response = $xl->tools->failure('xl_err_ns');
            break;
        }

        // sleep(1);
        // $response = $xl->tools->failure('xl_err_guest');
        // break;

        $user = (int)($modx->user->id ?: 0);
        if (!(($sp['guest'] && empty($user)) || !empty($user))) {
            $response = $xl->tools->failure('xl_err_guest');
            break;
        }

        $response = $xl->tools->runProcessor('mgr/vote/doit', array(
            'class' => $sp['class'],
            'list' => $sp['list'],
            'parent' => $parent,
            'value' => $value,
        ));
        if ($error = $xl->tools->formatProcessorErrors($response)) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[xLike] Ошибка при попытке проголосовать: ' . print_r($error, 1));
            $response = $xl->tools->failure($error, $_REQUEST);
            break;
        } else {
            $response = $xl->tools->success('', $response->getObject());
        }
        break;

    default:
        $response = $xl->tools->failure('Access denied');
}

@session_write_close();
exit($response);