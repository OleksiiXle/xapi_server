<?php

namespace common\models;

use yii\base\Exception;
use yii\helpers\FileHelper;
use PHPExcel_IOFactory;


class Functions
{
    public static $rightArray=[
        0 => 'a_main_code',
        1 => '',
        2 => '',
        3 => '',
        4 => '',
        5 => '',
        6 => '',
        7 => '',
    ];
    public static $exelHeader = [
        1 => 'A',
        2 => 'B',
        3 => 'C',
        4 => 'D',
        5 => 'E',
        6 => 'F',
        7 => 'G',
        8 => 'H',
        9 => 'I',
        10 => 'J',
        11=> 'K',
        12=> 'L',
        13=> 'M',
        14=> 'N',
        15=> 'O',
        16=> 'P',
        17=> 'R',
        18=> 'Q',
        19=> 'S',
        20=> 'T',
        21=> 'Y',
        22=> 'W',
        23=> 'U',
        24=> 'V',
        25=> 'Z',
        26=> 'X',
    ];

    public static function dbg()
    {
        $rec['METHOD'] = \Yii::$app->request->getMethod();
        $rec['HEADERS'] = \Yii::$app->request->headers;
      //  $rec['RAW_BODY'] = \Yii::$app->request->rawBody;
      //  $rec['BODY_PARAMS'] = \Yii::$app->request->bodyParams;
        $rec['QUERY_PARAMS'] = \Yii::$app->request->queryParams;
        $rec['COOCIES'] = \Yii::$app->request->cookies;
        if (\Yii::$app->request->isPost){
            $rec['POST'] = \Yii::$app->request->post();
        }
        \yii::trace('************************************************ REQUEST', "dbg");
        \yii::trace(\yii\helpers\VarDumper::dumpAsString($rec), "dbg");

    }


    //*************************** EXEL *****************************************************************

    /**
     * Вывод трех мерного ассоциативного массива в Ексел файл
     * - ключи первого подмассива будут в превом ряду екселя
     * @param $data - массив
     * @param $pathToFile
     * @param $department_id
     * @param string $title
     * @return mixed
     */
    public static function exportToExel($data, $pathToFile, $department_id=0, $fileMask = 'structure_', $title = 'Новый лист', $upload=true){
        if ($department_id>0){
            $fn = $fileMask . $department_id .'.xls';
        } else {
            $user = \Yii::$app->user->getId();
            $fn = 'report_' . $user .'.xls';
        }

        $fileName = $pathToFile . '/' . $fn;

        $myXls = new \PHPExcel(); // Создание объекта класса PHPExcel

        $myXls->setActiveSheetIndex(0); // Указание на активный лист

        $mySheet = $myXls->getActiveSheet(); // Получение активного листа

        $mySheet->setTitle($title); // Указание названия листа книги

        $headerArr = array_keys($data[0]);
        $colCnt = $rowCnt = 1;
        foreach ($headerArr as $key => $value){
            $mySheet->setCellValue(self::$exelHeader[$colCnt++] . $rowCnt, $value);
        }
        $rowCnt++;
        foreach ($data as $dataItem){
            $colCnt = 1;
            foreach ($dataItem as $key => $value){
                $mySheet->setCellValue(self::$exelHeader[$colCnt++] . $rowCnt, $value);
            }
            $rowCnt++;
        }


// Указываем значения для отдельных ячеек
        //  $mySheet->setCellValue("A1", "1-я строка");
        //    $mySheet->setCellValue("A2", "2-я строка");
        //   $mySheet->setCellValue("A3", "3-я строка");
        //    $mySheet->setCellValue("B1", "2-й столбец");

// HTTP-заголовки
        /*header ("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header ("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cache");
        header ("Content-type: application/vnd.ms-excel");
        header ("Content-Disposition: attachment; filename=".$fn);*/

// Вывод файла
        $objWriter = new \PHPExcel_Writer_Excel5($myXls);
      //  $pathToFile = '/var/www/xle/items/rbac/dumps/test_csv/export.xls';

        $objWriter->save($fileName);

        if ($upload){
            if (file_exists($fileName)){
                $options['mimeType'] = FileHelper::getMimeTypeByExtension($fileName);
                $attachmentName = basename($fileName);
                \Yii::$app->response->sendFile($fileName, $attachmentName, $options);
                $result['status'] = true;
                $result['data'] = 'Файл ' . $fileName . ' успішно експортовано';
                unlink($fileName);
            } else {
                $result['status'] = false;
                $result['data'] = 'Файл ' . $fileName . ' не вдалося експортувати';
            }
        } else {
            $result['status'] = true;
            $result['data'] =  basename($fileName);
        }

        return $result;

    }

