# Installation

    $ cd $ROUNDCUBE_HOME/plugins
    $ git clone https://github.com/OpenCSI/openam_authentication

# OpenAM configuration

Configure your agent to send mail attribute as USER_EMAIL http header.

* Connect to OpenAM
* Go to the agent tabs
* Click on your agent, select Application tab
* In **Profile Attributes Processing** section, select HTTP_HEADER
* Enter 'mail' as map key, and USER_EMAIL as map value and click on add button
* Save the agent

Could be a good idea to ensure all mandatory variables are ok by using phpinfo (check for
iPlanetDirectoryPro cookie and HTTP_USER_EMAIL).

# Roundcube configuration

Open the file openam_authentication/openam_authentication.php file to adjust followings variables

    public $cookieName = 'iPlanetDirectoryPro';
    public $userName = 'HTTP_USER_EMAIL';

Edit your main.inc.php file to add the plugin and define OpenURL

    $rcmail_config['plugins'] = array('openam_authentication');
    $rcmail_config['logout_url'] = 'https://sso.sample.com/openam/UI/Logout';
