<?php

class File {
	private $filename = false;	// Full path to the file
	private $fileSize = false;
	private $writeable = false;

	private $type = false;		// File or Directory

	public function __construct($filename) {
		if ( !file_exists($filename) ) {
			throw new Exception('File does not exist: ' . $filename);
		}
		$this->setFilename($filename);

		// Is the file writeable?
		if ( is_writeable($filename) ) {
			$this->setWriteable(true);
		} else {
			$this->setWriteable(false);
		}

		// is this a file, or directory
		if ( is_dir($filename) ) {
			$this->setType('d');
		} else {
			$this->setType('f');
		}

		// what is the size of the file?
		$this->setSize(filesize($filename));
	}

	function move($newFileName) {
		// This simply links to rename function
		$this->rename($newFileName);
	}

	function rename($newFileName) {
		// Check that we can write to the target directory
		if ( is_writeable(dirname($newFileName)) ) {
			rename($this->getFilename(), $newFileName);
		} else {
			throw new Exception("No permission, or destination does not exist");
		}
	}

	function delete() {
		unlink($this->getFilename());
	}

	/**
	 * Get and Set
	 */
	public function getFilename() {
		return $this->filename;
	}

	private function setFilename($filename) {
		$this->filename = $filename;
		return true;
	}

	public function getSize() {
		return $this->fileSize;
	}

	private function setSize($size) {
		$this->fileSize = $size;
		return true;
	}

	public function getType() {
		return $this->type;
	}

	private function setType($type) {
		// TODO: this could be a switch?
		if ( $type == 'd' || $type == 'dir' ) {
			$type = 'Directory';
		} elseif ( $type == 'f' ) {
			$type = 'File';
		}

		if ( $type != 'Directory' || $type != 'File' ) {
			// TODO: throw an exception

		}

		$this->type = $type;
		return true;
	}

	public function getWriteable() {
		return $this->writeable;
	}

	private function setWriteable($writeable) {
		$this->writeable = $writeable;
	}
}

?>
