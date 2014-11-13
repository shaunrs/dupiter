<?php

class checksumFile extends File {
	private $checksum = false;

	public function setChecksum($checksum) {
		$this->checksum = $checksum;
	}

	public function getChecksum() {
		if ( $this->checksum ) {
			return $this->checksum;
		} else {
			throw new Exception("Checksum is not yet set for this file");
		}
	}
}

?>
