<?php

/**
 * Описание плагина
 */
class xlOnMODXInit extends xlPlugin
{
    public function run()
    {
        $this->xl->tools->systemMapExtend(array(
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
}