<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/JPIExportConfig.php';
use \PHPExcel\PHPExcel;

class JPIExportService
{	
	protected $title;
	protected $format;
	protected $exportAttributes;
	protected $data;
	protected $phpExcelObject;
	
	public function loadConfiguration(JPIExportConfig $configuration) {
		$this->title = $configuration->getTitle();
		$this->format = $configuration->getFormat();
		$this->exportAttributes = $configuration->getExportAttributes();
		$this->data = $configuration->getData();
		$this->phpExcelObject = $configuration->getPhpExcelObject();
	}

	public function export(JPIExportConfig $configuration)
  	{
  		// Chargement des informations
  		$this->loadConfiguration($configuration);
  		
  		// Pas de données préchargées, récupération des data
  		if(is_null($this->phpExcelObject) ) {
	  		// Création du phpExcel
	  		$this->phpExcelObject = new PHPExcel();
	  		
	  		// Le titre de l'onglet
	  		$this->phpExcelObject->getActiveSheet()->setTitle($this->title);
	
	  		// Les données
	  		$i = 2;
	  		foreach($this->data as $litem) {
	  			$lMembres = $litem->getAttributes($this->exportAttributes["attribute"]);
	  			$col = 'A';
	  			foreach($lMembres as $attribut)
	  			{
	  				$this->phpExcelObject->setActiveSheetIndex(0)->setCellValue($col.$i, $attribut);
	  				$col++;
	  			}
	  			$i++;
	  		}
	
	  		// Le header
	  		$i = 'A';
	  		foreach($this->exportAttributes["header"] as $nom) {
	  			$this->phpExcelObject->setActiveSheetIndex(0)->setCellValue($i.'1', $nom)->getColumnDimension($i)->setAutoSize(true);
	  			$i++;
	  		}

	  	}

	  	// L'onglet actif est le premier
	  	$this->phpExcelObject->setActiveSheetIndex(0);

    	return $this->exportHeader();
	}
		
	public function exportHeader() {		
		// header selon le format
		switch($this->format) {
			

			case "ods":
				header('Content-Type', 'application/vnd.oasis.opendocument.spreadsheet');
				header('Content-Disposition', 'attachment;filename="'.$this->title.'.ods"');
				// If you're serving to IE 9, then the following may be needed
				header('Cache-Control', 'max-age=1');
				// If you're serving to IE over SSL, then the following may be needed
				header ('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				header ('Last-Modified', gmdate('D, d M Y H:i:s').' GMT'); // always modified
				header ('Cache-Control', 'cache, must-revalidate'); // HTTP/1.1
				header ('Pragma', 'public'); // HTTP/1.0
				break;
				 
			case "csv":
				// Les headers
				header('Content-Encoding', 'UTF-8');
				header('Content-type', 'text/csv; charset=UTF-8');
				header('Content-Disposition', 'attachment; filename="'.$this->title.'.csv"');
				header('Cache-Control', 'max-age=0');
				break;
				 
			case "xlsx":
				// Les headers
				header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition', 'attachment;filename="'.$this->title.'.xlsx"');
				header('Cache-Control', 'max-age=1');
				 
				header('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				header('Last-Modified', gmdate('D, d M Y H:i:s').' GMT'); // always modified
				header('Cache-Control', 'cache, must-revalidate'); // HTTP/1.1
				header('Pragma', 'public');
				break;
			
			case "xls":
			default:
				// Redirect output to a client’s web browser (Excel5)
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$this->title.'.xls"');
				header('Cache-Control: max-age=0');
				// If you're serving to IE 9, then the following may be needed
				header('Cache-Control: max-age=1');
			
				// If you're serving to IE over SSL, then the following may be needed
				header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
				header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				header ('Pragma: public'); // HTTP/1.0
				break;
		}
		
		// writer selon le format
		switch($this->format) {
			case "xls":
				$writer = PHPExcel_IOFactory::createWriter($this->phpExcelObject, 'Excel5');
				break;
					
			case "ods":
				$writer = PHPExcel_IOFactory::createWriter($this->phpExcelObject, 'OpenDocument');
				break;
					
			case "csv":
				$writer = PHPExcel_IOFactory::createWriter($this->phpExcelObject, 'CSV')->setDelimiter(',')
				->setEnclosure('"')
				->setLineEnding("\r\n")
				->setSheetIndex(0);
				break;
					
			case "xlsx":
			default:
				$writer = PHPExcel_IOFactory::createWriter($this->phpExcelObject, 'Excel2007');
				break;
		}
		
		// creation de la reponse
		$writer->save('php://output');
		exit;
	}
}
?>
