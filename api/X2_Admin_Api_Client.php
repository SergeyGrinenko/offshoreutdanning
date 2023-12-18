<?php
if (!isset($_SESSION) && !headers_sent()) {
    session_start( [
        'read_and_close' => true,
    ] );
}

class X2_Admin_Api_Client
{
    const
        NOAUTH_NOUSER = 1,
        NOAUTH_LOGIN_FAIL = 2,
        NOAUTH_TOKEN_FAIL = 3,

        ENV_TEST = 'test',
        ENV_DEV = 'dev',
        ENV_STAGE = 'stage',
        ENV_PROD = 'prod';

    /** Singleton instance */
    private static $_instance = [];

    protected
        $user = null,
        $pass = null,
        $params = [],
        $authfail = false,
        $authfailcount = 0,
        $lastresult = null,
        $tokenApcKey = 'api_exsto_no_typo3',
        $token = null,
        $env = 'dev',
        $log = [],

        /* Don't ever try to foreward debuggging cookie again! Never initialize xdebug on two threads.
         * Leads to weird behaviour that takes time to debug. Use these settings instead and turn off debug-helper!
         */
        $xdebug = false,
        $debugKey = 'PHPSTORM',

        # Cache api-calls within same request
        $requestCache = [];

    protected
        $conf = [
        self::ENV_DEV => ['api' => 'https://admin-dev2022.exsto.no/api/:controller/:action'],
        self::ENV_PROD => ['api' => 'https://admin.exsto.no/api/:controller/:action']
    ],
        $curl;

    protected $options = [];
    protected $cookies = [];

    public function setGet($params = [])
    {
        if (!empty($params)) {
            $this->setParams($params);
        }
        return $this;
    }

    public function setPost($params)
    {
        $this->options[CURLOPT_POST] = 1;
        $this->setParams($params);
        return $this;
    }

    public function setParams($params)
    {
        $this->params = array_merge($this->params, $params);
    }

    public function setRoute($controller, $action)
    {
        $this->_setUrl(strtr($this->_getConf('api'), array(':controller' => $controller, ':action' => $action)));
        return $this;
    }

    protected function _setUrl($url)
    {
        $this->options[CURLOPT_URL] = $url;
    }

    protected function _getConf($str)
    {
        return $this->conf[$this->env][$str];
    }

    protected function auth()
    {

        if ($this->authfailcount > 2) return $this->lastresult;

        $backupoptions = $this->options;
        $backupparams = $this->params;

        $this->_reset();

        $this->setRoute('auth', 'login');
        $this->setPost(['username' => $this->getUser(), 'password' => $this->getPass()]);

        $res = $this->execute(true);

        if ($res['auth']['authenticated'] === true) {
            $this->log('Auth successful, setting token');
            $this->_setToken($res['auth']['token']);
        } else {
            $this->log('Auth fail');
            if (in_array($res['auth']['code'], [self::NOAUTH_NOUSER, self::NOAUTH_LOGIN_FAIL])) {
                $this->authfail = $res['auth']['reason'];
                $this->authfailcount++;
            }
        }

        $this->options = $backupoptions;
        $this->params = $backupparams;
        return $res;
    }

    /**
     * @param bool $auth
     * @param bool $returnJson
     * @return bool|array
     */
    public function execute($auth = false, $returnJson = false)
    {

        # If not doing auth add token and default parameters (Typo3 Page ID)
        if (!$auth) {
            $token = $this->getToken();

            if (empty($token)) {
                $this->log('Returning false, because empty(token)');
                $this->_reset();
                return false;
            }

            $this->setParams(['token' => $token]);

        }

        if (!empty($_GET['no_cache'])) $this->setParams(['no_cache' => 1]);

        # Add parameters to request
        if (isset($this->options[CURLOPT_POST])) {
            # We are doing a POST
            $this->options[CURLOPT_POSTFIELDS] = $this->params;
        } else {
            # We are doing a GET
            $this->options[CURLOPT_URL] .= '?' . http_build_query($this->params);
        }

        # Check if we have a cache of this query
        if (!$auth) {
            $cachekey = md5(serialize($this->options));
            $this->log('Cachekey is: ' . $cachekey);
            if (isset($this->requestCache[$cachekey])) {
                $this->log('Returning REQUEST_CACHE[$cachekey]');
                $this->_reset();
                return $this->requestCache[$cachekey];
            }
        }

        $this->curl = curl_init();
        if ($this->xdebug) $this->cookies['xdebug'] = 'XDEBUG_SESSION=' . $this->debugKey;
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
        if (!empty($this->cookies)) {
            curl_setopt($this->curl, CURLOPT_COOKIE, implode(';', array_values($this->cookies)));
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            'User-Agent: x2_restclient/curl',
            'Accept: application/json',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
        ]);