    /**
     * Вывод трех мерного ассоциативного массива в Ексел файл частями
     * - ключи первого подмассива будут в превом ряду екселя
     * @param $data - массив
     * @param $pathToFile
     * @param $department_id
     * @param string $title
     * @return mixed
     */
    public static function exportToExelUniversal($data, $fileFullName, $title = 'Новый лист', $upload)
    {
        $result['status'] = false;
        $result['data'] =  'error';

        try{
            $firstPage = ! file_exists($fileFullName);
            if ($firstPage){
                $myXls = new \PHPExcel();
            } else {
                $check = is_writable($fileFullName);
                if (!$check){
                    $ret = chmod($fileFullName, 0777);
                    $check = is_writable($fileFullName);
                }
                $myXls = PHPExcel_IOFactory::load($fileFullName);
            }
            /*
            $myXls = ($firstPage)
                ? new \PHPExcel()
                : PHPExcel_IOFactory::load($firstPage);
            */


            $myXls->setActiveSheetIndex(0); // Указание на активный лист

            $mySheet = $myXls->getActiveSheet(); // Получение активного листа

            $mySheet->setTitle($title); // Указание названия листа книги

            $headerArr = array_keys($data[0]);

            if ($firstPage){
                $colCnt = $rowCnt = 1;
                foreach ($headerArr as $key => $value){
                    $mySheet->setCellValue(self::$exelHeader[$colCnt++] . $rowCnt, $value);
                }
            } else {
                $rowCnt = $myXls->getActiveSheet()->getHighestRow();
            }
            $rowCnt++;
            foreach ($data as $dataItem){
                $colCnt = 1;
                foreach ($dataItem as $key => $value){
                    $mySheet->setCellValue(self::$exelHeader[$colCnt++] . $rowCnt, $value);
                }
                $rowCnt++;
            }

// Вывод файла
            $objWriter = new \PHPExcel_Writer_Excel5($myXls);
            $objWriter->save($fileFullName);

            if ($upload){
                if (file_exists($fileFullName)){
                    $options['mimeType'] = FileHelper::getMimeTypeByExtension($fileFullName);
                    $attachmentName = basename($fileFullName);
                    \Yii::$app->response->sendFile($fileFullName, $attachmentName, $options);
                    $result['status'] = true;
                    $result['data'] =  basename($fileFullName);
                    unlink($fileFullName);
                } else {
                    $result['status'] = false;
                    $result['data'] = 'Файл ' . $fileFullName . ' не знайдено';
                }
            } else {
                $result['status'] = true;
                $result['data'] =  basename($fileFullName);
            }






        } catch (\Exception $e){
            $result['data'] = $e->getMessage();
        }
        return $result;

    }

    /**
     * Чтение данных Ексел файла в массив
     * @param $xlsFileName
     * @return array
     */
    public static function getExlArray($xlsFileName){
        $objPHPExcel = PHPExcel_IOFactory::load($xlsFileName);
        $objPHPExcel->setActiveSheetIndex(0);
        $aSheet = $objPHPExcel->getActiveSheet();

        //этот массив будет содержать массивы содержащие в себе значения ячеек каждой строки
        $array = array();
        //получим итератор строки и пройдемся по нему циклом
        foreach($aSheet->getRowIterator() as $row){
            //получим итератор ячеек текущей строки
            $cellIterator = $row->getCellIterator();
            //пройдемся циклом по ячейкам строки
            //этот массив будет содержать значения каждой отдельной строки
            $item = array();
            foreach($cellIterator as $cell){
                //заносим значения ячеек одной строки в отдельный массив
                //  array_push($item, iconv('utf-8', 'cp1251', $cell->getCalculatedValue()));
                array_push($item, $cell->getCalculatedValue());
            }
            //заносим массив со значениями ячеек отдельной строки в "общий массв строк"
            array_push($array, $item);
        }
        return $array;

    }



    public static function intToDate___($i){
        $res =  (isset($i) && is_numeric($i)) ? date('m/d/Y',  $i) : '';
        return $res;
    }

    public static function intToDate($i){
        $res =  (isset($i) && is_numeric($i) && ($i>0)) ? date('d.m.Y',  $i) : '';
        return $res;
    }

    public static function dateToInt($d){
        if (isset($d) && is_string($d)){
            if ($d == ''){
                return null;
            }
            $arr = date_parse($d);
            $res = mktime(0, 0, 0,  $arr['month'],$arr['day'], $arr['year']);
            return $res;
        } else
            return null;
    }

