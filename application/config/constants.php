<?php  
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('NOMSIS', 'Sales de Jujuy - Sistema Mejora Continua');
define('ENPRODUCCION', false);
define('USER_DATA_SESSION', 'oc_sdj_logged');
define('MAILBCC', 'fmoraiz@gmail.com');
define('MAILS_ALWAYS_CC', 'jdecastro@orocobre.com');
define('MAIL_GG', 'ftorres@salesdejujuy.com');
define('GTE_MANT', 6);
define('ARRAY_MAILS_GERENTES_ORO', 'aaleman@orocobre.com, aapaza@orocobre.com, msanchez@orocobre.com, rluna@orocobre.com, srodriguez@orocobre.com, jbarry@orocobre.com, fernando.cornejo@boraxargentina.com');
define('MAIL_DEFECTO_DMS','gestionderiesgo@salesdejujuy.com, gestionderiesgo@boraxargentina.com');

define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

define('URL_BASE', "http://localhost/codeigniter/application/");
define('URL_BASE_SITIO', "http://localhost/codeigniter/");
//define('BASEPATH', "http://localhost/codeigniter/system/");
define('URL_BASE_FILE', URL_BASE_SITIO."application/");
define('URL_DMS_PLANTILLAS', URL_BASE_SITIO."downloads/dms_plantilla/");
define('URL_DMS_IMAGES', URL_BASE_SITIO."uploads/dms/images/");

define('PATH_DOMINIO', "c:/xampp/htdocs/codeigniter/");
define('PATH_BASE', "c:/xampp/htdocs/codeigniter/application/");
define('PATH_UPLOADS', PATH_DOMINIO."uploads/");
define('PATH_BASE_FILE', PATH_DOMINIO."application/");
define('PATH_DMS_PLANTILLAS', PATH_DOMINIO."docs/dms/plantillas/");
define('PATH_DMS_DOCS', PATH_UPLOADS."dms/documentos/");
define('PATH_DMS_DROPBOX', PATH_UPLOADS."dms/dropbox/");
define('PATH_DMS_IMAGES', PATH_UPLOADS."dms/images/");
define('PATH_REPO_FILES', PATH_DOMINIO."docs/reports/");
define('PATH_TAREAS_FILES', PATH_UPLOADS."tareas/");
define('PATH_VTO_FILES', PATH_UPLOADS."vto/documentos/");
define('PATH_RI_FILES', PATH_UPLOADS."rrii/");


define('PATH_CAPTCHA', PATH_DOMINIO."images/captcha/");

define('TAM_PAGINA',25);
define('KEYMD5','s4p0');


define('SEGMENTOS_DOM', 2);

//define('HOST_MAIL', "ssl://smtp.googlemail.com");
//define('USER_MAIL', "gestiondedocumentos@salesdejujuy.com");
//define('PASS_MAIL', "mineral321");
define('PUERTO_MAIL', "465");
define('HOST_MAIL', "ssl://smtp.googlemail.com");
define('SYS_MAIL', "smc@salesdejujuy.com");
define('USER_MAIL', "smc@salesdejujuy.com");
define('PASS_MAIL', "GgQGKPQVrwP97vc");
/* End of file constants.php */
/* Location: ./system/application/config/constants.php */

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code