<?php
namespace App\Traits;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
trait CommenRecycleTraits
{
	public $data;
	public function init($data, $type='', $fileName, $saveFilePath)
	{
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		switch ($type)
		{
			case 'first':
				$result = $this->firstExcelFile($reader, $data, $fileName, $saveFilePath);
				break;
			case 'secound':
				$result = $this->categoryExcelFile($reader, $data, $fileName, $saveFilePath);
				break;
		}
		$data = [
			'result' => $result,
		];
		return $this->data = $data;
	}

	private function categoryExcelFile($reader, $datas, $fileName, $saveFilePath)
	{
		$spreadsheet = $reader->load($fileName);
		$nextRow = 2;
		$thisRow = 1;
		foreach ($datas as $key => $record)
		{
			$spreadsheet->getActiveSheet()->insertNewRowBefore($nextRow);
			$spreadsheet->getActiveSheet()->SetCellValue('B' . $nextRow, $thisRow);
			$spreadsheet->getActiveSheet()->SetCellValue('C' . $nextRow, $record['category']);
			$spreadsheet->getActiveSheet()->SetCellValue('D' . $nextRow, $record['total_lbs_gross']);
			$spreadsheet->getActiveSheet()->SetCellValue('E' . $nextRow, $record['total_lbs_tare']);
			$spreadsheet->getActiveSheet()->SetCellValue('F' . $nextRow, $record['total_price']);
			$spreadsheet->getActiveSheet()->getStyle('B' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$spreadsheet->getActiveSheet()->getStyle('C' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$spreadsheet->getActiveSheet()->getStyle('D' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$spreadsheet->getActiveSheet()->getStyle('E' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$spreadsheet->getActiveSheet()->getStyle('F' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$nextRow++;
			$thisRow++;
		}
		$writer = new Xlsx($spreadsheet);
		try
		{
			$writer->save($saveFilePath);
			$message = true;
		}
		catch (\Exception $e)
		{
			$message = false;
		}
		return $message;
    }

    private function firstExcelFile($reader, $datas, $fileName, $saveFilePath)
    {
    	$recycleDate = Carbon::now()->format('l F jS,Y');
        $spreadsheet = $reader->load($fileName);
		$spreadsheet->getActiveSheet()->SetCellValue('F3', $recycleDate);
		$nextRow = 14;
		$thisRow = 1;
		foreach ($datas as $key => $data)
		{
			$spreadsheet->getActiveSheet()->insertNewRowBefore($nextRow);
			$spreadsheet->getActiveSheet()->SetCellValue('B' . $nextRow, $thisRow);
			$spreadsheet->getActiveSheet()->SetCellValue('C' . $nextRow, $data['category']);
			$spreadsheet->getActiveSheet()->SetCellValue('D' . $nextRow, $data['lgross']);
			$spreadsheet->getActiveSheet()->SetCellValue('E' . $nextRow, $data['ltare']);
			$spreadsheet->getActiveSheet()->SetCellValue('F' . $nextRow, $data['price']);
			$spreadsheet->getActiveSheet()->SetCellValue('G' . $nextRow, $data['total_price']);
			$spreadsheet->getActiveSheet()->SetCellValue('H' . $nextRow, $data['pgi']);
			$spreadsheet->getActiveSheet()->getStyle('B' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$spreadsheet->getActiveSheet()->getStyle('C' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$spreadsheet->getActiveSheet()->getStyle('D' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$spreadsheet->getActiveSheet()->getStyle('E' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$spreadsheet->getActiveSheet()->getStyle('F' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$spreadsheet->getActiveSheet()->getStyle('G' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$spreadsheet->getActiveSheet()->getStyle('H' . $nextRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ffffff');
			$nextRow++;
			$thisRow++;
		}
		$writer = new Xlsx($spreadsheet);
		try
		{
			$writer->save($saveFilePath);
			$message = true;
		}
		catch (\Exception $e)
		{
			$message = false;
		}
		return $message;
    }

    public function createPDF($html, $fileName, $saveFilePath)
	{
		$pdf = new \Mpdf\Mpdf();
        $pdf->WriteHTML($html);
        //save the file put which location you need folder/filname
        $pdf->Output($saveFilePath, 'F');
        return;
	}
}