    public static function dateTimeToInt($d){
        if (isset($d) && is_string($d)){
            if ($d == ''){
                return null;
            }
            $arr = date_parse($d);
            $res = mktime($arr['hour'], $arr['minute'], $arr['second'],  $arr['month'],$arr['day'], $arr['year']);
            return $res;
        } else
            return null;
    }

    public static function intToDateTime($i){
        $res =  (isset($i) && is_numeric($i)) ? date('d.m.Y H:i',  $i) : '';
        return $res;
    }

    public static function uploadFileXle($fileName, $unlink = true)
    {
        $result =[
            'status' => false,
            'data'   => 'Помилка вивантаження файлу',
            ];
        try{
            $options['mimeType'] = FileHelper::getMimeTypeByExtension($fileName);
            $attachmentName = basename($fileName);
            \Yii::$app->response->sendFile($fileName, $attachmentName, $options);
            $result['status'] = true;
            $result['data'] = 'Файл ' . $fileName . ' успішно вивантажено';
            if ($unlink){
                unlink($fileName);
            }
        } catch (Exception $e){
            $result['data'] = $e->getMessage();

        }
        return $result;
    }





    //*************************** CVS *****************************************************************
    /**
     * Вывод трех мерного ассоциативного массива в CSV файл
     * - ключи первого подмассива будут в превом ряду
     * @param $data - массив
     * @param $pathToFile
     * @param $department_id
     * @param string $title
     * @return mixed
     */
    public static function exportToCSV($data, $pathToFile, $fileMask = 'report'){
        try{
            $user = \Yii::$app->user->getId();
            $fileName = $pathToFile . '/' . $fileMask . '_' . $user . '.csv';
            $fp = fopen($fileName, 'w');

            $headerArr = array_keys($data[0]);
            fputcsv($fp, $headerArr);
            foreach ($data as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);
            return 'o.k.';
        } catch (Exception $e){
            return $e->getMessage();
        }

    }


