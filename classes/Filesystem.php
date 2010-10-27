<?php

/** 
* Short Description
*
* Long Description
* package Filesystem
* author Matt Mueller
*/

class Filesystem 
{

	static function absoluteToRelative($from, $to) {
		$from = realpath($from);
		$to = realpath($to);

		if($from == '' || $to == '')
			return false;

		$from_is_file = file_exists($from);
		$to_is_dir = is_dir($to);

		$f_arr = explode('/', $from);
		$t_arr = explode('/', $to);

		$f_count = count($f_arr);
		$t_count = count($t_arr);
		$count = ($f_count > $t_count) ? $f_count : $t_count;

		// Remove all common directories from each array.
		for ($i=0; $i < $count; $i++) { 		
			if(!isset($f_arr[$i]) || !isset($t_arr[$i])) break;

			if($f_arr[$i] == $t_arr[$i]) {
				unset($f_arr[$i]);
				unset($t_arr[$i]);
			}

		}

		// To - we're moving forward so just implode
		$end = trim(implode('/', $t_arr),'/ ');

		// If its a file remove one backtrack
		if($from_is_file) {
			array_pop($f_arr);
		}
		
		
		// From - we need to backtrack so replace with '..'
		$backtrack_arr = array();
		foreach ($f_arr as $i => $item) {
			if(trim($item))
				$backtrack_arr[] = '..';
		}

		$beginning = implode('/', $backtrack_arr);

		$path = $beginning.'/'.$end;
		$path = trim($path, '/');
		
		return $path;
	}

	/**
	 * connect filesystem.
	 *
	 * return bool Returns true on success or false on failure (always true for WP_Filesystem_Direct).
	 */
	static function connect() {
		return true;
	}
	/**
	 * Reads entire file into a string
	 *
	 * param $file string Name of the file to read.
	 * return string|bool The function returns the read data or false on failure.
	 */
	static function get_contents($file) {
		return file_get_contents($file);
	}
	/**
	 * Reads entire file into an array
	 *
	 * param $file string Path to the file.
	 * return array|bool the file contents in an array or false on failure.
	 */
	static function get_contents_array($file) {
		return file($file);
	}
	/**
	 * Write a string to a file
	 *
	 * param $file string Remote path to the file where to write the data.
	 * param $contents string The data to write.
	 * param $mode int (optional) The file permissions as octal number, usually 0644.
	 * return bool False upon failure.
	 */
	static function put_contents($file, $contents, $mode = false ) {
		if ( ! ($fp = fopen($file, 'w')) )
			return false;
		fwrite($fp, $contents);
		fclose($fp);
		self::chmod($file, $mode);
		return true;
	}
	/**
	 * Gets the current working directory
	 *
	 * return string|bool the current working directory on success, or false on failure.
	 */
	static function cwd() {
		return getcwd();
	}
	/**
	 * Change directory
	 *
	 * param $dir string The new current directory.
	 * return bool Returns true on success or false on failure.
	 */
	static function chdir($dir) {
		return chdir($dir);
	}
	/**
	 * Changes file group
	 *
	 * param $file string Path to the file.
	 * param $group mixed A group name or number.
	 * param $recursive bool (optional) If set True changes file group recursivly. Defaults to False.
	 * return bool Returns true on success or false on failure.
	 */
	static function chgrp($file, $group, $recursive = false) {
		if ( ! self::exists($file) )
			return false;
		if ( ! $recursive )
			return chgrp($file, $group);
		if ( ! self::is_dir($file) )
			return chgrp($file, $group);
		//Is a directory, and we want recursive
		$file = rtrim($file,'/').'/';
		$filelist = self::dirlist($file);
		foreach ($filelist as $filename)
			self::chgrp($file . $filename, $group, $recursive);

		return true;
	}
	/**
	 * Changes filesystem permissions
	 *
	 * param $file string Path to the file.
	 * param $mode int (optional) The permissions as octal number, usually 0644 for files, 0755 for dirs.
	 * param $recursive bool (optional) If set True changes file group recursivly. Defaults to False.
	 * return bool Returns true on success or false on failure.
	 */
	static function chmod($file, $mode = false, $recursive = false) {
		if ( ! $mode ) {
			if ( self::is_file($file) )
				$mode = 0644;
			elseif ( self::is_dir($file) )
				$mode = 0755;
			else
				return false;
		}

		if ( ! $recursive || ! self::is_dir($file) )
			return chmod($file, $mode);
		//Is a directory, and we want recursive
		$file = rtrim($file,'/').'/';
		$filelist = self::dirlist($file);
		foreach ( (array)$filelist as $filename => $filemeta)
			self::chmod($file . $filename, $mode, $recursive);

		return true;
	}
	/**
	 * Changes file owner
	 *
	 * param $file string Path to the file.
	 * param $owner mixed A user name or number.
	 * param $recursive bool (optional) If set True changes file owner recursivly. Defaults to False.
	 * return bool Returns true on success or false on failure.
	 */
	static function chown($file, $owner, $recursive = false) {
		if ( ! self::exists($file) )
			return false;
		if ( ! $recursive )
			return chown($file, $owner);
		if ( ! self::is_dir($file) )
			return chown($file, $owner);
		//Is a directory, and we want recursive
		$filelist = self::dirlist($file);
		foreach ($filelist as $filename) {
			self::chown($file . '/' . $filename, $owner, $recursive);
		}
		return true;
	}
	/**
	 * Gets file owner
	 *
	 * param $file string Path to the file.
	 * return string Username of the user.
	 */
	static function owner($file) {
		$owneruid = fileowner($file);
		if ( ! $owneruid )
			return false;
		if ( ! function_exists('posix_getpwuid') )
			return $owneruid;
		$ownerarray = posix_getpwuid($owneruid);
		return $ownerarray['name'];
	}
	/**
	 * Gets file permissions
	 *
	 * FIXME does not handle errors in fileperms()
	 *
	 * param $file string Path to the file.
	 * return string Mode of the file (last 4 digits).
	 */
	static function getchmod($file) {
		return substr(decoct(fileperms($file)),3);
	}
	static function group($file) {
		$gid = filegroup($file);
		if ( ! $gid )
			return false;
		if ( ! function_exists('posix_getgrgid') )
			return $gid;
		$grouparray = posix_getgrgid($gid);
		return $grouparray['name'];
	}

