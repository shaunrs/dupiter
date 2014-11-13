<?php

class ChecksumDB {
	private $files = array();			// Array of File objects identified by fileId
	private $checksums = array();			// List of all checksums that have been processed, by fileId
	private $fileDuplicateEntries = array();	// Associative array of [uniqueFileID] => array of [duplicateFileIds]

	private $fileCount = 0;		// Count number of files processed - this is used as an array ID for files and checksums
	private $duplicateCount = 0;	// Count number of duplicates that are found - this is used as an ID for fileDuplicateEntries

	public function __construct() {
		// Initialisation - nothing to do

	}

	public function checkFile(checksumFile $file) {
		// Check for duplicate checksum as we go, faster

		$prevFileId = $this->checksumExists($file->getChecksum());

		$newFileId = $this->addFile($file);

		if ( $prevFileId !== false ) {	// If true, file found before
			// Get the previous file object
			$prevFile = $this->getFileById($prevFileId);

			// Compare the sizes
			if ( $file->getSize() == $prevFile->getSize() ) {
				// Files are duplicates
				$this->markDuplicateFile($prevFileId, $newFileId);
				return true;
			} else {
				return false;
			}
		}
	}

	private function addFile(checksumFile $file) {
		$arrayId = $this->getFileCount();

		$this->checksums[$arrayId] = $file->getChecksum();
		$this->files[$arrayId] = $file;
		$this->fileCount++;

		return $arrayId;
	}

	private function markDuplicateFile($firstFileId, $newFileId) {
		$this->fileDuplicateEntries[$firstFileId][] = $newFileId;
		$this->duplicateCount++;
	}

	public function getFileCount() {
		return $this->fileCount;
	}

	public function getDuplicateCount() {
		return $this->duplicateCount;
	}

	/**
	 * Returns an associative array for fileIds listing all ID's of duplicates of those files
	 */
	public function getFileDuplicateEntries() {
		return $this->fileDuplicateEntries;
	}

	public function getFileById($id) {
		if ( array_key_exists($id, $this->files) ) {
			return $this->files[$id];
		} else {
			throw new Exception("getFileById: $id ERROR - Not found");
		}
	}

	private function checksumExists($checksum) {
		return array_search($checksum, $this->checksums);
	}

	public function listFiles() {
		foreach ( $this->files as $id => $file ) {
			echo $file->getFilename() . ' : ' . $this->checksums[$id] . "\n";
		}
	}
}
