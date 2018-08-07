<?php
class scanDir {
    static private $directories, $files, $ext_filter, $recursive;

// ----------------------------------------------------------------------------------------------
    // scan(dirpath::string|array, extensions::string|array, recursive::true|false)
    static public function scan(){
        // Initialize defaults
        self::$recursive = false;
        self::$directories = array();
        self::$files = array();
        self::$ext_filter = false;

        // Check we have minimum parameters
        if(!$args = func_get_args()){
			$slides = array(
				"error"=>"Must provide a path string or array of path strings");
			die(json_encode($slides));	
        }
        if(gettype($args[0]) != "string" && gettype($args[0]) != "array"){
			$slides = array(
				"error"=>"Must provide a path string or array of path strings");
			die(json_encode($slides));	
        }

        // Check if recursive scan | default action: no sub-directories
        if(isset($args[2]) && $args[2] == true){self::$recursive = true;}

        // Was a filter on file extensions included? | default action: return all file types
        if(isset($args[1])){
            if(gettype($args[1]) == "array"){self::$ext_filter = array_map('strtolower', $args[1]);}
            else
            if(gettype($args[1]) == "string"){self::$ext_filter[] = strtolower($args[1]);}
        }

        // Grab path(s)
        self::verifyPaths($args[0]);
        return self::$files;
    }

    static private function verifyPaths($paths){
        $path_errors = array();
        if(gettype($paths) == "string"){$paths = array($paths);}

        foreach($paths as $path){
            if(is_dir($path)){
                self::$directories[] = $path;
                $dirContents = self::find_contents($path);
            } else {
                $path_errors[] = $path;
            }
        }

        if($path_errors){
			$slides = array(
				"dirs"=>$dirs,
				"error"=>"The following directories do not exist",
				"details"=>$path_errors);
			die(json_encode($slides));	
		}
    }

    // This is how we scan directories
    static private function find_contents($dir){
        $result = array();
        $root = scandir($dir);
        foreach($root as $value){
            if($value === '.' || $value === '..') {continue;}
            if(is_file($dir.DIRECTORY_SEPARATOR.$value)){
                if(!self::$ext_filter || in_array(strtolower(pathinfo($dir.DIRECTORY_SEPARATOR.$value, PATHINFO_EXTENSION)), self::$ext_filter)){
                    self::$files[] = $result[] = $dir.DIRECTORY_SEPARATOR.$value;
                }
                continue;
            }
            if(self::$recursive){
                foreach(self::find_contents($dir.DIRECTORY_SEPARATOR.$value) as $value) {
                    self::$files[] = $result[] = $value;
                }
            }
        }
        // Return required for recursive search
        return $result;
    }
}


$dirs = array("active");

date_default_timezone_set('EST5EDT');

$interval = 12500;
$nextcheck = 30;