	static function copy($source, $destination, $overwrite = false) {
		if ( ! $overwrite && self::exists($destination) )
			return false;

		return copy($source, $destination);
	}

	static function move($source, $destination, $overwrite = false) {
		if ( ! $overwrite && self::exists($destination) )
			return false;

		// try using rename first.  if that fails (for example, source is read only) try copy
		if ( rename($source, $destination) )
			return true;

		if ( self::copy($source, $destination, $overwrite) && self::exists($destination) ) {
			self::delete($source);
			return true;
		} else {
			return false;
		}
	}

	static function delete($file, $recursive = false) {
		if ( empty($file) ) //Some filesystems report this as /, which can cause non-expected recursive deletion of all files in the filesystem.
			return false;
		$file = str_replace('\\', '/', $file); //for win32, occasional problems deleteing files otherwise

		if ( self::is_file($file) )
			return unlink($file);
		if ( ! $recursive && self::is_dir($file) )
			return rmdir($file);

		//At this point its a folder, and we're in recursive mode
		$file = rtrim($file,'/').'/';
		$filelist = self::dirlist($file, true);

		$retval = true;
		if ( is_array($filelist) ) //false if no files, So check first.
			foreach ($filelist as $filename => $fileinfo)
				if ( ! self::delete($file . $filename, $recursive) )
					$retval = false;

		if ( file_exists($file) && ! rmdir($file) )
			$retval = false;
		return $retval;
	}

	static function exists($file) {
		return file_exists($file);
	}

	static function is_file($file) {
		return is_file($file);
	}

	static function is_dir($path) {
		return is_dir($path);
	}

	static function is_readable($file) {
		return is_readable($file);
	}

	static function is_writable($file) {
		return is_writable($file);
	}

	static function atime($file) {
		return fileatime($file);
	}

	static function mtime($file) {
		return filemtime($file);
	}
	static function size($file) {
		return filesize($file);
	}

	static function touch($file, $time = 0, $atime = 0) {
		if ($time == 0)
			$time = time();
		if ($atime == 0)
			$atime = time();
		return touch($file, $time, $atime);
	}

	static function mkdir($path, $chmod = false, $chown = false, $chgrp = false) {
		// safe mode fails with a trailing slash under certain PHP versions.
		$path = rtrim($path,'/');

		if ( empty($path) )
			$path = '/';

		if ( ! $chmod )
			$chmod = 0755;

		if ( ! mkdir($path) )
			return false;
		self::chmod($path, $chmod);
		if ( $chown )
			self::chown($path, $chown);
		if ( $chgrp )
			self::chgrp($path, $chgrp);
		return true;
	}

