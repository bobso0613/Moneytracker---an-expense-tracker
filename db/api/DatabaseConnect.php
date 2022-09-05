<?php
error_reporting(0);

//ini_set('error_reporting', E_ALL|E_STRICT);
//ini_set('display_errors', 1);
	//echo 'ea ea';
	include_once("DatabaseParams.php");
	include_once("mysql-php7-shim.php");
//echo '222';

	//echo json_encode($garr_DatabaseMapping);

	$con = '';
	class DatabaseAccess {
		public function __construct(){
			/*
			$this->con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD);
			if (!$this->con)
			{
	  			die('Could not connect: ' . mysql_error());
  			}
  			*/

  			// default connect
  			//$this->connectDB("iisaac_abic_system_db");
		}

		public function connectDB($databaseName=''){

			global $garr_DatabaseMapping;
			/*
			try {
				mysql_close($this->con);
			}
			catch {}*/
			$this->con = @mysql_connect($garr_DatabaseMapping[$databaseName]["host"],$garr_DatabaseMapping[$databaseName]["username"],$garr_DatabaseMapping[$databaseName]["password"]);
			@mysql_select_db($garr_DatabaseMapping[$databaseName]["actual_tablename"], $this->con);

			// mysql_query("SET sql_mode = 'NO_ENGINE_SUBSTITUTION'",$this->con);
			//echo $garr_DatabaseMapping[$databaseName]["actual_tablename"];
		}

		public function closeCon(){
			@mysql_close($this->con);
		}

	}

	function changeTimeZone($dateString, $timeZoneSource = null, $timeZoneTarget = null)
	{
	  // if (empty($timeZoneSource)) {
	  //   $timeZoneSource = date_default_timezone_get();
	  // }
	  // if (empty($timeZoneTarget)) {
	  //   $timeZoneTarget = date_default_timezone_get();
	  // }
		// $timeZoneTarget = date_default_timezone_get();
	  $timeZoneTarget = "Asia/Manila";
	  $timeZoneSource = "Asia/Manila";

	  $dt = new DateTime($dateString, new DateTimeZone($timeZoneSource));
	  $dt->setTimezone(new DateTimeZone($timeZoneTarget));

	  return $dt->format("Y-m-d H:i:s");
	}

/*
$garr_DatabaseMapping = array("iisaac_abic_financials_db"=>array("host"=>"localhost","username"=>"root","password"=>"","actual_tablename"=>"iisaac_abic_financials_db"),
	"iisaac_abic_insurance_db"=>array("host"=>"localhost","username"=>"root","password"=>"","actual_tablename"=>"iisaac_abic_insurance_db"),
	"iisaac_abic_system_db"=>array("host"=>"localhost","username"=>"root","password"=>"","actual_tablename"=>"iisaac_abic_system_db"),
	"iisaac_abic_images_db"=>array("host"=>"localhost","username"=>"root","password"=>"","actual_tablename"=>"iisaac_abic_images_db"));*/

	
?>
