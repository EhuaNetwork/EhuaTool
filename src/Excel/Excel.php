<?php


namespace Ehua\Excel;

use PHPExcel_IOFactory;
use PHPExcel;


/**
 *
 *需要composer 安装
 *      "phpoffice/phpexcel": "^1.8",
 "      "phpoffice/phpspreadsheet": "^1.19"
 * @package Ehua\Excel
 */
class Excel
{
    /**
     * 导出excel
     * @param array $list 数据
     * @param array $flagarr 表头-字段标识
     * @param string $name 保存文件名
     * @param string $headcolor 表头背景色
     * @param string $head_fontcolor 表头文字颜色
     * @param string $type 导出类型 2003/2007
     */
    public function excelout($list, $flagarr, $filename, $headcolor = '0070C0', $head_fontcolor = 'FFFFFF', $type = '2007')
    {
        vendor("phpexcel.PHPExcel");
        vendor("phpexcel.PHPExcel.Writer.Excel2007");
        vendor("phpexcel.PHPExcel.Writer.Excel5");
        vendor("phpexcel.PHPExcel.IOFactory");

        $objExcel = new \PHPExcel();

        //设置属性
        $objExcel->getProperties()->setCreator("lws");
        $objExcel->getProperties()->setLastModifiedBy("lws");
        $objExcel->getProperties()->setTitle('Office ' . $type . ' XLS Document');
        $objExcel->getProperties()->setSubject('Office ' . $type . ' XLS Document');
        $objExcel->getProperties()->setDescription('for Office ' . $type . ' XLS');
        $objExcel->getProperties()->setKeywords('office ' . $type . ' php');
        $objExcel->getProperties()->setCategory("FOR ANTAG");
        $objExcel->setActiveSheetIndex(0);

        $str = 'abcdefghijklmnopqrstuvwxyz';
        $head_scope = $str[0] . '1:' . $str[count($flagarr) - 1] . '1'; //表头范围 如：a1:f1
        foreach ($flagarr as $k => $v) {
            //设置表头
            $objExcel->getActiveSheet()->setCellValue($str[$k] . '1', "$v[0]");

            //设置自适应宽度
            $objExcel->getActiveSheet()->getColumnDimension($str[$k])->setAutoSize(true);
        }

        //添加数据
        for ($i = 0; $i < count($list); $i++) {
            $j = $i + 2;
            foreach ($flagarr as $k => $v) {
                $objExcel->getActiveSheet()->setCellValue($str[$k] . $j, ' ' . $list[$i][$v[1]]); //前面加空格 防止数字文本被转换为科学计数法
            }
        }

        //设置表头背景色
        $objExcel->getActiveSheet()->getStyle($head_scope)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objExcel->getActiveSheet()->getStyle($head_scope)->getFill()->getStartColor()->setARGB($headcolor);

        //设置表头文字颜色
        $styleArray = array(
            'font' => array(
                'color' => array('rgb' => $head_fontcolor),
            ));
        $objExcel->getActiveSheet()->getStyle($head_scope)->applyFromArray($styleArray);

        //设置表头和表尾
        $objExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BPersonal cash register&RPrinted on &D');
        $objExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objExcel->getProperties()->getTitle() . '&RPage &P of &N');

        //设置页方向和规模
        $objExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $objExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $objExcel->setActiveSheetIndex(0);
        $timestamp = time();
        ob_end_clean();

        //导出excel
        if ($type == '2003') {
            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=' . $filename . '.xls');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        } else {
            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
    }
}