<?php

namespace Amsys;

class CSV {

	private $handle;
	private $head_name_map;
	private $head_index_map;
	private $current_line;

	public function __construct($filename) {
		$this->handle = fopen($filename, "r");
		if ($this->handle === FALSE) {
			throw new \Exception("Unable to open $filename.");
		}
		// Find indexes of headers
		$header_row = fgetcsv($this->handle);
		$index = 0;
		foreach ($header_row as $header) {
			$this->head_name_map[$header] = $index;
			$this->head_index_map[$index] = $header;
			$index ++;
		}
	}

	public function get($col_name, $optional = FALSE) {
		if (isset($this->head_name_map[$col_name])) {
			return trim($this->current_line[$col_name]);
		}
		if ($optional) {
			return FALSE;
		} else {
			throw new Exception("$col_name column not found." . print_r($this->head_name_map, TRUE));
		}
	}

	public function next() {
		$line = fgetcsv($this->handle);
		$this->current_line = array();
		$index = 0;
		foreach ($this->head_index_map as $index => $header_name) {
			$this->current_line[$header_name] = array_get($line, $index);
		}
		return $line !== FALSE;
	}

	/**
	 * Whether or not this CSV has a header named $header.
	 *
	 * @param string $header
	 * @return boolean
	 */
	public function has_header($header) {
		return isset($this->head_name_map[$header]);
	}

}
