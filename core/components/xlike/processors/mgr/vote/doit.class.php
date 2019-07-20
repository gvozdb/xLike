<?php

class xlVoteProcessor extends modProcessor
{
    /** @var xLike $xl */
    protected $xl;

    /**
     * @return bool
     */
    public function initialize()
    {
        $path = MODX_CORE_PATH . 'components/xlike/model/xlike/';
        if (!$this->xl = $this->modx->getService('xlike', 'xLike', $path)) {
            return false;
        }
        $this->xl->initialize($this->modx->context->key);

        return parent::initialize();
    }

    /**
     * @return string
     */
    public function process()
    {
        if (!$class = $this->getProperty('class')) {
            return $this->failure($this->modx->lexicon('xl_err_ns'));
        }
        if (!$list = $this->getProperty('list')) {
            return $this->failure($this->modx->lexicon('xl_err_ns'));
        }
        if (!$parent = (int)$this->getProperty('parent')) {
            return $this->failure($this->modx->lexicon('xl_err_ns'));
        }
        if (!$value = (int)$this->getProperty('value')) {
            return $this->failure($this->modx->lexicon('xl_err_ns'));
        }

        // IP and Session ID
        $ip = $this->xl->tools->getIp();
        $session = session_id();

        // Собираем массив для выборки
        $condition['parent'] = $parent;
        $condition['class'] = $class;
        $condition['list'] = $list;
        if ($this->modx->user->id) {
            $condition['createdby'] = $this->modx->user->id;
        } else {
            if ($this->getProperty('ip', true)) {
                $condition[] = 'createdby = 0 AND (ip = "' . $ip . '" OR session = "' . $session . '")';
            } else {
                $condition[] = 'createdby = 0 AND session = "' . $session . '"';
            }
        }

        //
        if ($vote = $this->modx->getObject('xlVote', $condition)) {
            // $response = $this->xl->tools->failure('count', $condition);
            if ($vote->value == $value) {
                $action = 'remove';
                $params = array(
                    'id' => $vote->id,
                );
            } else {
                $action = 'update';
                $params = array_merge($vote->toArray(), array(
                    'value' => $value,
                ));
            }
        } else {
            $action = 'create';
            $params = array(
                'parent' => $parent,
                'class' => $class,
                'list' => $list,
                'ip' => $this->getProperty('ip', true),
                'value' => $value,
            );
        }

        // Запускаем действие, соответствующее намерению пользователя
        $this->modx->error->reset();
        $response = $this->xl->tools->runProcessor(('mgr/vote/' . $action), $params);
        if ($error = $this->xl->tools->formatProcessorErrors($response)) {
            return $this->failure(print_r($error, 1));
        }

        // Выборка всех лайков/дизлайков и рейтинга
        $data = $this->xl->getVotesData($parent, $class, $list);

        //
        $this->xl->tools->invokeEvent('xLikeOnVote', array(
            // 'action' => $action,
            // 'vote' => is_object($vote) ? $vote : null,
            // 'value' => $value,
            'parent' => $parent,
            'class' => $class,
            'list' => $list,
            'likes' => $data['likes'],
            'dislikes' => $data['dislikes'],
            'rating' => $data['rating'],
        ));
        // $this->modx->log(1, 'doit xLikeOnVote $response ' . print_r($response, 1));

        //
        if (empty($data)) {
            if ($response instanceof modProcessorResponse) {
                $data = $response->getObject();
            } elseif (is_array($response)) {
                $data = $response;
            } else {
                $data = array();
            }
        }

        return $this->success('', $data);
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('xlike:default');
    }
}

return 'xlVoteProcessor';