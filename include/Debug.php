<?php

class Debug {
	
	public static $file_handle;
	
	private static function openFileHandle(){
		if(!self::$file_handle){
			self::$file_handle = fopen('/srv/tmp/kronos_debug.log', 'a');
		}
		
		return self::$file_handle;
	}
	
	public static function log($string){
		$fh = self::openFileHandle();
		fwrite($fh, print_r($string, true)."\n");
	}
	
}
