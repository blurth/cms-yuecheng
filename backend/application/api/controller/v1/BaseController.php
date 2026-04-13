<?php

namespace app\api\controller\v1;
use app\api\service\token\LoginToken;
use PhpOffice\PhpSpreadsheet\IOFactory;
use think\Db;

class BaseController
{

    protected $psychologist_id;
    public function __construct()
    {
        $currentUser = LoginToken::getInstance()->getTokenExtend();

        if ($currentUser['psychologist_id'] == 0){
            $this->psychologist_id = Db::name('psychologist')->column('id');
        }else{
            $this->psychologist_id = explode(',',$currentUser['psychologist_id']);
        }

    }
    public function importExcelData($cellStr)
    {
        $file = $_FILES["file"]["tmp_name"];

        if (empty($file) || !file_exists($file)) {
            header("Content-type: text/html; charset=utf-8");
            die('请先选择 Excel 文件!');
        }

        $spreadsheet = IOFactory::load($file);
        $allData = []; // 用于存储所有工作表的数据

        $sheetCount = $spreadsheet->getSheetCount();
        for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
            $worksheet = $spreadsheet->getSheet($sheetIndex);
            $sheetData = $this->extractDataFromSheet($worksheet);
            $allData = array_merge($allData, $sheetData); // 合并数据
        }

        return $allData;
    }

    private function extractDataFromSheet($worksheet): array
    {
        $cellStr = 'A,B,C,D,E,F,G,H'; // 根据需要调整列名
        $cellName = explode(',', $cellStr);

        $columnCnt = count($cellName);
        $rowCnt = $worksheet->getHighestRow();

        $data = [];
        for ($row = 1; $row <= $rowCnt; $row++) {
            $rowData = [];
            for ($col = 0; $col < $columnCnt; $col++) {
                $cellId = $cellName[$col] . $row;
                $cell = $worksheet->getCell($cellId);
// 使用 getFormattedValue() 获取单元格的显示值
                $cellValue = $cell->getFormattedValue();

                $rowData[$cellName[$col]] = $cellValue;
            }
            $data[] = $rowData;
        }

        return $data;
    }
}