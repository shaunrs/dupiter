#!/usr/bin/php
<?php
/**
 * Dupiter v0.0.4 - Duplicate File Finder
 * copyright (c)2012 Shaun Smith. All Rights Reserved.
 */


$cmdOptions = getopt('',array('list','move','debug','cleandirs'));

$version='0.0.4';

require('classes/File.class.php');
require('classes/ChecksumFile.class.php');
require('classes/CryptoService.class.php');
require('classes/ChecksumDB.class.php');

// Set the path to work on. TODO: Cmd line parameter
$path = "/my/dir/to/check/for/dupes/";
$duplicateStore = '/store/dupes/here/when/moving/';

#$excludeStrings = '\/www\/|\/Websites\/';
$excludeStrings = 'null';

$debug=false;
$listDuplicates=false;
$moveDuplicates=false;
$cleanDirectories=false;

if ( isset($cmdOptions['debug']) ) {
	$debug=true;
}

if ( isset($cmdOptions['list']) ) {
	$listDuplicates=true;
}

if ( isset($cmdOptions['move']) ) {
	$moveDuplicates=true;
}

if ( isset($cmdOptions['cleandirs']) ) {
	$cleanDirectories=true;
}

// First off, start the CryptoService and ChecksumDB
try {
	$CryptoService = new CryptoService('md5');
	$ChecksumDB = new ChecksumDB();
} catch (Exception $e) {
	// We need an exception handler!

}

echo "\n";
echo "Source Path:\t\t$path\n";
echo "Duplicates Path:\t$duplicateStore\n\n";

echo "Analysing duplicate files, please wait..\n";

if ( $debug ) {
	echo "DEBUG is on, a dot '.' will be printed for each file scanned!\n";
}


/**
 * Analyse the directory tree for duplicates
 */
$compareStartTime = microtime(true);

// This function recursively scans a directory
recursiveFileCheck($path);

$compareTimeTaken = round(microtime(true)-$compareStartTime,2);

if ( $debug ) {
	echo "\n";
}



/**
 * List and/or Move the duplicate files
 */
$moveFileStartTime = microtime(true);
foreach ( $ChecksumDB->getFileDuplicateEntries() as $prevFileId => $dupeFileList ) {
	$prevFileName = $ChecksumDB->getFileById($prevFileId)->getFilename();
	$prevFileChecksum = $ChecksumDB->getFileById($prevFileId)->getChecksum();

	if ( $listDuplicates ) {
		echo "\n\n$prevFileName ($prevFileChecksum) has duplicates of:\n";
	}

	foreach ( $dupeFileList as $dupeFileId ) {
		$dupeFile = $ChecksumDB->getFileById($dupeFileId);
		$dupeSrcFileName = $dupeFile->getFilename();
		$dupeFileChecksum = $dupeFile->getChecksum();

		// Remove the path from filename
		$dupeDestFileName = $duplicateStore.str_replace($path, '', $dupeSrcFileName);

		if ( $listDuplicates ) {
			echo "\t$dupeSrcFileName ($dupeFileChecksum)";
			echo "\n";
		}

		if ( $moveDuplicates ) {
			// Make sure the dest directory exists
			if ( !file_exists(dirname($dupeDestFileName)) ) {
				mkdir(dirname($dupeDestFileName),0755,true);
			}

			// Move the file
			try {
				$dupeFile->move($dupeDestFileName);
			} catch (Exception $e) {
				echo "Moving the file $dupeSrcFileName failed with error:\n";
				echo "\t" . $e->getMessage();
			}
		}
	}
}
$moveFileTimeTaken = round(microtime(true)-$moveFileStartTime,2);


/**
 * Clean Directories
 */
$cleanDirStartTime = microtime(true);
$clearedCount=0;
if ( $cleanDirectories ) {
	echo "Cleaning up our directory structure..\n";
	$clearedCount = recursiveDirClean($path);
}
$cleanDirTimeTaken = round(microtime(true)-$cleanDirStartTime,2);


