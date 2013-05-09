<?php

/**
 * OpenAM Authentication
 *
 * Use OpenAM cookie (by default iPlanetDirectory) as password. Your IMAP server must be configured
 * to support token authentication, for example by using https://github.com/OpenCSI/pam_openam
 *
 * Configuration:
 * // redirect the client to this URL after logout. This page is then responsible to clear HTTP auth
 * $rcmail_config['logout_url'] = 'https://sso.opencsi.com/openam/UI/Logout';
 *
 * @version @package_version@
 * @license GNU GPLv3+
 * @author Bruno Bonfils
 * @author Thomas Bruederli
 */
class openam_authentication extends rcube_plugin
{
  public $task = 'login|logout';
  public $cookieName = 'iPlanetDirectoryPro';
  public $userName = 'HTTP_USER_EMAIL'; /* Add HTTP_ as prefix of the variable you defined in OpenAM */

  function init()
  {
    write_log('userlogins', 'OpenAM authentication module called');
    $this->add_hook('startup', array($this, 'startup'));
    $this->add_hook('authenticate', array($this, 'authenticate'));
    $this->add_hook('logout_after', array($this, 'logout'));
  }

  function startup($args)
  {
    // change action to login
    if (empty($args['action']) && empty($_SESSION['user_id'])
        && !empty($_SERVER[$this->userName]) && !empty($_COOKIE[$this->cookieName]))
      $args['action'] = 'login';

    return $args;
  }

  function authenticate($args)
  {
    write_log('userlogins', sprintf('Trying to authenticate %s with token %s', $_SERVER[$this->userName], $_COOKIE[$this->cookieName]));
    // Allow entering other user data in login form,
    // e.g. after log out (#1487953)
    if (!empty($args['user'])) {
        return $args;
    }

    if (!empty($_SERVER[$this->userName]) && !empty($_COOKIE[$this->cookieName])) {
      $args['user'] = $_SERVER[$this->userName];
      $args['pass'] = $_COOKIE[$this->cookieName];
    }

    $args['cookiecheck'] = false;
    $args['valid'] = true;

    return $args;
  }
  
  function logout($args)
  {
    // redirect to configured URL in order to clear HTTP auth credentials
    if (!empty($_SERVER[$this->userName]) && $args['user'] == $_SERVER[$this->userName] && ($url = rcmail::get_instance()->config->get('logout_url'))) {
      header("Location: $url", true, 307);
    }
  }

}

