<?php
	/*
		|--------------------------------------------------|
		|	Example MySQL Backup File                      |
		|	                                               |
		|	Written by: Justin Keller <kobenews@cox.net>   |
		|   Released under GNU Public license.             |
		|                                                  |
		|	Only use with MySQL database backup class,     |
		|	version 1.0.0 written by Vagharshak Tozalakyan |
		|	<vagh@armdex.com>.                             |
		|--------------------------------------------------|
	*/
	
	require_once 'mysql_backup.class.php';
	$backup_obj = new MySQL_Backup();
	
	//----------------------- EDIT - REQUIRED SETUP VARIABLES -----------------------
			
			 require_once("../Connections/tecnocomm.php");
			
			$backup_obj->server = $hostname_tecnocomm;
			$backup_obj->port = 3306;
			$backup_obj->username = $username_tecnocomm ;
			$backup_obj->password = $password_tecnocomm;
			$backup_obj->database = $database_tecnocomm ;
			
			//Tables you wish to backup. All tables in the database will be backed up if this array is null.
			$backup_obj->tables = array();

	//------------------------ END - REQUIRED SETUP VARIABLES -----------------------
	
	//-------------------- OPTIONAL PREFERENCE VARIABLES ---------------------
			
			//Add DROP TABLE IF EXISTS queries before CREATE TABLE in backup file.
			$backup_obj->drop_tables = true;
			
			//Only structure of the tables will be backed up if true.
			$backup_obj->struct_only = false;
			
			//Include comments in backup file if true.
			$backup_obj->comments = true;
			
			//Directory on the server where the backup file will be placed. Used only if task parameter equals MSB_SAVE.
			$backup_obj->backup_dir = '/';
			
			//Default file name format.
			$backup_obj->fname_format = 'm_d_Y';

	//--------------------- END - OPTIONAL PREFERENCE VARIABLES ---------------------
			
	//---------------------- EDIT - REQUIRED EXECUTE VARIABLES ----------------------		
				
			/*
				Task: 
					MSB_STRING - Return SQL commands as a single output string.
					MSB_SAVE - Create the backup file on the server.
					MSB_DOWNLOAD - Download backup file to the user's computer.
					
			*/
			$task = MSB_DOWNLOAD;
			
			//Optional name of backup file if using 'MSB_SAVE' or 'MSB_DOWNLOAD'. If nothing is passed, the default file name format will be used.
			$filename = 'bakcup_'.date("d_m_Y").'.tecnocomm';
			
			//Use GZip compression if using 'MSB_SAVE' or 'MSB_DOWNLOAD'?
			$use_gzip = false;

	//--------------------- END - REQUIRED EXECUTE VARIABLES ----------------------
	
	//-------------------- NO NEED TO ANYTHING BELOW THIS LINE -------------------- 
	
	if (!$backup_obj->Execute($task, $filename, $use_gzip))
	{
		 $output = $backup_obj->error;
	}
	else
	{
		$output = 'Operation Completed Successfully At: <b>' . date('g:i:s A') . '</b><i> ( Local Server Time )</i>';
	}
?>