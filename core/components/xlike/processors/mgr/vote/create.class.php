<?php

class xlVoteCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'xlVote';
    public $classKey = 'xlVote';
    public $languageTopics = array('xlike:default');
    // public $permission = 'create';
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
     * @return bool|string
     */
    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::beforeSave();
    }

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $check_ip = $this->getProperty('ip', true);

        // IP and Session ID
        $ip = $this->xl->tools->getIp();
        $session = session_id();

        //
        $this->setDefaultProperties(array(
            'class' => 'modResource',
            'list' => 'default',
        ));
        $this->setProperty('ip', $ip);
        $this->setProperty('session', $session);
        $this->setProperty('createdby', $this->modx->user->id);
        $this->setProperty('createdon', time());
        $this->setProperty('updatedon', time());
        $this->setProperty('value', ($this->getProperty('value') > 0 ? 1 : -1));

        // Проверяем на заполненность
        $required = array(
            'parent',
            'ip',
            // 'session',
            'value',
        );
        $this->xl->tools->checkProcessorRequired($this, $required, 'xl_err_required');

        // Проверяем на уникальность
        $unique = array(
            'parent',
        );
        if ($this->modx->user->id) {
            $condition['class'] = $this->getProperty('class');
            $condition['list'] = $this->getProperty('list');
            $condition['createdby'] = $this->modx->user->id;
            $this->xl->tools->checkProcessorUnique('', 0, $this, $unique, 'xl_err_unique', $condition);
        } else {
            foreach (array('ip', 'session') as $v) {
                if ((empty($check_ip) && $v === 'ip') || (empty($session) && $v === 'session')) {
                    continue;
                }
                $condition = array(
                    'class' => $this->getProperty('class'),
                    'list' => $this->getProperty('list'),
                    'createdby' => 0,
                    $v => $$v,
                );
                $this->xl->tools->checkProcessorUnique('', 0, $this, $unique, 'xl_err_unique', $condition);
            }
        }

        return parent::beforeSet();
    }

    /**
     * @return bool
     */
    public function afterSave()
    {
        return parent::afterSave();
    }
}

return 'xlVoteCreateProcessor';