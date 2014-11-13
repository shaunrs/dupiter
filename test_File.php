<?php

include('classes/File.class.php');

echo 'Creating file object..';

try {
	$file = new File('test/test1.txt');
} catch (Exception $e) {
	exceptionExit($e);
}

echo " [done]\n\n";

echo "File name is: " . $file->getFilename() ."\n";

try {
	echo 'File size is: ' . $file->getSize() . " bytes\n";
} catch (Exception $e) {
	exceptionExit($e);
}

try {
	echo 'File is writeable: ';
	if ( $file->getWriteable() ) {
		echo 'YES';
	} else {
		echo 'NO';
	}
} catch (Exception $e) {
	exceptionExit($e);
}

echo "\n\n";


function exceptionExit($e) {
	echo "\n\nAn Exception has occurred:\n    " . $e->getMessage() . "\n\n";
	exit;
}
