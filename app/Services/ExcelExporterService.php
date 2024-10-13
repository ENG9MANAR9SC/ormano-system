<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ExcelExporterService
{

  private static $_instance = null;

  private $headers = [];
  private $rows = [[]];

  /////////////////////////////////////////////////
  // !start of setters and getters
  /////////////////////////////////////////////////
  public static function getInstance()
  {
    if (self::$_instance === null) {
      self::$_instance = new self;
    }
    return self::$_instance;
  }

  public function setHeaders(array $headers)
  {
    $this->headers = $headers;
    return $this;
  }

  public function getHeaders(): array
  {
    return $this->headers;
  }

  public function setRows(array $rows)
  {
    $this->rows = $rows;
    return $this;
  }

  public function getRows(): array
  {
    return $this->rows;
  }

  /////////////////////////////////////////////////
  // !end of setters and getters
  /////////////////////////////////////////////////

  public function exportExcel()
  {

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = $this->getHeaders();
    $headerStartLetter = 'A';
    foreach ($headers as $key => $header) {
      $spreadsheet->getActiveSheet()->getColumnDimension($headerStartLetter)->setAutoSize(true);
      $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER);
      $sheet->setCellValue($headerStartLetter . '1', $header);
      $headerStartLetter++;
    }
    // $rowId = 15;
    $rows = $this->getRows();
    $rowId = 2;

    foreach ($rows as $row) {

      foreach ($row as $key => $value) {
        $columnLetter = chr(65 + $key); // Convert key to Excel column letter
        $sheet->setCellValue($columnLetter . $rowId, $value);
      }
      $rowId++;
    }

    $excelName = 'payments_' . today()->format('Y_m_d') . '_' . rand(10000, 99999) . '.xlsx';
    $tempPath = sys_get_temp_dir() . '/' . $excelName;

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($excelName) . '"');


    $writer->save($tempPath);

    // Read the file into a variable
    $data = file_get_contents($tempPath);

    // Delete the temporary file
    unlink($tempPath);

    // Return the file data as a base64 encoded string in the API response
    return [
      'file' => base64_encode($data),
      'name' => $excelName,
    ];
  }
}
