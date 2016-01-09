<?php

class JPIExportConfig
{
	protected $title;
	protected $format;
	protected $exportAttributes;
	protected $data;
	protected $phpExcelObject;
	
	public function __construct($title, $format, $exportAttributes, $data, $phpExcelObject = null) {
		$this->title = $title;
		$this->format = $format;
		$this->exportAttributes = $exportAttributes;
		$this->data = $data;
		$this->phpExcelObject = $phpExcelObject;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function getFormat() {
		return $this->format;
	}
	
	public function setFormat($Format) {
		$this->format = $format;
	}
	
	public function getPhpExcelObject() {
		return $this->phpExcelObject;
	}
	
	public function setPhpExcelObject($phpExcelObject) {
		$this->phpExcelObject = $phpExcelObject;
	}
	
	public function getExportAttributes() {
		return $this->exportAttributes;
	}
	
	public function setExportAttributes($exportAttributes) {
		$this->exportAttributes = $exportAttributes;
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function setData($data) {
		$this->data = $data;
	}
}
?>
