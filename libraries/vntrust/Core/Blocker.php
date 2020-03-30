<?php
if (! defined('IN_vnT')){
  die('Hacking attempt!');
}

/**
 * vnT_Blocker
 *
 */
class vnT_Blocker
{
    const INCORRECT_TEMPRORARY_DIRECTORY = 'Incorrect temprorary directory specified';
    const INCORRECT_IP_ADDRESS = 'Incorrect IP address specified';

    const LOGIN_RULE_NUMBER = 0;
    const LOGIN_RULE_TIMERANGE = 1;
    const LOGIN_RULE_END = 2;

    public $is_flooded;
    public $flood_block_time;
    public $login_block_end;

    private $logs_path;
    private $ip_addr;
    private $flood_rules = array(
        10 => 10, // rule 1 - maximum 10 requests in 10 secs
        60 => 30, // rule 2 - maximum 30 requests in 60 secs
        300 => 50, // rule 3 - maximum 50 requests in 300 secs
        3600 => 200 // rule 4 - maximum 200 requests in 3600 secs
    );
    private $login_rules = array(5, 5, 60);

    /**
     * vnT_Blocker::__construct()
     *
     * @param mixed $logs_path
     * @param mixed $rules
     * @param string $ip
     * @return void
     */
    public function __construct($logs_path, $ip = '')
    {
        if (!is_dir($logs_path)) {
            trigger_error(vnT_Blocker::INCORRECT_TEMPRORARY_DIRECTORY);
        }
        if (substr($logs_path, -1) != '/') {
            $logs_path .= '/';
        }

        if (empty($ip)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if (preg_match('#^(?:(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(?:\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$#', $ip)) {
            $ip2long = ip2long($ip);
        } else {
            if (substr_count($ip, '::')) {
                $ip = str_replace('::', str_repeat(':0000', 8 - substr_count($ip, ':')) . ':', $ip);
            }
            $ip = explode(':', $ip);
            $r_ip = '';
            foreach ($ip as $v) {
                $r_ip .= str_pad(base_convert($v, 16, 2), 16, 0, STR_PAD_LEFT);
            }
            $ip2long = base_convert($r_ip, 2, 10);
        }
        if ($ip2long == -1 or $ip2long === false) {
            trigger_error(vnT_Blocker::INCORRECT_IP_ADDRESS);
        }

        $this->logs_path = $logs_path;
        $this->ip_addr = $ip2long;
    }

    /**
     * vnT_Blocker::trackFlood()
     *
     * @param mixed $rules
     * @return void
     */
    public function trackFlood($rules = array())
    {
        if (!empty($rules)) {
            $this->flood_rules = $rules;
        }

        $info = $this->_get_info();
        foreach ($this->flood_rules as $interval => $limit) {
            if (!isset($info['access'][$interval])) {
                $info['access'][$interval]['time'] = time();
                $info['access'][$interval]['count'] = 0;
            }

            ++$info['access'][$interval]['count'];

            if (time() - $info['access'][$interval]['time'] > $interval) {
                $info['access'][$interval]['count'] = 1;
                $info['access'][$interval]['time'] = time();
            }

            if ($info['access'][$interval]['count'] > $limit) {
                $this->flood_block_time = 1 + (time() - $info['access'][$interval]['time'] - $interval) * -1;
                $this->is_flooded = true;
            }
        }

        if (empty($this->is_flooded)) {
            $this->_save_info($info);
        }
    }

    /**
     * vnT_Blocker::trackLogin()
     *
     * @param mixed $rules
     * @return void
     */
    public function trackLogin($rules = array())
    {
        if (!empty($rules)) {
            $this->login_rules = $rules;
        }
    }

    /**
     * vnT_Blocker::is_blocklogin()
     *
     * @param mixed $loginname
     * @return
     */
    public function is_blocklogin($loginname)
    {
        $blocked = false;

        if (!empty($loginname)) {
            $_loginname = md5($loginname);
            $info = $this->_get_info();

            if (isset($info['login'][$_loginname]) and $info['login'][$_loginname]['count'] >= $this->login_rules[vnT_Blocker::LOGIN_RULE_NUMBER]) {
                $this->login_block_end = $info['login'][$_loginname]['lasttime'] + ($this->login_rules[vnT_Blocker::LOGIN_RULE_END] * 60);
                if ($this->login_block_end > time()) {
                    $blocked = true;
                }
            }
        }

        return $blocked;
    }

    /**
     * vnT_Blocker::set_loginFailed()
     *
     * @param mixed $loginname
     * @param integer $time
     * @return void
     */
    public function set_loginFailed($loginname, $time = 0)
    {
        if (empty($time)) {
            $time = time();
        }

        if (!empty($loginname)) {
            $loginname = md5($loginname);
            $info = $this->_get_info();

            if (!isset($info['login'][$loginname]) or ($time - $info['login'][$loginname]['starttime']) > ($this->login_rules[vnT_Blocker::LOGIN_RULE_TIMERANGE] * 60)) {
                $info['login'][$loginname] = array();
                $info['login'][$loginname]['count'] = 0;
                $info['login'][$loginname]['starttime'] = $time;
                $info['login'][$loginname]['lasttime'] = 0;
            }

            $info['login'][$loginname]['count'] ++;
            $info['login'][$loginname]['lasttime'] = $time;

            $this->_save_info($info);

            return $info['login'][$loginname] ;
        }

    }

    /**
     * vnT_Blocker::reset_trackLogin()
     *
     * @param mixed $loginname
     * @return void
     */
    public function reset_trackLogin($loginname)
    {
        if (!empty($loginname)) {
            $loginname = md5($loginname);
            $info = $this->_get_info();
            unset($info['login'][$loginname]);
            $this->_save_info($info);
        }
    }

    /**
     *
     */
    public function resetTrackFlood()
    {
        $info = $this->_get_info();
        if (isset($info['access'])) {
            unset($info['access']);
        }
        $this->_save_info($info);
    }

    /**
     * vnT_Blocker::_get_info()
     *
     * @return
     */
    private function _get_info()
    {
        $info = array();
        $logfile = $this->_get_logfile();
        if (file_exists($logfile)) {
            $info = unserialize(file_get_contents($logfile));
        }

        return $info;
    }

    /**
     * vnT_Blocker::_save_info()
     *
     * @param mixed $info
     * @return
     */
    private function _save_info($info)
    {
        $logfile = $this->_get_logfile();
        return file_put_contents($logfile, serialize($info));
    }

    /**
     * vnT_Blocker::_get_logfile()
     *
     * @return
     */
    private function _get_logfile()
    {
        return $this->logs_path . $this->ip_addr . '.log';
    }
}