echo "\nFiles scanned:\t\t" . $ChecksumDB->getFileCount() . "\t\ttook $compareTimeTaken seconds";
echo "\nDuplicates Found:\t" . $ChecksumDB->getDuplicateCount() . "\t\ttook $moveFileTimeTaken seconds";
echo "\nRemoved dirs:\t\t$clearedCount\t\ttook $cleanDirTimeTaken seconds";
echo "\n\n";




/**
 * Thats all folks
 */



function recursiveDirClean($path) {
	global $debug, $excludeStrings;

	$clearedCount = 0;

        // Ensure we always have a trailing slash
        if ( !preg_match('/\/$/',$path) ) {
                $path .= '/';
        }

        // Get the file listing of this path
        if ( !is_dir($path) ) {
                echo 'Not a directory, unable to process!';
                return false;
        }

        // We might as well ignore full directories in the exclude list too
        if ( preg_match("/($excludeStrings)/", $path) ) {
                echo "Skipping excluded directory: $path\n";
                return false;
        }

        //$contents = scandir($path);
        if ( ($contents = @scandir($path)) === false ) {
			echo 'Skipping unreadable directory: ' . $path . "\n";
			return false;
		}
		
        natcasesort($contents);

	// Recurse first, so we remove child folders initially
	foreach ( $contents as $filename ) {
		// Ignore files starting with .
                if ( preg_match('/^\./',$filename) ) {
                        continue;
                }

		// If this is a directory, recurse
                if ( is_dir($path.$filename) ) {
                        $clearedCount += recursiveDirClean($path.$filename);
		}
	}

        // If this is an empty directory, clear it?
	if ( count($contents) == 2 ) {
		if ( $debug ) {
                	echo "Removing directory: $path\n";
		}
                rmdir($path);
		$clearedCount++;
        }

	return $clearedCount;

}

function recursiveFileCheck($path) {
	global $CryptoService, $ChecksumDB, $debug, $excludeStrings;

	// Ensure we always have a trailing slash
	if ( !preg_match('/\/$/',$path) ) {
		$path .= '/';
	}

	// Get the file listing of this path
	if ( !is_dir($path) ) {
		echo 'Not a directory, unable to process!';
		return false;
	}
	
	// We might as well ignore full directories in the exclude list too
	if ( preg_match("/($excludeStrings)/", $path) ) {
		echo "Skipping excluded directory: $path\n";
		return false;
	}

	//$contents = scandir($path);
	if ( ($contents = @scandir($path)) === false ) {
		echo 'Skipping unreadable directory: ' . $path . "\n";
		return false;
	}

	natcasesort($contents);

	// foreach content, if directory - recurse
	foreach ( $contents as $filename ) {
		// Ignore files starting with .
		if ( preg_match('/^\./',$filename) ) {
			continue;
		}

		// Check if this is a directory (recurse) or file (checksum)
		if ( is_dir($path.$filename) ) {
			recursiveFileCheck($path.$filename);
			//echo $path.$filename . "\n";
		} elseif ( is_file($path.$filename) ) {
			// This is a file, Crypto it!
			try {
				$thisFile = new checksumFile($path . $filename);
				$thisFile->setChecksum($CryptoService->getFileChecksum($thisFile));
				$ChecksumDB->checkFile($thisFile); // This enters the file into the DB, so no need to check return value really
			} catch ( Exception $e ) {
				if ( $debug ) {
					echo 'X';
				} else {
					echo "Exception for file: $path.$filename";
				}
				continue;
			}
		} else {
			// This will skip items such as symlinks
			if ( $debug ) {
				echo 'S';
			} else {
				// We want to warn about this one
				echo "Skipping unknown file: $path$filename\n";
			}
			continue;
		}

		if ( $debug ) {
			echo '.';
		}
	}
}
