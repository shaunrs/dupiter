<?php


include('test_Crypto.php');
include('classes/ChecksumDB.class.php');

echo 'Create my checksum database..';

try {
	$ChecksumDB = new checksumDB;
} catch (Exception $e) {
	exceptionExit($e);
}

echo " [pass]\n\n";

echo 'Add the current file to the ChecksumDB..';

try {
	if ( $ChecksumDB->checkFile($file) ) {
		echo ' [FAIL] Duplicate detected!';
	} else {
		echo ' [pass]';
	}
} catch (Exception $e) {
	exceptionExit($e);
}

echo "\n\n";

echo 'Generate new file, and checksum to add..';

try { 
	$file2 = new checksumFile('test/test2.txt');
	$file2->setChecksum($crypto->getFileChecksum($file2));

} catch (Exception $e) {
	exceptionExit($e);
}

echo " [pass]\n\n";

echo 'Add second file to ChecksumDB..';

try {
        if ( $ChecksumDB->checkFile($file2) ) {
                echo ' [FAIL] Duplicate detected!';
        } else {
                echo ' [pass]';
        }
} catch (Exception $e) {
        exceptionExit($e);
}
echo "\n\n";
echo 'Add a third, duplicate file..';
try {
        $file3 = new checksumFile('test/test3.txt');
        $file3->setChecksum($crypto->getFileChecksum($file3));
	if ( $ChecksumDB->checkFile($file3) ) {
                echo ' [pass] Duplicate detected!';
        } else {
                echo ' [FAIL] No duplicate detected!';
        }
} catch (Exception $e) {
	exceptionExit($e);
}

echo "\n\n";

echo "Finally, list all files processed and MD5 Sums:\n";
echo $ChecksumDB->listFiles();
