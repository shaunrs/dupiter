<?php

/**
 * This class should choose a checksum algorithm (SHA, MD5 etc.) and take either a string or File object
 */

class CryptoService {
	private $cryptoFileFunction = false;
	private $cryptoTextFunction = false;

	public function __construct($crypto = 'md5') {
		$this->setCryptoFunction($crypto);
	}

	public function getChecksum(checksumFile $file) {
		$this->getFileChecksum($file);
	}

	// TODO: We can merge to getChecksum and check the type
	public function getFileChecksum(checksumFile $file) {
		$functionName = $this->getCryptoFunction('file');
		return $functionName($file->getFilename());
	}

	public function getTextChecksum($text) {
		$functionName = $this->getCryptoFunction('text');
		return ${functionName}($text);
	}

	private function getCryptoFunction($type = 'file') {
		if ( $type == 'file' ) {
			return $this->cryptoFileFunction;
		} elseif ( $type == 'text' ) {
			return $this->cryptoTextFunction;
		} else {
			return false;
		}
	}

	public function setCryptoFunction($crypto = 'md5') {
		switch ($crypto) {
			case 'md5':
				$this->cryptoFileFunction = 'md5_file';
				$this->cryptoTextFunction = 'md5';
				break;
			case 'sha':
				$this->cryptoFileFunction = 'sha1_file';
				$this->cryptoTextFunction = 'sha1';
				break;
		}
	}

}

?>
