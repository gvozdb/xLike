<?php

abstract class xlPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var xLike $xl */
    protected $xl;
    /** @var array $sp */
    protected $sp;

    public function __construct(&$modx, &$sp)
    {
        $this->sp = &$sp;
        $this->modx = &$modx;
        $this->xl = $this->modx->xlike;

        if (!is_object($this->xl)) {
            $path = MODX_CORE_PATH . 'components/xlike/model/xlike/';
            $this->xl = $this->modx->getService('xlike', 'xlike', $path, $this->sp);
        }
        if (!$this->xl->initialized[$this->modx->context->key]) {
            $this->xl->initialize($this->modx->context->key);
        }
    }

    abstract public function run();
}