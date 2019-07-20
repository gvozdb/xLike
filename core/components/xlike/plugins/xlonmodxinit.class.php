<?php

/**
 * Описание плагина
 */
class xlOnMODXInit extends xlPlugin
{
    public function run()
    {
        $this->map(array(
            'modResource' => array(
                'composites' => array(
                    'xlVotes' => array(
                        'class' => 'xlVote',
                        'local' => 'id',
                        'foreign' => 'parent',
                        'cardinality' => 'many',
                        'owner' => 'local',
                        'criteria' => array(
                            'foreign' => array(
                                'class' => 'modResource',
                            ),
                        ),
                    ),
                ),
            ),
            'modUser' => array(
                'composites' => array(
                    'xlVotes' => array(
                        'class' => 'xlVote',
                        'local' => 'id',
                        'foreign' => 'createdby',
                        'cardinality' => 'many',
                        'owner' => 'local',
                    ),
                ),
            ),
        ));
    }

    /**
     * @param array $map
     *
     * @return bool
     */
    public function map(array $map = array())
    {
        foreach ($map as $class => $data) {
            $this->modx->loadClass($class);

            foreach ($data as $tmp => $fields) {
                if ($tmp == 'fields') {
                    foreach ($fields as $field => $value) {
                        foreach (array('fields', 'fieldMeta', 'indexes') as $key) {
                            if (isset($data[$key][$field])) {
                                $this->modx->map[$class][$key][$field] = $data[$key][$field];
                            }
                        }
                    }
                } elseif ($tmp == 'composites' || $tmp == 'aggregates') {
                    foreach ($fields as $alias => $relation) {
                        if (!isset($this->modx->map[$class][$tmp][$alias])) {
                            $this->modx->map[$class][$tmp][$alias] = $relation;
                        }
                    }
                }
            }
        }

        return true;
    }
}