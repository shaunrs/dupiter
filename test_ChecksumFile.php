<?php

include('classes/File.class.php');
include('classes/ChecksumFile.class.php');

echo 'Creating non-existant file object..';

try {
	$file = new checksumFile('/tmp/randomness');
	echo " [FAIL]";
} catch (Exception $e) {
	echo " [pass] Exception: " . $e->getMessage() . "\n\n";
}

echo 'Creating file without a filename..';

try {
        $file = new checksumFile('');
	echo " [FAIL]";
} catch (Exception $e) {
        echo " [pass] Exception: " . $e->getMessage() . "\n\n";
}

echo 'Creating file object..';

try {
	$file = new checksumFile('test/test1.txt');
} catch (Exception $e) {
	exceptionExit($e);
}

echo " [pass]\n\n";

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


echo 'Get empty checksum..';

try {
	$file->getChecksum();
	echo " [FAIL]";
} catch (Exception $e) {
	echo " [pass] Exception: " . $e->getMessage() . "\n\n";
}

echo 'Set and get checksum..';
try {
	$file->setChecksum('s5d49287a');
	if ( $file->getChecksum() == 's5d49287a' ) {
		echo " [pass]";
	} else {
		echo " [FAIL]";
	}
	echo " Checksum: " . $file->getChecksum();
} catch (Exception $e) {
	echo " [FAIL] Exception: " . $e->getMessage() . "\n\n";
}

echo "\n\n";

function exceptionExit($e) {
	echo "\n\nAn Exception has occurred:\n    " . $e->getMessage() . "\n\n";
	exit;
}