if(file_exists("override.txt")) {
	$dirs = file("override.txt", FILE_IGNORE_NEW_LINES);
} else {
	if(file_exists("today.txt")) {
		$today = file("today.txt", FILE_IGNORE_NEW_LINES)[0];
	} else {
		$today = date('Y-m-d');
	}
	$hour = (time() / 3600) % 24;
	$min = (time() / 60) % 60;
	$milspec = $hour * 100 + $min;

	if($milspec < 800) {
		$dirs = array("switchmode");
	}

	if($today == "2018-08-09") {
		$interval = 12500;
		if($milspec < 800) {
		  // Normal Carousel
		  // ..
		  $nextcheck = mktime( 8, 1, 0, 8, 9, 2018) - time();
		} else if($milspec < 900) {
		  // Volunteers Lobby				 8:00 - 9:00
		  // ..
		  $dirs = array("gls_thursday_01_volunteers");
		  $nextcheck = mktime( 9, 1, 0, 8, 9, 2018) - time();
		} else if($milspec < 915) {
		  // Guests Lobby					 9:00 - 9:15
		  // ..
		  $dirs = array("gls_thursday_02_guests", "gls_between_sessions");
		  $nextcheck = mktime( 9, 16, 0, 8, 9, 2018) - time();
		} else if($milspec < 945) {
		  // Session 1 Pre					 9:15 - 9:45
		  // ..
		  $dirs = array("gls_thursday_03_session_1_pre", "gls_between_sessions");
		  $nextcheck = mktime( 9, 46, 0, 8, 9, 2018) - time();
		} else if($milspec < 1100) {
		  // Session 1						 9:45 - 11:00
		  // ..
		  $dirs = array("gls_thursday_04_session_1", "gls_during_sessions");
		  $nextcheck = mktime( 11, 1, 0, 8, 9, 2018) - time();
		} else if($milspec < 1130) {
		  // Session 1 Post					11:00 - 11:30
		  // ..
		  $dirs = array("gls_thursday_05_session_1_post", "gls_between_sessions");
		  $nextcheck = mktime( 11, 31, 0, 8, 9, 2018) - time();
		} else if($milspec < 1200) {
		  // Session 2 Pre					11:30 - 12:00
		  // ..
		  $dirs = array("gls_thursday_06_session_2_pre", "gls_between_sessions");
		  $nextcheck = mktime( 12, 1, 0, 8, 9, 2018) - time();
		} else if($milspec < 1300) {
		  // Session 2						12:00 - 1:00
		  // ..
		  $dirs = array("gls_thursday_07_session_2", "gls_during_sessions");
		  $nextcheck = mktime( 13, 1, 0, 8, 9, 2018) - time();
		} else if($milspec < 1330) {
		  // Session 2 Post					 1:30 - 2:15
		  // ..
		  $dirs = array("gls_thursday_08_session_2_post", "gls_between_sessions");
		  $nextcheck = mktime( 13, 31, 0, 8, 9, 2018) - time();
		} else if($milspec < 1415) {
		  // Lunch							 1:30 - 2:15
		  // ..
		  $dirs = array("gls_thursday_09_lunch", "gls_between_sessions");
		  $nextcheck = mktime( 14, 16, 0, 8, 9, 2018) - time();
		} else if($milspec < 1445) {
		  // Session 3 Pre					 2:15 - 2:45
		  // ..
		  $dirs = array("gls_thursday_10_session_3_pre", "gls_between_sessions");
		  $nextcheck = mktime( 14, 46, 0, 8, 9, 2018) - time();
		} else if($milspec < 1545) {
		  // Session 3						 2:45 - 3:45
		  // ..
		  $dirs = array("gls_thursday_11_session_3", "gls_during_sessions");
		  $nextcheck = mktime( 15, 46, 0, 8, 9, 2018) - time();
		} else if($milspec < 1630) {
		  // PM Break / Session 4 Pre		 4:15 - 4:30
		  // ..
		  $dirs = array("gls_thursday_12_session_3_post", "gls_thursday_13_session_4_pre");
		  $nextcheck = mktime( 16, 31, 0, 8, 9, 2018) - time();
		} else if($milspec < 1715) {
		  // Session 4						 3:45 - 5:15
		  // ..
		  $dirs = array("gls_thursday_14_session_4", "gls_during_sessions");
		  $nextcheck = mktime( 17, 16, 0, 8, 9, 2018) - time();
		} else if($milspec < 1830) {
		  // Dismissal / Session 4 Post		 5:15 - 6:30
		  // ..
		  $dirs = array("gls_thursday_15_session_4_post");
		}
	}
	if($today == "2018-08-10") {
		$interval = 12500;
		if($milspec < 800) {
		  // Normal Carousel
		  // ..
		  $nextcheck = mktime( 8, 1, 0, 8, 10, 2018) - time();
		} else if($milspec < 900) {
		  // Volunteers Lobby				 8:00 - 9:00
		  // ..
		  $dirs = array("gls_friday_01_volunteers");
		  $nextcheck = mktime( 9, 1, 0, 8, 10, 2018) - time();
		} else if($milspec < 915) {
		  // Guests Lobby					 9:00 - 9:15
		  // ..
		  $dirs = array("gls_friday_02_guests", "gls_between_sessions");
		  $nextcheck = mktime( 9, 16, 0, 8, 10, 2018) - time();
		} else if($milspec < 945) {
		  // Session 5 Pre					 9:15 - 9:45
		  // ..
		  $dirs = array("gls_friday_03_session_5_pre", "gls_between_sessions");
		  $nextcheck = mktime( 9, 46, 0, 8, 10, 2018) - time();
		} else if($milspec < 1115) {
		  // Session 5						 9:45 - 11:15
		  // ..
		  $dirs = array("gls_friday_04_session_5", "gls_during_sessions");
		  $nextcheck = mktime( 11, 16, 0, 8, 10, 2018) - time();
		} else if($milspec < 1145) {
		  // Session 5 Post					11:15 - 11:45
		  // ..
		  $dirs = array("gls_friday_05_session_5_post", "gls_between_sessions");
		  $nextcheck = mktime( 11, 46, 0, 8, 10, 2018) - time();
		} else if($milspec < 1200) {
		  // Session 6 Pre					11:45 - 12:00
		  // ..
		  $dirs = array("gls_friday_06_session_6_pre", "gls_between_sessions");
		  $nextcheck = mktime( 12, 1, 0, 8, 10, 2018) - time();
		} else if($milspec < 1300) {
		  // Session 6						12:00 - 1:00
		  // ..
		  $dirs = array("gls_friday_07_session_6", "gls_during_sessions");
		  $nextcheck = mktime( 13, 1, 0, 8, 10, 2018) - time();
		} else if($milspec < 1315) {
		  // Session 6 Post					 1:00 - 1:15
		  // ..
		  $dirs = array("gls_friday_08_session_6_post", "gls_between_sessions");
		  $nextcheck = mktime( 13, 16, 0, 8, 10, 2018) - time();
		} else if($milspec < 1415) {
		  // Lunch							 1:15 - 2:15
		  // ..
		  $dirs = array("gls_friday_09_lunch", "gls_between_sessions");
		  $nextcheck = mktime( 14, 16, 0, 8, 10, 2018) - time();
		} else if($milspec < 1445) {
		  // Session 7 Pre					 2:15 - 2:45
		  // ..
		  $dirs = array("gls_friday_10_session_7_pre", "gls_between_sessions");
		  $nextcheck = mktime( 14, 46, 0, 8, 10, 2018) - time();
		} else if($milspec < 1615) {
		  // Session 7						 2:45 - 4:15
		  // ..
		  $dirs = array("gls_friday_11_session_7", "gls_during_sessions");
		  $nextcheck = mktime( 16, 16, 0, 8, 10, 2018) - time();
		} else if($milspec < 1730) {
		  // Session 7 Post / Dismissal		 4:15 - 5:30
		  // ..
		  $dirs = array("gls_friday_12_session_7_post");
		}
	}
	if(($milspec < 600) || ($milspec >= 2300)) {
		$dirs = array("afterhours");
		$interval = 250;
	}
}

$file_ext = array(
    "jpg",
    "png"
);

$items = array();
// Multiple dirs, with specified extensions, include sub-dir files
$files = scanDir::scan($dirs, $file_ext, true);

foreach($files as $file) 
{ 
   array_push($items, $file);
} 

if($nextcheck < 10) {
	$nextcheck = 10;
} else if($nextcheck > 300) {
	$nextcheck = 300;
}
$nextcheck = $nextcheck * 1000;
$nextcheck = 12500;

$slides = array("dirs"=>$dirs, "items"=>$items, "refresh"=>$nextcheck, "interval"=>$interval);
echo json_encode($slides);	

?>