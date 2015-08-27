<?php

namespace App;

use Upload\Storage\FileSystem;
use Upload\File as UploadFile;
use Upload\Validation\Mimetype;
use Upload\Validation\Size;

use Exception;

class File 
{
	/**
	 * @var object
	 */ 
	protected $file;

	/**
	 * Create uploader instance
	 * 
	 * @param $fileInput string
	 * @param $path string
	 * 
	 * @return object
	 */ 
	public function __construct($fileInput, $path)
	{
		$storage = new FileSystem($path);
		$this->file = new UploadFile($fileInput, $storage);

		return $this;
	}

	/**
	 * Validate uploaded files
	 * 
	 * @param $size mixed
	 * @param $types array
	 * 
	 * @return object
	 */ 
	public function validate($size, $types)
	{
		$validator = [
			new Mimetype($types),
			new Size($size)
		];

		$this->file->addValidations($validator);

		return $this;
	}

	/**
	 * Get the file upload object
	 * 
	 * @return object
	 */ 
	public function file()
	{
		return $this->file;
	}

	/**
	 * Initialize uploader
	 * 
	 * @param $fileInput string
	 * @param $path string
	 * 
	 * @return object
	 */ 
	public static function make($fileInput, $path)
	{
		return new static($fileInput, $path);
	}
}