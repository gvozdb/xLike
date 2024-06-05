<?php

class xLike
{
    /**
     * @var array $config
     */
    public $config = array();
    /**
     * @var array $initialized
     */
    public $initialized = array();
    /**
     * @var modX $modx
     */
    public $modx;
    /**
     * @var xlTools $tools
     */
    public $tools;
    /**
     * @var xlCrypter $crypter
     */
    public $crypter;
    /**
     * @var pdoTools $pdoTools
     */
    public $pdoTools;


    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx = &$modx;

        $corePath = MODX_CORE_PATH . 'components/xlike/';
        $assetsUrl = MODX_ASSETS_URL . 'components/xlike/';
        $assetsPath = MODX_ASSETS_PATH . 'components/xlike/';

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'assetsPath' => $assetsPath,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'actionUrl' => $assetsUrl . 'action.php',

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'pluginsPath' => $corePath . 'plugins/',
            'handlersPath' => $corePath . 'handlers/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',

            'prepareResponse' => false,
            'jsonResponse' => false,
        ), $config);

        $this->modx->addPackage('xlike', $this->config['modelPath']);
        $this->modx->lexicon->load('xlike:default');
    }


    /**
     * @param string $ctx
     * @param array  $sp
     *
     * @return boolean
     */
    public function initialize($ctx = 'web', $sp = array())
    {
        $this->config = array_merge($this->config, $sp, array('ctx' => $ctx));

        $this->getTools();
        if ($pdoTools = $this->getPdoTools()) {
            $pdoTools->setConfig($this->config);
        }

        if (empty($this->initialized[$ctx])) {
            switch ($ctx) {
                case 'mgr':
                    break;
                default:
                    // if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
                    //     $this->loadFrontendScripts();
                    // }
                    break;
            }
        }

        return ($this->initialized[$ctx] = true);
    }


    /**
     * @param string $objectName
     * @param array  $sp
     *
     * @return bool
     */
    public function loadFrontendScripts($objectName = '', array $sp = array())
    {
        if (empty($objectName)) {
            $objectName = 'xLike';
        }
        $objectName = trim($objectName);

        if (empty($this->modx->loadedjscripts[$objectName]) && (!defined('MODX_API_MODE') || !MODX_API_MODE)) {
            $pls = $this->tools->makePlaceholders($this->config);
            if ($css = trim($this->modx->getOption('xl_frontend_css'))) {
                $this->modx->regClientCSS(str_replace($pls['pl'], $pls['vl'], $css));
            }
            if ($js = trim($this->modx->getOption('xl_frontend_js'))) {
                $this->modx->regClientScript(str_replace($pls['pl'], $pls['vl'], $js));
            }

            $params = $this->modx->toJSON(array_merge(array(
                // 'assetsUrl' => $this->config['assetsUrl'],
                'actionUrl' => $this->config['actionUrl'],
            ), $sp));

            $this->modx->regClientScript('<script>
                if (typeof(' . $objectName . 'Cls) === "undefined") {
                    var ' . $objectName . 'Cls = new ' . $objectName . '(' . $params . ');
                }
            </script>', true);

            $this->modx->loadedjscripts[$objectName] = true;
        }

        return !empty($this->modx->loadedjscripts[$objectName]);
    }


    /**
     * @return xlTools
     */
    public function getTools()
    {
        if (!is_object($this->tools)) {
            if ($class = $this->modx->loadClass('tools.xlTools', $this->config['handlersPath'], true, true)) {
                $this->tools = new $class($this->modx, $this->config);
            }
        }

        return $this->tools;
    }


    /**
     * @param string $method
     *
     * @return xlCrypter
     */
    public function getCrypter($method = 'AES-256-CBC')
    {
        if (!is_object($this->crypter)) {
            if ($class = $this->modx->loadClass('crypter.xlCrypter', $this->config['handlersPath'], true, true)) {
                //
                $salt = md5(join('', [
                    $this->modx->getOption('site_name'),
                    $this->modx->getOption('error_page'),
                    $this->modx->getOption('default_template'),
                    $this->modx->getOption('mail_smtp_hosts'),
                    $this->modx->getOption('emailsender'),
                ]));

                //
                $this->crypter = new $class($salt, $method);
            }
        }

        return $this->crypter;
    }


    /**
     * @return pdoTools
     */
    public function getPdoTools()
    {
        if (class_exists('pdoTools') && !is_object($this->pdoTools)) {
            $this->pdoTools = $this->modx->getService('pdoTools');
        }

        return $this->pdoTools;
    }


    /**
     * @param $parent
     * @param $class
     * @param $list
     *
     * @return array
     */
    public function getVotesData($parent, $class, $list)
    {
        $data = array(
            'likes' => 0,
            'dislikes' => 0,
            'rating' => '0.00',
        );
        if (empty($parent) || empty($class) || empty($list)) {
            return $data;
        }

        // Выборка всех голосов
        $q = $this->modx->newQuery('xlVote')
            ->select(array(
                'SUM(value = 1) as likes',
                'SUM(value = -1) as dislikes',
            ))
            ->where(array(
                'parent' => $parent,
                'class' => $class,
                'list' => $list,
            ))
            ->limit(1)
            ->groupby('parent, class, list');
        if ($q->prepare() && $q->stmt->execute()) {
            if ($tmp = $q->stmt->fetch(PDO::FETCH_ASSOC) AND is_array($tmp)) {
                $data = array_merge($data, $tmp);

                // Считаем рейтинг
                $data['rating'] = (
                    (($data['likes'] + 1.9208) / ($data['likes'] + $data['dislikes']) -
                    1.96 * sqrt(($data['likes'] * $data['dislikes']) / ($data['likes'] + $data['dislikes']) + 0.9604) /
                    ($data['likes'] + $data['dislikes'])) / (1 + 3.8416 / ($data['likes'] + $data['dislikes'])) * 100
                );
                $data['rating'] = number_format($data['rating'], 2, '.', ' ');
            }
            unset($tmp);
        }
        if (empty($data['rating'])) {
            $data['rating'] = '0.00';
        }

        return $data;
    }
}