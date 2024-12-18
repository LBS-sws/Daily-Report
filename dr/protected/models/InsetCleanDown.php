<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/3/14 0014
 * Time: 11:57
 */
class InsetCleanDown extends DownSummary {


    public function setCleanData($data){
        if(!empty($data)){
            $endStr = $this->getColumn($this->th_num-1);
            foreach ($data as $keyStr=>$list){
                $row = $this->current_row;
                $lastRow = $this->current_row-1;
                $col = 0;
                foreach ($list as $item){
                    $keyStr = $this->current_row==6?"":$this->getColumn($col);//第一行不需要写公式
                    switch ($keyStr){
                        case "I"://月停单率(虫控);
                            $computeStr="=(B{$row}+C{$row}-B{$lastRow}-C{$lastRow}+(E{$row}/12))/D{$lastRow}";
                            $this->objPHPExcel->getActiveSheet()->setCellValue($keyStr.$this->current_row, $computeStr);
                            // 获取单元格的样式
                            $style = $this->objPHPExcel->getActiveSheet()->getStyle($keyStr.$this->current_row);
                            $style->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                            break;
                        case "J"://综合停单率(虫控);
                            $computeStr="=((E{$row}+F{$row}+G{$row}+H{$row})/12)/(D{$lastRow}-B{$lastRow}-C{$lastRow})";
                            $this->objPHPExcel->getActiveSheet()->setCellValue($keyStr.$this->current_row, $computeStr);
                            // 获取单元格的样式
                            $style = $this->objPHPExcel->getActiveSheet()->getStyle($keyStr.$this->current_row);
                            $style->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                            break;
                        case "R"://月停单率(清洁);
                            $computeStr="=(K{$row}+L{$row}-K{$lastRow}-L{$lastRow}+(N{$row}/12))/M{$lastRow}";
                            $this->objPHPExcel->getActiveSheet()->setCellValue($keyStr.$this->current_row, $computeStr);
                            // 获取单元格的样式
                            $style = $this->objPHPExcel->getActiveSheet()->getStyle($keyStr.$this->current_row);
                            $style->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                            break;
                        case "S"://综合停单率(清洁);
                            $computeStr="=((N{$row}+O{$row}+P{$row}+Q{$row})/12)/(M{$lastRow}-K{$lastRow}-L{$lastRow})";
                            $this->objPHPExcel->getActiveSheet()->setCellValue($keyStr.$this->current_row, $computeStr);
                            // 获取单元格的样式
                            $style = $this->objPHPExcel->getActiveSheet()->getStyle($keyStr.$this->current_row);
                            $style->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                            break;
                        default:
                            $this->objPHPExcel->getActiveSheet()
                                ->setCellValueByColumnAndRow($col, $this->current_row, $item);

                    }
                    $col++;
                }
                $this->current_row++;
            }
        }
    }
}