    /**
     * Чтение CSV файла в массив, возвращает массив
     * @param $fileName
     * @return array|string
     */
    public static function readCSV_ToArray($fileName){
        if (!file_exists($fileName) ) {
            return 'file not found ' . $fileName;
        }
        if (!is_readable($fileName)) {
            return 'file not is_readable ' . $fileName;
        }
        $data = [];
        if (($handle = fopen($fileName, 'r')) !== false) {
            $colName = fgetcsv($handle, 1000, ',');
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $buf = [];
                for ($i=0; $i < count($row); $i++){
                    $buf[$colName[$i]]=$row[$i];
                }
                if (!is_numeric($buf['i_positions_amount'])){
                    $buf['i_positions_amount'] = 0;
                }
                $data[]= $buf;
            }
            fclose($handle);
            return $data;
        } else {
            return 'file not fgetcsv ' . $fileName;
        }
    }

    /**
     * ++ Добавление временного подразделения, возвращает его ИД
     * @param $department_id
     * @param $staff_order_id
     * @return int
     */
    public static function getError_id($department_id, $staff_order_id){
        $root_id = $department_id;
        $department = new OrderProjectDepartment();
        $department->parent_id =$root_id;
        $department->name = 'Ошибка преобразования';
        $department->staff_order_id = $staff_order_id;
        $department->save();
        $error_id = $department->id;
        return $error_id;

    }

    public static function addCSV_str($csvStr, $root_id, $staff_order_id, $error_id ){
        $errArr = '';
        try{
            $prn = strval($csvStr['y_prn']);
            $cat_type = iconv_substr ($csvStr['a_main_code'], 0 , 1 , 'UTF-8' );
            switch ($cat_type){
                case 'н': //*********************************************   ПОДРАЗДЕЛЕНИЕ
                    //   echo 'department ' .  $csvStr['g_name'] . '<br>';
                    $department = new OrderProjectDepartment();
                    $department->name = $csvStr['g_name'];
                    $department->x_rn = $csvStr['x_rn'];
                    $department->staff_order_id = $staff_order_id;

                    if (trim($csvStr['z_level']) == '') {
                       // echo '   root' . '<br>';
                        $department->parent_id = $parent_id = $root_id;
                        $department->setAttributes($csvStr);
                    } else {
                        $parent = OrderProjectDepartment::find()
                            ->andWhere(['x_rn' => $prn, 'staff_order_id' => $staff_order_id])
                            ->asArray()->all();
                        if (count($parent) != 1) {
                            $errArr = $cat_type . '     Error - count=' . count($parent) .  ' name=' . $csvStr['g_name'] .
                                ' - not found RN=' . $prn . '<br>';
                            $department->parent_id = $parent_id = $error_id;
                        } else {
                            $department->parent_id = $parent_id = $parent[0]['id'];
                        }
                    }
                    //----------- словари
                    $start = stripos($csvStr['b_department_code'],'<g>')+3;
                    $end = stripos($csvStr['b_department_code'],'</g>');
                    $globalCode = substr($csvStr['b_department_code'], $start, ($end - $start));
                    if (($end - $start) > 0){
                        //   echo   $globalCode . '  ->  '  . $csvStr['b_department_code'] . PHP_EOL;
                        $department->global_code = $globalCode;
                    }
                    $start = stripos($csvStr['b_department_code'],'<b>')+3;
                    $end = stripos($csvStr['b_department_code'],'</b>');
                    $subordination = substr($csvStr['b_department_code'], $start, ($end - $start));
                    if (($end - $start) > 0){
                        //   echo   $subordination . '  ->  '  . $csvStr['b_department_code'] . PHP_EOL;
                        $department->subordination = $subordination;
                    }
                    $department->save();
                    if (!$department->save()) {
                        $errArr = 'Ошибка сохранения подразделения : ';
                        foreach ($department->getErrors() as $key => $value){
                            $errArr .=  $key . ': ' . $value[0] . ' *** ';
                        }
                    };
                    break;
                case 'п':  //********************************************** ПОСАДА
                    //  echo 'positiom ' .  $csvStr['g_name'] . '<br>';
                    $newPosition = new OrderProjectPosition();
                    $newPosition->name = $csvStr['g_name'];
                    $newPosition->staff_order_id = $staff_order_id;
                    $parent = OrderProjectDepartment::find()
                        ->andWhere(['x_rn' =>  $prn, 'staff_order_id' => $staff_order_id])
                        ->asArray()->all();
                    if (count($parent) !=1){
                        $errArr =
                            $cat_type . '     Error - count=' . count($parent) . ' name=' . $csvStr['g_name'] .
                            ' - not found RN=' . $prn ;
                    } else {
                        if (count($parent) == 0){
                            $errArr = 'Должности : ' . $csvStr['g_name'] . ' не найдено подразделение - prn=' . $prn;
                            break;
                            }
                        $newPosition->order_project_department_id = $parent[0]['id'];
                        $newPosition->setAttributes($csvStr);
                        //****** словарные
                        $start = stripos($csvStr['b_department_code'],'<g>')+3;
                        $end = stripos($csvStr['b_department_code'],'</g>');
                        $globalCode = substr($csvStr['b_department_code'], $start, ($end - $start));
                        if (($end - $start) > 0){
                            //   echo   $globalCode . '  ->  '  . $csvStr['b_department_code'] . PHP_EOL;
                            $newPosition->global_code = $globalCode;
                        }
                        //*********
                        $category_personal = iconv_substr ($csvStr['a_main_code'], 1 , 2 , 'UTF-8' );
                        switch ($category_personal){
                            case "нс":
                                $newPosition->position_category = 1;
                                break;
                            case "мс":
                                $newPosition->position_category = 2;
                                break;
                            case "цд":
                                $newPosition->position_category = 3;
                                break;
                            case "цп":
                                $newPosition->position_category = 4;
                                break;
                            default:
                                $newPosition->position_category = 99;
                                break;
                        }
                        $group = iconv_substr ($csvStr['a_main_code'], 3 , 2 , 'UTF-8' );
                        switch ($group){
                            case "ко":
                                $newPosition->position_group = 1;
                                break;
                            case "кр":
                                $newPosition->position_group = 2;
                                break;
                            case "кс":
                                $newPosition->position_group = 3;
                                break;
                            case "кз":
                                $newPosition->position_group = 4;
                                break;
                            case "кв":
                                $newPosition->position_group = 5;
                                break;
                            case "кх":
                                $newPosition->position_group = 6;
                                break;
                            case " г?":
                                $newPosition->position_group = 7;
                                break;
                            case "c?":
                                $newPosition->position_group = 8;
                                break;
                            case "в?":
                                $newPosition->position_group = 9;
                                break;
                            case "і?":
                                $newPosition->position_group = 10;
                                break;
                            default:
                                $newPosition->position_group = 99;
                                break;
                        }
                        $attestation_to_civil = iconv_substr ($csvStr['a_main_code'], 5 , 1 , 'UTF-8' );
                        switch ($attestation_to_civil){
                            case "0":
                                $newPosition->attestation_to_civil = 0;
                                break;
                            case "1":
                                $newPosition->attestation_to_civil = 1;
                                break;
                            default:
                                $newPosition->position_group = 9;
                                break;
                        }
                        $financing_source = iconv_substr ($csvStr['a_main_code'], 7 , 2 , 'UTF-8' );
                        $newPosition->financing_source =  $financing_source;
                        //****
                    }
                    if (!$newPosition->save()){
                        $errArr =  ' Помилка збереження посади: ' . $csvStr['g_name'] . ' prn=' . $prn;
                        foreach ($newPosition->getErrors() as $key => $value) {
                            $errArr .=  $key . ': ' . $value[0] . ' *** ';
                        }
                    }
                    break;
                case 'и':{
                    $departmentTarget = OrderProjectDepartment::find()
                        ->andWhere(['x_rn' => $csvStr['y_prn'], 'staff_order_id' => $staff_order_id ])->one();
                    if (!isset($departmentTarget)){
                        $errArr =  $cat_type . ' rn=' . $csvStr['x_rn'] . ' prn=' . $csvStr['y_prn'] . ' *** SUMMARY NOT FOUND ' .
                            $csvStr['g_name'];

                    } else {
                        $departmentTarget->summary_txt = $csvStr['g_name'];
                        $departmentTarget->summary_amount = $csvStr['i_positions_amount'];
                        if ($departmentTarget->save()){
                        } else {
                            $errArr =  ' Помилка збереження ВСЬОГО : ';
                            foreach ($departmentTarget->getErrors() as $key => $value) {
                                $errArr .=  $key . ': ' . $value[0] . ' *** ';
                            }
                        }
                    }
                }
            }
        } catch  (Exception $e){
            $errArr = 'Ошибка загрузки ' . $e->getMessage();
            return $errArr;
        }
        return $errArr;
    }

    /**
     * добавление ряда из массива CSV в БД (подразделение, должность, ИТОГО)
     * @param $csvStr
     * @param $root_id
     * @param $staff_order_id
     * @param $error_id
     * @return string
     */
    public static function addCSV_str_first($csvStr, $root_id, $staff_order_id, $error_id ){
        $errArr = '';
        try{
            $prn = strval($csvStr['y_prn']);
            $cat_type = iconv_substr ($csvStr['a_main_code'], 0 , 1 , 'UTF-8' );
            switch ($cat_type){
                case 'н': //*********************************************   ПОДРАЗДЕЛЕНИЕ
                    //   echo 'department ' .  $csvStr['g_name'] . '<br>';
                    $department = new OrderProjectDepartment();
                    $department->setAttributes($csvStr);
                    $department->name = $csvStr['g_name'];
                    $department->x_rn = $csvStr['x_rn'];
                    $department->staff_order_id = $staff_order_id;
                    $department->parent_id = $parent_id = $error_id;
                    $department->summary_amount_new = $department->summary_amount;

                    //----------- словари
                    $start = stripos($csvStr['b_department_code'],'<g>')+3;
                    $end = stripos($csvStr['b_department_code'],'</g>');
                    $globalCode = substr($csvStr['b_department_code'], $start, ($end - $start));
                    if (($end - $start) > 0){
                        //   echo   $globalCode . '  ->  '  . $csvStr['b_department_code'] . PHP_EOL;
                        $department->global_code = $globalCode;
                    }
                    $start = stripos($csvStr['b_department_code'],'<b>')+3;
                    $end = stripos($csvStr['b_department_code'],'</b>');
                    /*
                    $subordination = substr($csvStr['b_department_code'], $start, ($end - $start));
                    if (($end - $start) > 0){
                        //   echo   $subordination . '  ->  '  . $csvStr['b_department_code'] . PHP_EOL;
                        $department->subordination = $subordination;
                    }
                    */
                    $department->save();
                    if (!$department->save()) {
                        $errArr = 'Ошибка сохранения подразделения : ';
                        foreach ($department->getErrors() as $key => $value){
                            $errArr .=  $key . ': ' . $value[0] . ' *** ';
                        }
                    };
                    break;
                case 'п':  //********************************************** ПОСАДА
                    //  echo 'positiom ' .  $csvStr['g_name'] . '<br>';
                    $newPosition = new OrderProjectPosition();
                    $newPosition->name = $csvStr['g_name'];
                    $newPosition->staff_order_id = $staff_order_id;
                    $newPosition->x_rn = $csvStr['x_rn'];
                    $newPosition->y_prn = $csvStr['y_prn'];

                    $newPosition->order_project_department_id = $error_id;
                    $newPosition->setAttributes($csvStr);
                    $newPosition->positions_amount_new = $newPosition->i_positions_amount;

                    //****** словарные
                    $start = stripos($csvStr['b_department_code'],'<g>')+3;
                    $end = stripos($csvStr['b_department_code'],'</g>');
                    $globalCode = substr($csvStr['b_department_code'], $start, ($end - $start));
                    if (($end - $start) > 0){
                        $newPosition->global_code = $globalCode;
                    }
                    //*********
                    $category_personal = iconv_substr ($csvStr['a_main_code'], 1 , 2 , 'UTF-8' );
                    switch ($category_personal){
                        case "нс":
                            $newPosition->position_category = 1;
                            break;
                        case "мс":
                            $newPosition->position_category = 2;
                            break;
                        case "цд":
                            $newPosition->position_category = 3;
                            break;
                        case "цп":
                            $newPosition->position_category = 4;
                            break;
                        default:
                            $newPosition->position_category = 99;
                            break;
                    }
                    $group = iconv_substr ($csvStr['a_main_code'], 3 , 2 , 'UTF-8' );
                    switch ($group){
                        case "ко":
                            $newPosition->position_group = 1;
                            break;
                        case "кр":
                            $newPosition->position_group = 2;
                            break;
                        case "кс":
                            $newPosition->position_group = 3;
                            break;
                        case "кз":
                            $newPosition->position_group = 4;
                            break;
                        case "кв":
                            $newPosition->position_group = 5;
                            break;
                        case "кх":
                            $newPosition->position_group = 6;
                            break;
                        case " г?":
                            $newPosition->position_group = 7;
                            break;
                        case "c?":
                            $newPosition->position_group = 8;
                            break;
                        case "в?":
                            $newPosition->position_group = 9;
                            break;
                        case "і?":
                            $newPosition->position_group = 10;
                            break;
                        default:
                            $newPosition->position_group = 99;
                            break;
                    }
                    $attestation_to_civil = iconv_substr ($csvStr['a_main_code'], 5 , 1 , 'UTF-8' );
                    switch ($attestation_to_civil){
                        case "0":
                            $newPosition->attestation_to_civil = 0;
                            break;
                        case "1":
                            $newPosition->attestation_to_civil = 1;
                            break;
                        default:
                            $newPosition->position_group = 9;
                            break;
                    }
                    $financing_source = iconv_substr ($csvStr['a_main_code'], 7 , 2 , 'UTF-8' );
                    switch ($financing_source){
                        case 'дз':
                            $newPosition->financing_source =  1;
                            break;
                        default:
                            $newPosition->financing_source =  2;
                            break;
                    }

                    //****

                    if (!$newPosition->save()){
                        $errArr =  ' Помилка збереження посади: ' . $csvStr['g_name'] . ' prn=' . $prn;
                        foreach ($newPosition->getErrors() as $key => $value) {
                            $errArr .=  $key . ': ' . $value[0] . ' *** ';
                        }
                    }
                    break;
                case 'и':{
                    $departmentTarget = OrderProjectDepartment::find()
                        ->andWhere(['x_rn' => $csvStr['y_prn'], 'staff_order_id' => $staff_order_id ])->one();
                    if (!isset($departmentTarget)){
                        $errArr =  $cat_type . ' rn=' . $csvStr['x_rn'] . ' prn=' . $csvStr['y_prn'] . ' *** SUMMARY NOT FOUND ' .
                            $csvStr['g_name'];

                    } else {
                        $departmentTarget->summary_txt = $csvStr['g_name'];
                        $departmentTarget->summary_amount = $csvStr['i_positions_amount'];
                        if ($departmentTarget->save()){
                        } else {
                            $errArr =  ' Помилка збереження ВСЬОГО : ';
                            foreach ($departmentTarget->getErrors() as $key => $value) {
                                $errArr .=  $key . ': ' . $value[0] . ' *** ';
                            }
                        }
                    }
                }

            }
        } catch  (Exception $e){
            $errArr = 'Ошибка загрузки ' . $e->getMessage();
            return $errArr;
        }
        return $errArr;
    }

    /**
     * назначение предка подразделению, параметр - $csvStr - данные подразделения
     * @param $csvStr
     * @param $error_id
     * @param $department_id
     * @return string
     */
    public static function addCSV_parents_for_departments($csvStr, $error_id, $department_id, $staff_order_id, $minRn ){
        if ($csvStr['id'] == $department_id)
            return '';
        $errArr = '';
        try{
            $department = OrderProjectDepartment::findOne($csvStr['id']);
            if (isset($department)){
             //  return $department->name . ' yes';
             //   $prn = strval($department->y_prn);
                if (isset($department->x_rn) && $department->x_rn == $minRn){
                    //-- корневое подразхделение CSV
                    $department->parent_id = $department_id;
                    if (!$department->save()){
                        $errArr = 'Ошибка назначения предков подразделения ' . $csvStr['name'] . ' :';
                        foreach ($department->getErrors() as $key => $value){
                            $errArr .=  $key . ': ' . $value[0] . ' *** ';
                        }
                    } else {
                        $errArr = $csvStr['name'] . ' - ok';
                    }
                } else {
                    //-- he корневое подразхделение CSV
                    if (!isset($department->y_prn))
                        return $department->name . ' нет y_prn';
                    $prn = $department->y_prn;
                    $parent = OrderProjectDepartment::find()
                        ->andWhere(['x_rn' => $prn, 'staff_order_id' => $staff_order_id])
                        ->one();
                    if (isset($parent)){
                        $department->parent_id = $parent->id;
                        if (!$department->save()){
                            $errArr = 'Ошибка назначения предков подразделения ' . $csvStr['name'] . ' :';
                            foreach ($department->getErrors() as $key => $value){
                                $errArr .=  $key . ': ' . $value[0] . ' *** ';
                            }
                        } else {
                            $errArr =' - ok';
                        }
                    } else {
                        $department->parent_id = $error_id;
                        if (!$department->save()){
                            $errArr = 'Ошибка назначения предков подразделения ' . $csvStr['name'] . ' :';
                            foreach ($department->getErrors() as $key => $value){
                                $errArr .=  $key . ': ' . $value[0] . ' *** ';
                            }
                        } else {
                            $errArr = $csvStr['name'] . ' - ok';
                        }

                        $errArr = $csvStr['name'] . ' - не знайдено предка prn=' . $prn;
                    }

                }


            } else {
              //  $errArr = $csvStr['name'] . ' - не знайдено ' . $csvStr['id'];
                $errArr =  ' - не знайдено ';
            }
        } catch  (Exception $e){
            $errArr = 'Ошибка загрузки ' . $e->getMessage();
            return $errArr;
        }
        return $errArr;
    }

    /**
     * назначение предка должности, параметр - $csvStr - данные должности
     * @param $csvStr
     * @param $error_id
     * @param $department_id
     * @return string
     */
    public static function addCSV_parents_for_positions($csvStr, $error_id, $department_id , $staff_order_id){
        $errArr = '';
        try{
            $position = \app\modules\structure\models\OrderProjectPosition::findOne($csvStr['id']);
            if (isset($position)){
             //  return $department->name . ' yes';
             //   $prn = strval($department->y_prn);
                if (!isset($position->y_prn))
                    return $position->name . ' нет y_prn';
                $prn = $position->y_prn;
                $parent = OrderProjectDepartment::find()
                    ->andWhere(['x_rn' => $prn, 'staff_order_id' => $staff_order_id])
                    ->one();
                if (isset($parent)){
                    $position->order_project_department_id = $parent->id;
                    if (!$position->save()){
                        $errArr = 'Ошибка назначения предков должности' . $csvStr['name'] . ' :';
                        foreach ($position->getErrors() as $key => $value){
                            $errArr .=  $key . ': ' . $value[0] . ' *** ';
                        }
                    } else {
                        $errArr =' - ok';
                    }
                } else {
                    $position->order_project_department_id = $error_id;
                    if (!$position->save()){
                        $errArr = 'Ошибка назначения предков должности ' . $csvStr['name'] . ' :';
                        foreach ($position->getErrors() as $key => $value){
                            $errArr .=  $key . ': ' . $value[0] . ' *** ';
                        }
                        return $errArr;
                    }
                    $errArr =' - не знайдено предка prn=' . $prn;
                }

            } else {
              //  $errArr = $csvStr['name'] . ' - не знайдено ' . $csvStr['id'];
                $errArr =  ' - не знайдено ';
            }
        } catch  (Exception $e){
            $errArr = 'Ошибка загрузки ' . $e->getMessage();
            return $errArr;
        }
        return $errArr;
    }

    /**
     * Пересчет численности подразделений проекта
     * @return string
     */
    public static function amountRefresh($order_id, $department_id){
        $result = '';
        try{
            //-- изменение всех подразделений
            $d = OrderProjectDepartment::updateAll(['summary_amount_del' =>0, 'summary_amount_add' => 0,
                'summary_amount_new' => 0, 'summary_amount' => 0],
                ['staff_order_id' => $order_id]);

            $positions = OrderProjectPosition::find()
                ->innerJoinWith('orderProjectDepartment')
                ->andWhere(['order_project_department.staff_order_id' => $order_id])
                ->all();

            foreach($positions as $position){
                $position->positions_amount_new = $position->i_positions_amount;
                $position->creation = true;
                $position->save();
                //-- модифицировать свое подразделение
                $department = OrderProjectDepartment::findOne($position->order_project_department_id);
                if ($department_id != $department->id ){
                    $department->creation = !$department->changed;
                    $department->summary_amount += $position->i_positions_amount;
                    $department->summary_amount_new += $position->i_positions_amount;
                    $res = $department->save();
                }
                //-- модифицировать предков своего подразделения
                $firstParent = $department->parent_id;
                $node = OrderProjectDepartment::findOne($firstParent);
                if (isset($node)){
                    if ($department_id != $node->id ){
                        $node->summary_amount_new += $position->i_positions_amount;
                        $node->summary_amount += $position->i_positions_amount;
                        $node->creation = true;
                        $r = $node->save();
                    }
                    $pid = $node->parent_id;
                    do{
                        $node = OrderProjectDepartment::findOne($pid);
                        if (isset($node)){
                            $node->summary_amount_new += $position->i_positions_amount;
                            $node->summary_amount += $position->i_positions_amount;
                            $node->creation = true;
                            $node->save();
                            $pid = $node->parent_id;
                        }
                    } while($node != null);
                }
            }

        } catch  (Exception $e){
            $result = 'Ошибка модификации численности ' . $e->getMessage();
            return $result;
        }
        return $result;
    }
