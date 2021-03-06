<?php
class ExcelChartRow {

  // private $rawData ; 
  private $type ;
  private $colStart ;
  private $colEnd ;
  private $rowNum ;
  
  private $cells ;

  private static $largestColNumber = 0 ;
  private static $largestRowNumber = 0 ;
  
  function __construct( $row, $sharedStringsHandler ) {

    $rowId  = trim($row->attributes()->r) ;	
    ExcelChartRow::$largestRowNumber = $this->rowNum = $rowId ;

    $this->cells = array() ;
    
    foreach( $row->c as $key => $cell ) {
      $col = ExcelFunctions::getCellCol( $rowId, $cell ) ;
      $col = ExcelFunctions::columnIndexFromString( $col ) ;
      if ( $col > ExcelChartRow::$largestColNumber ) {
        ExcelChartRow::$largestColNumber = $col ;
      }
      $this->cells[ $col ] = $sharedStringsHandler->getCellValue($cell) ;
    }
  }
  
  public function getLargestRowNumber() {
    return ExcelChartRow::$largestRowNumber ;
  }
  
  public function getLargestColNumber() {
    return ExcelChartRow::$largestColNumber ;
  }
  
  public function getRowNum() {
    return $this->rowNum ;
  }
  
  public function getType() {
    return $this->type ;
  }
  
  public function getCells() {
    return $this->cells ;
  }
  
  public function appendToXML( $sheet, $sharedDest, $sharedSource, $currentRowIndex  ) {
	
    $row = $sheet->addChild('row') ;

    $row->addAttribute('r', $currentRowIndex) ;
    $row->addAttribute('x14ac:dyDescent', '0.25') ;
    $row->addAttribute('spans', '1:22') ;
	
    foreach( $this->cells as $cellKey => $cell ) {
    
      if ( $cellKey > $this->colStart && $cellKey <= $this->colEnd ) { 
      // 
      } else if ( trim($this->colEnd) !== "" ) {
        continue ;
      }
      
      $newcell = $row->addChild('c') ;
      
      $newcell->addChild('v',$cell->v[0]) ;
      
      foreach ( $cell->attributes() as $key => $value ) {
        if ( $key == 's' ) {
        continue ;
      }
      
      if ( trim($key) == "t" && trim($value) == "s" ) {
        if ( ($stringIndex = $sharedDest->sharedStringIndex( $stringValue = $sharedSource->getCellValue( $cell ) ))  !== false ) {
        $newcell->v[0] = $stringIndex ;
        } else {
        $newcell->v[0] = $sharedDest->addSharedString( $stringValue ) ;
        }
      }
      $newcell->addAttribute( $key, $value ) ;
      }
      
      
      $colLetter = ExcelFunctions::stripCellCol( $cell ) ;
      $colLetter = ExcelFunctions::stringFromColumnIndex( ExcelFunctions::columnIndexFromString($colLetter) - 1 ) ;
      $newcell['r'] = $colLetter . $currentRowIndex ;
      
      
    }
  }
  
  
  
  
}

?>