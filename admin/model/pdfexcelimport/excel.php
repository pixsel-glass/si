<?php
//class ModelPdfexcelimportExcel extends Model {

include_once ($_SERVER['DOCUMENT_ROOT'].'/system/library/PHPExcel/PHPExcel.php');

class ModelPdfexcelimportExcel extends PHPExcel {

	public function __construct(){
           parent::__construct($this);
	}

	public function __destruct(){}
        
    public function setMeta($creator='Pixsel',$lastmodifiedby='Pixsel',$title='excel price by Pixsel',$subject='excel price by Pixsel',$description='excel price by Pixsel',$keywords='excel price by Pixsel',$category='excel file by Pixsel'){
		$this->getProperties()->setCreator($creator)
		->setLastModifiedBy($lastmodifiedby)
		->setTitle($title)
		->setSubject($subject)
		->setDescription($description)
		->setKeywords($keywords)
		->setCategory($category);
	}

	public function setTitleSheet($title){
		$this->getActiveSheet()->setTitle($title);
	}

	public function setValue($column=0,$row=1,$value=''){
		$this->getActiveSheet()->setCellValueByColumnAndRow($column, $row, $value);
	}

	public function mergeCells($column_1=0,$row_1=1,$column_2=1,$row_2=1){
		$this->getActiveSheet()->mergeCellsByColumnAndRow($column_1, $row_1, $column_2, $row_2);
	}

	public function autoWidth(){
		$highestColumn = $this->getActiveSheet()->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		for ($col = 0; $col <= $highestColumnIndex; ++$col) {
			$this->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);
		}
	}

	public function setColumnWidth($column, $width){
        $this->getActiveSheet()->getColumnDimensionByColumn($column)->setAutoSize(false);
        $this->getActiveSheet()->getColumnDimensionByColumn($column)->setWidth($width);
	}

	public function textWrapInRow($row){
		$highestColumn = $this->getActiveSheet()->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		for ($col = 0; $col < $highestColumnIndex; ++$col) {
			$this->getActiveSheet()->getStyleByColumnAndRow($col,$row)->getAlignment()->setWrapText(true);
		}
	}

    public function styleInCell($column,$row,$style){
		$this->getActiveSheet()->getStyleByColumnAndRow($column,$row)->applyFromArray($style);
	}

	public function styleInRow($row,$style){
		$highestColumn = $this->getActiveSheet()->getHighestColumn();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		for ($col = 0; $col < $highestColumnIndex; ++$col) {
			$this->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style);
		}
	}

	public function styleInRows($row_1,$row_2,$style){
		for ($row = $row_1; $row <= $row_2; ++$row) {
		    $highestColumn = $this->getActiveSheet()->getHighestColumn($row);
		    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		    for ($col = 0; $col < $highestColumnIndex; ++$col) {
			    $this->getActiveSheet()->getStyleByColumnAndRow($col,$row)->applyFromArray($style);
		    }
		}
	}

	public function styleInColumn($column, $row_all, $style){
		//$highestRow = $this->getActiveSheet()->getHighestRow();
		for ($row = 0; $row < $row_all; ++$row) {
			$this->getActiveSheet()->getStyleByColumnAndRow($column,$row)->applyFromArray($style);
		}
	}

    public function insertImg($name, $src, $offsetx, $offsety, $width, $height, $coordinates){
        $objDrawing = new PHPExcel_Worksheet_Drawing(); 
        $objDrawing->setName($name); 
        $objDrawing->setDescription($name);
        $objDrawing->setPath($src);
        $objDrawing->setOffsetX($offsetx);                       
        $objDrawing->setOffsetY($offsety);
        $objDrawing->setCoordinates($coordinates);  
        $objDrawing->setWidth($width);  
        $objDrawing->setHeight($height); 
        $objDrawing->setWorksheet($this->getActiveSheet()); 
    }

    public function insertLink($column=0,$row=1,$url=''){
	    $this->getActiveSheet()->getCellByColumnAndRow($column,$row)->getHyperlink()->setUrl($url);
    }

    public function freeze($column=0,$row=1){
    	$this->getActiveSheet()->freezePaneByColumnAndRow($column, $row);
    }

	public function outToFile($obj,$name_file='excel.xls'){
		//$objWriter = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');
		$objWriter = new PHPExcel_Writer_Excel5($obj);
		$objWriter->save($name_file);
	}

	public function outToBrowser($obj,$name_file='excel.xls'){
        $objWriter = new PHPExcel_Writer_Excel5($obj);
	    header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="'.$name_file.'"');
	    header('Cache-Control: max-age=0');
	    $objWriter->save('php://output');             
	}

	public function readFile($file){
		return $onj_file = PHPExcel_IOFactory::load($file);
	}

	public function setLink($column=0,$row=1,$link='',$value=''){
		$this->getActiveSheet()->getCellByColumnAndRow($column, $row)->setValue($value);
		$this->getActiveSheet()->getCellByColumnAndRow($column, $row)->getHyperlink($value)->setUrl($link);
	}

	public function fileToArray($obj_file){
		$aSheet = $obj_file->getActiveSheet();
		$array = array();
		foreach($aSheet->getRowIterator() as $row){
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false);
			$item = array();
			foreach($cellIterator as $cell){
				array_push($item, $cell->getCalculatedValue());
			}
			array_push($array, $item);
		}

		return $array;
	}
        
    public function fileToArrayFormatted($obj_file){
		$aSheet = $obj_file->getActiveSheet();
		$array = array();
		foreach($aSheet->getRowIterator() as $row){
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false);
			$item = array();
			foreach($cellIterator as $cell){
				array_push($item, $cell->getFormattedValue());
			}
			array_push($array, $item);
		}

		return $array;
	}

	public function fileToArrayLinks($obj_file){
		$aSheet = $obj_file->getActiveSheet();
		$array = array();
		foreach($aSheet->getRowIterator() as $row){
			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false);
			$item = array();
			foreach($cellIterator as $cell){
				array_push($item, $cell->getHyperlink()->getUrl());
			}
			array_push($array, $item);
		}

		return $array;
	}

	public function changeForExcel($string_in, $length = 0,$change = 1){
      if($change==1){
		$string_out = str_replace("/",",", $string_in);
		$string_out = str_replace("?","",$string_out);
		$string_out = str_replace(":","",$string_out);
		$string_out = str_replace("="," ",$string_out);
      }else{
        $string_out = $string_in;
      }
	  if($length==30){
		if(strlen($string_out)>30){
		    $string_out = substr($string_out,0,25).'..';
		}else{
		    $string_out = $string_out;
		}
	  }
	  return $string_out;
	}
}