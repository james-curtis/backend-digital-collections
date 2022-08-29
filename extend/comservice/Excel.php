<?php
namespace comservice;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel
{
    /**
     * 使用PHPEXECL导入
     * @param string $file 文件地址
     * @param int $sheet 工作表sheet(传0则获取第一个sheet)
     * @param int $columnCnt 列数(传0则自动获取最大列)
     * @param array $options 操作选项
     *  array mergeCells 合并单元格数组
     *  array formula    公式数组
     *  array format     单元格格式数组
     * @return array
     * @throws
     * @throws \Exception
     */
    public static function  importExcel(string $file = '', int $sheet = 0, int $columnCnt = 0, &$options = [])
    {
        try {
            // 转码
            $file = iconv("utf-8", "gb2312", $file);
            if (empty($file) OR !file_exists($file)) {
                throw new \Exception('文件不存在!');
            }


            $objRead = IOFactory::createReader('Xlsx');

            if (!$objRead->canRead($file)) {
                /** @var Xls $objRead */
                $objRead = IOFactory::createReader('Xls');

                if (!$objRead->canRead($file)) {
                    throw new \Exception('只支持导入Excel文件！');
                }
            }

            /* 如果不需要获取特殊操作，则只读内容，可以大幅度提升读取Excel效率 */
            empty($options) && $objRead->setReadDataOnly(true);
            /* 建立excel对象 */
            $obj = $objRead->load($file);
            // 获取指定的sheet表
            $currSheet = $obj->getSheet($sheet);

            if (isset($options['mergeCells'])) {
                // 读取合并行列
                $options['mergeCells'] = $currSheet->getMergeCells();
            }

            if (0 == $columnCnt) {
                // 取得最大的列号
                $columnH = $currSheet->getHighestColumn();
                // 兼容原逻辑，循环时使用的是小于等于
                $columnCnt = Coordinate::columnIndexFromString($columnH);
            }


            // 获取总行数
            $rowCnt = $currSheet->getHighestRow();
            //return $rowCnt;
            $data   = [];

            // 读取内容
            for ($_row = 1; $_row <= $rowCnt; $_row++) {
                $isNull = true;

                for ($_column = 1; $_column <= $columnCnt; $_column++) {
                    $cellName = Coordinate::stringFromColumnIndex($_column);
                    $cellId   = $cellName . $_row;
                    $cell     = $currSheet->getCell($cellId);

                    if (isset($options['format'])) {
                        // 获取格式
                        $format = $cell->getStyle()->getNumberFormat()->getFormatCode();
                        // 记录格式
                        $options['format'][$_row][$cellName] = $format;
                    }

                    if (isset($options['formula'])) {
                        // 获取公式，公式均为=号开头数据
                        $formula = $currSheet->getCell($cellId)->getValue();

                        if (0 === strpos($formula, '=')) {
                            $options['formula'][$cellName . $_row] = $formula;
                        }
                    }

                    if (isset($format) && 'm/d/yyyy' == $format) {
                        // 日期格式翻转处理
                        $cell->getStyle()->getNumberFormat()->setFormatCode('yyyy/mm/dd');
                    }

                    $data[$_row][$cellName] = trim($currSheet->getCell($cellId)->getFormattedValue());

                    if (!empty($data[$_row][$cellName])) {
                        $isNull = false;
                    }
                }

                // 判断是否整行数据为空，是的话删除该行数据
                if ($isNull) {
                    unset($data[$_row]);
                }
            }
            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function upload()
    {
        set_time_limit(0);
        $type = 'Excel5';
        if($type=="Excel5"){
            header('Content-Type: application/vnd.ms-excel'); //告诉浏览器将要输出excel03文件
        }else{
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器数据excel07文件
        }
        header("Content-type:application/vnd.ms-excel");
        header('Content-Disposition: attachment;filename="'."ceshi".'.xlsx');  //告诉浏览器将输出文件的名称
        header('Cache-Control: max-age=0');  //禁止缓存

        //require_once './vendor/autoload.php';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

    }


    /**
     * 表格导出
     * @param array $data 为要导出的数据 $data必须为二维数组
     * @param string $title 为excel的标题
     * @param array $header 为excel首行每一列的名称 例$arr=array('编号','名称','售出时间','价格','返款人','返款编号','银行','状态');
     * @param array $index 为每一列对应的数据库字段 例$index=array('id','name','addtime','money','toname','tocardno','toaccounttypename','status');
     * @return bool
     */
    public static function export($data,$title,$header,$index){
        set_time_limit(0);

        try {
            // 创建Spreadsheet对象
            $objPHPExcel = new Spreadsheet();

            //获取需要导出的最大列数
            $maxColumnNum = count($header);
            $maxColumn = range('A', 'Z')[$maxColumnNum - 1];
            //dump($maxColumn);

            // 设置宽度
            foreach (range('A', $maxColumn) as $k => $v) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($v)->setWidth(25);
                //设置单元格格式为文本
                $objPHPExcel->getActiveSheet()->getStyle($v)->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                //合并第一行为标题
                if ($k == $maxColumnNum - 1) {
                    // 合并excel
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $v . '1');
                }
            };


            // 设置前两行行高
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

            // 设置字体样式 //默认字体大小
            foreach ($objPHPExcel->getActiveSheet()->getStyles() as $style) {
                $style->getFont()->setSize(10);
            }

            //设置A1行的字体大小
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16)->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A2:' . $maxColumn . '2')->getFont()->setBold(true); //粗体

            // 设置前两行的样式为垂直、水平居中
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A2:' . $maxColumn . '2')->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // 设置边框
            $objPHPExcel->getActiveSheet()->getStyle('A1:' . $maxColumn . '1')->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('A2:' . $maxColumn . '2')->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            // 前两行单元格内容
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', $title);

            //设置标题行
            foreach (range('A', $maxColumn) as $k1 => $v1) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($v1 . '2', $header[$k1]);
            };
            // 数据行设置
            for ($i = 0; $i < count($data); $i++) {
                // // 设置字段值
                 $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i + 3), $data[$i]['id']);
                 $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i + 3), $data[$i]['member']);
                 $objPHPExcel->getActiveSheet()->setCellValue('C' . ($i + 3), $data[$i]['member']);

                 if( @fopen($data[$i]['code'] , 'r' ) ) {
                     $objDrawing = new Drawing();
                     $objDrawing->setPath($data[$i]['code']);
                     // 设置图片的宽度
                     $objDrawing->setHeight(50);
                     $objDrawing->setWidth(50);
                     $objDrawing->setCoordinates('D' . ($i + 3));
                     $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                 }

                 $objPHPExcel->getActiveSheet()->setCellValue('E' . ($i + 3), $data[$i]['nick_name']);
                 $objPHPExcel->getActiveSheet()->setCellValue('F' . ($i + 3), $data[$i]['address']);
                 $objPHPExcel->getActiveSheet()->setCellValue('G' . ($i + 3), $data[$i]['detail_address']);
                 $objPHPExcel->getActiveSheet()->setCellValue('H' . ($i + 3), $data[$i]['create_time']);
                 $objPHPExcel->getActiveSheet()->setCellValue('I' . ($i + 3), $data[$i]['integral']);
                //循环设置字段值
