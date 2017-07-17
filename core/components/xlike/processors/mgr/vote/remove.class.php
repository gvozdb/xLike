<?php

class xlVoteRemoveProcessor extends modObjectProcessor
{
    public $objectType = 'xlVote';
    public $classKey = 'xlVote';
    public $languageTopics = array('xlike:default');
    // public $permission = 'remove';

    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }

        if (!$id = (int)$this->getProperty('id')) {
            $ids = $this->modx->fromJSON($this->getProperty('ids'));
            if (empty($ids)) {
                return $this->failure($this->modx->lexicon('xl_err_ns'));
            }
        } else {
            $ids[0] = $id;
        }

        foreach ($ids as $id) {
            /** @var xlVote $object */
            if (!$object = $this->modx->getObject($this->classKey, $id)) {
                return $this->failure($this->modx->lexicon('xl_err_nf'));
            }
            $array = $object->toArray();
            $object->remove();
        }

        return $this->success();
    }
}

return 'xlVoteRemoveProcessor';