<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace MyEasyPHP\Libs\ext;

/**
 * Description of Csv
 *
 * @author Nganthoiba
 */
class Csv {
    public function array2csv($header = array(), array &$array)
    {
        if (count($array) == 0) {
          return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        if(count($header)>0){
           fputcsv($df, $header);
        }else{
           fputcsv($df, array_keys(reset($array)));
        }
        foreach ($array as $row) {
           fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }
    
    public function setHeaders($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        //header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }
    
    public function getCsv($header, $data, $filename){
        $this->setHeaders($filename);
        return $this->array2csv($header,$data);
    }
    
}