//                foreach (range('A', $maxColumn) as $k2 => $v2) {
//                    $objPHPExcel->setActiveSheetIndex(0)
//                        ->setCellValue($v2 . ($i + 3), ' ' . $data[$i][$index[$k2]]);
//                };
                // 设置第三行及以后行的样式为垂直、水平居中
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':' . $maxColumn . ($i + 3))->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 设置第三行及以后行的行高
                $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(45);

                // 设置A-$maxColumn本行的边框
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':' . $maxColumn . ($i + 3))->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }

            //sheet命名
            $objPHPExcel->getActiveSheet()->setTitle($title);

            //默认打开的sheet
            $objPHPExcel->setActiveSheetIndex(0);
          //  var_dump($objPHPExcel);
            //excel头参数
            header("Content-Type:application/vnd.ms-execl;charset=utf-8");
            header('Content-Disposition:attachment;filename=' . $title . '(' . date('Y-m-d') . ').xls');//日期文件名后缀
            header('Cache-Control:max-age=0');

            //两种输出方式
            //$objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx'); //excel2007为xlsx
            $objWriter = new Xlsx($objPHPExcel);
            $objWriter->save('php://output');
            exit();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public static function exportRubblist($data,$title,$header,$index){

        try {
            // 创建Spreadsheet对象
            $objPHPExcel = new Spreadsheet();

            //获取需要导出的最大列数
            $maxColumnNum = count($header);
            $maxColumn = range('A', 'Z')[$maxColumnNum - 1];
            //dump($maxColumn);

            // 设置宽度
            foreach (range('A', $maxColumn) as $k => $v) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($v)->setWidth(25);
                //设置单元格格式为文本
                $objPHPExcel->getActiveSheet()->getStyle($v)->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_TEXT);

                //合并第一行为标题
                if ($k == $maxColumnNum - 1) {
                    // 合并excel
                    $objPHPExcel->getActiveSheet()->mergeCells('A1:' . $v . '1');
                }
            };


            // 设置前两行行高
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
            $objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);

            // 设置字体样式 //默认字体大小
            foreach ($objPHPExcel->getActiveSheet()->getStyles() as $style) {
                $style->getFont()->setSize(10);
            }

            //设置A1行的字体大小
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16)->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A2:' . $maxColumn . '2')->getFont()->setBold(true); //粗体

            // 设置前两行的样式为垂直、水平居中
            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A2:' . $maxColumn . '2')->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // 设置边框
            $objPHPExcel->getActiveSheet()->getStyle('A1:' . $maxColumn . '1')->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('A2:' . $maxColumn . '2')->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            // 前两行单元格内容
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', $title);

            //设置标题行
            foreach (range('A', $maxColumn) as $k1 => $v1) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($v1 . '2', $header[$k1]);
            };
            // 数据行设置
            for ($i = 0; $i < count($data); $i++) {
                // // 设置字段值
//                $objPHPExcel->getActiveSheet()->setCellValue('A' . ($i + 3), $data[$i]['id']);
//                $objPHPExcel->getActiveSheet()->setCellValue('B' . ($i + 3), $data[$i]['member']);
//                $objPHPExcel->getActiveSheet()->setCellValue('C' . ($i + 3), $data[$i]['member']);
//
//                if( @fopen($data[$i]['code'] , 'r' ) ) {
//                    $objDrawing = new Drawing();
//                    $objDrawing->setPath($data[$i]['code']);
//                    // 设置图片的宽度
//                    $objDrawing->setHeight(50);
//                    $objDrawing->setWidth(50);
//                    $objDrawing->setCoordinates('D' . ($i + 3));
//                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
//                }
//
//                $objPHPExcel->getActiveSheet()->setCellValue('E' . ($i + 3), $data[$i]['nick_name']);
//                $objPHPExcel->getActiveSheet()->setCellValue('F' . ($i + 3), $data[$i]['address']);
//                $objPHPExcel->getActiveSheet()->setCellValue('G' . ($i + 3), $data[$i]['detail_address']);
//                $objPHPExcel->getActiveSheet()->setCellValue('H' . ($i + 3), $data[$i]['create_time']);
                //循环设置字段值
                foreach (range('A', $maxColumn) as $k2 => $v2) {
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($v2 . ($i + 3), ' ' . $data[$i][$index[$k2]]);
                };
                // 设置第三行及以后行的样式为垂直、水平居中
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':' . $maxColumn . ($i + 3))->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 设置第三行及以后行的行高
                $objPHPExcel->getActiveSheet()->getRowDimension($i + 3)->setRowHeight(45);

                // 设置A-$maxColumn本行的边框
                $objPHPExcel->getActiveSheet()->getStyle('A' . ($i + 3) . ':' . $maxColumn . ($i + 3))->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
            }

            //sheet命名
            $objPHPExcel->getActiveSheet()->setTitle($title);

            //默认打开的sheet
            $objPHPExcel->setActiveSheetIndex(0);

            //excel头参数
            header("Content-Type:application/vnd.ms-execl;charset=utf-8");
            header('Content-Disposition:attachment;filename=' . $title . '(' . date('Y-m-d H:i:s') . ').xls');//日期文件名后缀
            header('Cache-Control:max-age=0');

            //两种输出方式
            //$objWriter = IOFactory::createWriter($objPHPExcel, 'Xlsx'); //excel2007为xlsx
            $objWriter = new Xlsx($objPHPExcel);
            $objWriter->save('php://output');
            exit();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}