        curl_setopt_array($this->curl, $this->options);

        $this->log('Calling curl');
        $response = curl_exec($this->curl);
        $headersize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);

        curl_close($this->curl);

        $body = substr($response, $headersize);
        $this->log('Setting LASTRESULT');
        $this->lastresult = json_decode($body, true);
        if (empty($this->lastresult)) {
            $this->log('LASTRESULT is empty!');
        }

        if ($auth) {
            $this->log('Returning this->LASTRESULT');
            $this->_reset();
            return $this->lastresult;
        }


        if ($this->lastresult !== null && $this->lastresult['success']) {

            if ($this->lastresult['auth']['authenticated'] === false) {
                # Token probably expired
                # Do auth and execute again
                $this->log('Calling this->auth');
                $this->auth();
                $this->log('Returning another execute');
                $this->_reset();
                return $this->execute();
            } else {
                /** @noinspection PhpUndefinedVariableInspection */
                $this->requestCache[$cachekey] =& $this->lastresult['data']; // maybe reference is causing problem?
                $this->log('Returning this->LASTRESULT[data]');
                if (!isset($this->lastresult['data'])) {
                    $this->log('this->LASTRESULT[data] is not set');
                } else if (empty($this->lastresult['data'])) {
                    $this->log('this->LASTRESULT[data] is empty');
                }
                $this->_reset();
                if ($returnJson) {
                    return $body;
                }
                return $this->lastresult['data'];

            }
        }

        $this->_reset();
//        trigger_error('X2 API-request failed');
//        $this->log('X2 API-request failed, returning empty array');
        return [];
    }

    /**
     * @param $user
     * @param $pass
     * @return $this
     */
    public static function getInstance($user = null, $pass = null)
    {
        if (!isset(self::$_instance[$user])) {
            self::$_instance[$user] = new self($user, $pass);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        if ($pass !== null && $pass !== self::$_instance[$user]->getPass())
            /** @noinspection PhpUndefinedMethodInspection */
            self::$_instance[$user]->setPass($pass);

        return self::$_instance[$user];
    }

    protected function _reset()
    {
        $this->options = [];
        $this->params = [];
        return $this;
    }

    protected function __construct($user, $pass)
    {
        if (!empty($user)) $this->setUser($user);
        if (!empty($pass)) $this->setPass($pass);
    }

    protected function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    protected function getUser()
    {
        return $this->user;
    }

    protected function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    protected function getPass()
    {
        return $this->pass;
    }

    protected function _setToken($token)
    {
        $this->token = $token;

        $_SESSION[$this->tokenApcKey] = $token;
        return $this;
    }

    protected function getToken()
    {
        if ($this->token !== null)
            return $this->token;

        $token = $_SESSION[$this->tokenApcKey];
        if ($token) {
            $this->token = $token;
        } else {
            $this->auth();
        }


        return $this->token;
    }

    public function getLastResult()
    {
        return $this->lastresult;
    }

    /**
     * @param $bool
     * @return $this
     */
    public function setDebug($bool = true)
    {
        $this->xdebug = (bool)$bool;
        return $this;
    }

    public function setEnv($env)
    {
        $this->env = $env;
        return $this;
    }

    public function log($msg)
    {
        $this->log[] = $msg;
        return $this;
    }

}