//-------------------------------------------------------------------------------------
    public static function readCSV_ToDb__($fileName, $staff_order_id, $department_id){
        if (!file_exists($fileName) ) {
            return 'file not found ' . $fileName;
        }
        if (!is_readable($fileName)) {
            return 'file not is_readable ' . $fileName;
        }
        $result = [];
        if (($handle = fopen($fileName, 'r')) !== false) {
            $colName = fgetcsv($handle, 1000, ',');
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $buf = [];
                for ($i=0; $i < count($row); $i++){
                    $buf[$colName[$i]]=$row[$i];
                }
                if (!is_numeric($buf['i_positions_amount'])){
                    $buf['i_positions_amount'] = 0;
                }
                $errstr = self::addCSV_str($buf, $staff_order_id, $department_id) ;
                if ($errstr !=''){
                    $result[] = $errstr;
                }
            }
            fclose($handle);
            return $result;
        } else {
            return 'file not fgetcsv ' . $fileName;
        }
    }

    public static function addCSV_str_short__($csvStr, $staff_order_id, $department_id){
        $errArr = '';
        $prn = strval($csvStr['y_prn']);
        $cat_type = iconv_substr ($csvStr['a_main_code'], 0 , 1 , 'UTF-8' );
        switch ($cat_type){
            case 'н': //*********************************************   ПОДРАЗДЕЛЕНИЕ
                //   echo 'department ' .  $csvStr['g_name'] . '<br>';
                $department = new OrderProjectDepartment();
                $department->name = $csvStr['g_name'];
                $department->x_rn = $csvStr['x_rn'];
                $department->staff_order_id = $staff_order_id;
                $department->setAttributes($csvStr);
                if (!$department->save()) {
                    $errArr = 'Ошибка сохранения подразделения : ';
                    foreach ($department->getErrors() as $key => $value){
                        $errArr .=  $key . ': ' . $value[0] . ' *** ';
                    }
                };
                break;
            case 'п':  //********************************************** ПОСАДА
                //  echo 'positiom ' .  $csvStr['g_name'] . '<br>';
                $newPosition = new OrderProjectPosition();
                $newPosition->name = $csvStr['g_name'];
                $newPosition->staff_order_id = $staff_order_id;
                $newPosition->order_project_department_id = $department_id;
                if (!is_numeric($newPosition->i_positions_amount)){
                    $newPosition->i_positions_amount = 0;
                }

                $newPosition->setAttributes($csvStr);
                if (!$newPosition->save()){
                    $errArr =  ' Помилка збереження посади: ';
                    foreach ($newPosition->getErrors() as $key => $value) {
                        $errArr .=  $key . ': ' . $value[0] . ' *** ';
                    }
                }
                break;
            case 'и':{
                $departmentTarget = OrderProjectDepartment::find()
                    ->andWhere(['x_rn' => $csvStr['y_prn'], 'staff_order_id' => $staff_order_id ])->one();
                if (isset($departmentTarget)){
                    $departmentTarget->summary_txt = $csvStr['g_name'];
                    $departmentTarget->summary_amount = $csvStr['i_positions_amount'];
                    if ($departmentTarget->save()){
                    } else {
                        $errArr =  ' Помилка збереження ВСЬОГО : ';
                        foreach ($departmentTarget->getErrors() as $key => $value) {
                            $errArr .=  $key . ': ' . $value[0] . ' *** ';
                        }

                    }
                }
            }
                break;
        }
        return $errArr;

    }



}