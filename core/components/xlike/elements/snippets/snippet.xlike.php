<?php
/** @var modX $modx */
/** @var xLike $xl */
/** @var array $scriptProperties */
$sp = &$scriptProperties;
if (!$xl = $modx->getService('xlike', 'xLike', (MODX_CORE_PATH . 'components/xlike/model/xlike/'), $sp)) {
    return 'Could not load xLike class!';
}
$xl->initialize($modx->context->key);

//
$tpl = $sp['tpl'] ?: 'tpl.xLike';
$sp['mode'] = $sp['mode'] ?: 'db';
$sp['guest'] = isset($sp['guest']) ? $sp['guest'] : true;
$sp['parent'] = (int)($sp['parent'] ?: $modx->resource->id);
$sp['class'] = $sp['class'] ?: 'modResource';
$sp['list'] = $sp['list'] ?: 'default';
if (empty($sp['parent']) || empty($sp['class']) || empty($sp['list'])) {
    return;
}
$ip = $xl->tools->getIp();
$session = session_id();

$pls = array(
    'parent' => $sp['parent'],
    'value' => 0,
    'likes' => 0,
    'dislikes' => 0,
    'rating' => '0.00',
);

// Выборка всех лайков/дизлайков и рейтинга
if ($sp['mode'] == 'db') {
    $pls = array_merge($pls, $xl->getVotesData($sp['parent'], $sp['class'], $sp['list']));
} elseif ($sp['mode'] == 'local') {
    foreach (array('likes', 'dislikes', 'rating') as $v) {
        $pls[$v] = $sp[$v] ?: 0;
    }
}

//
$user = (int)($modx->user->id ?: 0);
$pls['can'] = (($sp['guest'] && empty($user)) || !empty($user));

// Выборка установленного голоса
$q = $modx->newQuery('xlVote');
$q->select(array(
    'value',
));
$q->where(array(
    'parent' => $sp['parent'],
    'class' => $sp['class'],
    'list' => $sp['list'],
    'createdby' => $user,
));
if (!empty($sp['guest']) && empty($user)) {
    $q->where(array(
        '(ip = "' . $ip . '" OR session = "' . $session . '")',
    ));
}
$q->limit(1);
if ($q->prepare() && $q->stmt->execute()) {
    $pls['value'] = $q->stmt->fetchColumn();
}

// Записываем параметры сниппета в сессию
unset($sp['parent'], $sp['tpl'], $sp['mode'], $sp['likes'], $sp['dislikes'], $sp['rating']);
$pls['propkey'] = sha1(serialize($sp));
$_SESSION['xLike']['properties'][$pls['propkey']] = $sp;

// return $pls;

//
return $xl->tools->getChunk($tpl, $pls);