	static function rmdir($path, $recursive = false) {
		return self::delete($path, $recursive);
	}

	static function dirlist($path, $include_hidden = true, $recursive = false) {
		if ( self::is_file($path) ) {
			$limit_file = basename($path);
			$path = dirname($path);
		} else {
			$limit_file = false;
		}

		if ( ! self::is_dir($path) )
			return false;

		$dir = dir($path);
		if ( ! $dir )
			return false;

		$ret = array();

		while (false !== ($entry = $dir->read()) ) {
			$struc = array();
			$struc['name'] = $entry;

			if ( '.' == $struc['name'] || '..' == $struc['name'] )
				continue;

			if ( ! $include_hidden && '.' == $struc['name'][0] )
				continue;

			if ( $limit_file && $struc['name'] != $limit_file)
				continue;

			$struc['perms'] 	= self::gethchmod($path.'/'.$entry);
			$struc['permsn']	= self::getnumchmodfromh($struc['perms']);
			$struc['number'] 	= false;
			$struc['owner']    	= self::owner($path.'/'.$entry);
			$struc['group']    	= self::group($path.'/'.$entry);
			$struc['size']    	= self::size($path.'/'.$entry);
			$struc['lastmodunix']= self::mtime($path.'/'.$entry);
			$struc['lastmod']   = date('M j',$struc['lastmodunix']);
			$struc['time']    	= date('h:i:s',$struc['lastmodunix']);
			$struc['type']		= self::is_dir($path.'/'.$entry) ? 'd' : 'f';

			if ( 'd' == $struc['type'] ) {
				if ( $recursive )
					$struc['files'] = self::dirlist($path . '/' . $struc['name'], $include_hidden, $recursive);
				else
					$struc['files'] = array();
			}

			$ret[ $struc['name'] ] = $struc;
		}
		$dir->close();
		unset($dir);
		return $ret;
	}
	
	private static function gethchmod($file){
		$perms = self::getchmod($file);
		if (($perms & 0xC000) == 0xC000) // Socket
			$info = 's';
		elseif (($perms & 0xA000) == 0xA000) // Symbolic Link
			$info = 'l';
		elseif (($perms & 0x8000) == 0x8000) // Regular
			$info = '-';
		elseif (($perms & 0x6000) == 0x6000) // Block special
			$info = 'b';
		elseif (($perms & 0x4000) == 0x4000) // Directory
			$info = 'd';
		elseif (($perms & 0x2000) == 0x2000) // Character special
			$info = 'c';
		elseif (($perms & 0x1000) == 0x1000) // FIFO pipe
			$info = 'p';
		else // Unknown
			$info = 'u';

		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
					(($perms & 0x0800) ? 's' : 'x' ) :
					(($perms & 0x0800) ? 'S' : '-'));

		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
					(($perms & 0x0400) ? 's' : 'x' ) :
					(($perms & 0x0400) ? 'S' : '-'));

		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
					(($perms & 0x0200) ? 't' : 'x' ) :
					(($perms & 0x0200) ? 'T' : '-'));
		return $info;
	}
	
	/**
	 * Converts *nix style file permissions to a octal number.
	 *
	 * Converts '-rw-r--r--' to 0644
	 * From "info at rvgate dot nl"'s comment on the PHP documentation for chmod()
 	 *
	 * @link http://docs.php.net/manual/en/function.chmod.php#49614
	 * @since 2.5
	 * @access public
	 *
	 * @param string $mode string *nix style file permission
	 * @return int octal representation
	 */
	private static function getnumchmodfromh($mode) {
		$realmode = '';
		$legal =  array('', 'w', 'r', 'x', '-');
		$attarray = preg_split('//', $mode);

		for ($i=0; $i < count($attarray); $i++)
		   if ($key = array_search($attarray[$i], $legal))
			   $realmode .= $legal[$key];

		$mode = str_pad($realmode, 9, '-');
		$trans = array('-'=>'0', 'r'=>'4', 'w'=>'2', 'x'=>'1');
		$mode = strtr($mode,$trans);

		$newmode = '';
		$newmode .= $mode[0] + $mode[1] + $mode[2];
		$newmode .= $mode[3] + $mode[4] + $mode[5];
		$newmode .= $mode[6] + $mode[7] + $mode[8];
		return $newmode;
	}
}


?>