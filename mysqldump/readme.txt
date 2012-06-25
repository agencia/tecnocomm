MySQL database backup class, version 1.0.0

HOW TO USE

1. Create the instance of MySQL_Backup class.
2. Define necessary properties.
3. Call Execute() method to create backup.

require_once 'mysql_backup.class.php';
$backup_obj = new MySQL_Backup();
$backup_obj->server = 'localhost';
$backup_obj->username = 'username';
$backup_obj->password = 'password';
$backup_obj->database = 'dbname';
$backup_obj->tables = array();
$backup_obj->drop_tables = true;
$backup_obj->struct_only = false;
$backup_obj->comments = true;
$backup_obj->fname_format = 'd_m_y__H_i_s';
if (!$backup_obj->Execute(MSB_DOWNLOAD, '', true))
{
  die($backup_obj->error);
}


PUBLIC PROPERTIES

var $server = 'localhost';
The name of MySQL server.

var $port = 3306;
The port of MySQl server.

var $username = 'root';
Database username.

var $password = '';
Database password.

var $database = '';
Name of the database.

var $link_id = -1;
MySQL link identifier of the current connection. You can set this if you
want to connect the MySQL server by your own.

var $connected = false;
Set true if the connection is already established before calling Execute().

var $tables = array();
Tables you want to backup. All tables in the database will be backed up if
this array is empty.

var $drop_tables = true;
Add DROP TABLE IF EXISTS queries before CREATE TABLE in backup file.

var $struct_only = false;
Only structure of the tables will be backed up if true.

var $comments = true;
Include comments in backup file if true.

var $backup_dir = '';
Directory on the server where the backup file will be placed. Used only if task
parameter equals to MSB_SAVE in Execute() method.

var $fname_format = 'd_m_y__H_i_s';
Default file name format.

var $error = '';
Error message.


PUBLIC METHODS

function Execute($task = MSB_STRING, $fname = '', $compress = false)
$task - operation to perform: MSB_STRING - return SQL commands as a string;
  MSB_SAVE - create the backup file on the server; MSB_DOWNLOAD - save backup
  file in the user's computer.
$fname - optional name of backup file.
$compress - use GZip compression?

