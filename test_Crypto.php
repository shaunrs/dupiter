<?php

include('classes/CryptoService.class.php');
include('test_ChecksumFile.php');

echo 'Starting an MD5 Checksum object..';

try {
	$crypto = new CryptoService();
} catch (Exception $e) {
	exceptionExit($e);
}

echo " [pass]\n\n";


echo 'Finding the checksum of the file object..';

try {
	$file->setChecksum($crypto->getFileChecksum($file));
} catch (Exception $e) {
	exceptionExit($e);
}

echo " [pass]\n\n";

echo 'Checksum is: ' . $file->getChecksum() . "\n\n";
