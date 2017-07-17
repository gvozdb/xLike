<?php

class xLikeHomeManagerController extends modExtraManagerController
{
    /** @var xLike $xl */
    public $xl;

    /**
     *
     */
    public function initialize()
    {
        $path = MODX_CORE_PATH . 'components/xlike/model/xlike/';
        $this->xl = $this->modx->getService('xlike', 'xLike', $path);

        parent::initialize();
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('xlike:default');
    }

    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }

    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('xlike');
    }

    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->xl->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->xl->config['cssUrl'] . 'mgr/bootstrap.buttons.css');

        $this->addJavascript($this->xl->config['jsUrl'] . 'mgr/xlike.js');

        $this->addJavascript($this->xl->config['jsUrl'] . 'mgr/misc/ux.js');
        $this->addJavascript($this->xl->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->xl->config['jsUrl'] . 'mgr/misc/combo.js');

        $this->addJavascript($this->xl->config['jsUrl'] . 'mgr/misc/default.grid.js');
        $this->addJavascript($this->xl->config['jsUrl'] . 'mgr/misc/default.window.js');

        $this->addJavascript($this->xl->config['jsUrl'] . 'mgr/widgets/objects.grid.js');
        $this->addJavascript($this->xl->config['jsUrl'] . 'mgr/widgets/objects.window.js');

        $this->addJavascript($this->xl->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->xl->config['jsUrl'] . 'mgr/sections/home.js');

        $this->addHtml('
            <script type="text/javascript">
                xLike.config = ' . json_encode($this->xl->config) . ';
                xLike.config[\'connector_url\'] = "' . $this->xl->config['connectorUrl'] . '";
                Ext.onReady(function() {
                    MODx.load({
                        xtype: "xlike-page-home",
                    });
                });
            </script>
        ');
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->xl->config['templatesPath'] . 'home.tpl';
    }
}