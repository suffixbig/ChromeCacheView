<?php
/*
2017/10/05
動作會把所有*.html 拷貝到上1層目錄,最多處理3層目錄
好處:可以刪掉多餘的子目錄
壞處:這支程式只能執行一次，不行執行2次
php判斷並刪除空目錄及空子目錄的方法
*/
header("Content-Type:text/html;charset=utf-8");//全程式總編碼指定
date_default_timezone_set('Asia/Taipei');//設定系統時區
$brn="<br>\n";
$thisDir = ".";      //config.inc.php檔的相對路徑
$_file = basename(__FILE__);  //自行取得本程式名稱
$config['sdir']=dirname(__FILE__);// 將目前的目錄變更為 dirname(__FILE__)


$dirall_a=array();
$dirall=array();
$dirall3=array();
//函式功能 列出該路徑下所有的檔案包含子目錄
function get_dir_list($dir){//&$a 檔案加總變數 傳址參數,$dir 資料夾路徑
global $dirall_a;//注意這行
   if(is_dir($dir)){//如果是資料夾才執行
       $dh = opendir($dir);//打開資料夾
       chdir ($dir);//指定目錄
       while (($file = readdir($dh)) !== false) {//列出該目錄的所有檔案
           $fileb=pathinfo ( $file , PATHINFO_EXTENSION );// =>取得副檔名
           if (is_dir($file) && basename($file)!='.' && basename($file)!='..'){//若是資料夾 且非 . .. 就在呼叫自已一次 
               get_dir_list2($file);
           }else if($file != "." && $file != ".." &&  empty($fileb)){//若非 . .. 又沒有副檔名
               $dirall_a[]=$file;//輸出 完整檔案路徑檔名
           }
       }
       closedir($dh);//關閉資料夾
   }
}

//函式功能 列出該路徑下所有的檔案包含子目錄
function get_dir_list2($dir){//&$a 檔案加總變數 傳址參數,$dir 資料夾路徑

    global $dirall;//注意這行
           $files = array();
           if($handle = opendir($dir)) {
            while(false !== ($file = readdir($handle))){
                $fileb=pathinfo ( $file , PATHINFO_EXTENSION );// =>取得副檔名
                
                $curfile = $dir.'/'.$file;          // 當前目錄
                //print_r($file);exit;
                    if((string)$file === "." or (string)$file === ".."){
                    }elseif(is_dir($curfile)){
                        //print_r($file);echo"|";
                        get_dir_list3($curfile);
                    }else if($fileb=='html' or $fileb=='htm' ){
                        $dirall[]=$curfile;//輸出 完整檔案路徑檔名
                        //移動到上1層目錄
                        $fb=dirname(dirname($curfile))."/".$file;
                        phprename($curfile,$fb);//更名
                    }else if( empty($fileb) ){
                        //沒副檔名的
                        $dirall[]=$curfile;//輸出 完整檔案路徑檔名
                        phprename($curfile,$curfile.".html");//更名           
                    }else{

                    }
               }
            closedir($handle);
           }
}

//子子目錄
function get_dir_list3($dir){//&$a 檔案加總變數 傳址參數,$dir 資料夾路徑
    global $dirall3;//注意這行
    //echo "運行第3層目錄".$dir."|";
    if($handle3 = opendir($dir)) {
     while(false !== ($file = readdir($handle3))){
         $fileb=pathinfo ( $file , PATHINFO_EXTENSION );// =>取得副檔名
         //print_r($fileb);
         $curfile = $dir.'/'.$file;          // 當前目錄加檔案名
            if((string)$file === "." or (string)$file === ".."){
             }else if(is_dir($curfile)){
                 //還是目錄不處理
             }else if($fileb=='html' or $fileb=='htm' ){
                 $dirall3[]=$curfile;//輸出 完整檔案路徑檔名
                 //移動到上1層目錄
                 $fb=dirname(dirname($curfile))."/".$file;
                 phprename($curfile,$fb);//更名  
             }else if( empty($fileb) ){
                 //沒副檔名的
                 $dirall3[]=$curfile;//輸出 完整檔案路徑檔名
                 phprename($curfile,$curfile.".html");//更名           
             }else{

             }
        }
     closedir($handle3);
    }
}
//php判斷並刪除空目錄及空子目錄的方法
/** 刪除所有空目錄
* @param String $path 目錄路徑
*/
function rm_empty_dir($path){
    if(is_dir($path) && ($handle = opendir($path))!==false){
        while(($file=readdir($handle))!==false){     // 遍歷文件夾
            if($file!='.' && $file!='..'){
                $curfile = $path.'/'.$file;          // 當前目錄
                if(is_dir($curfile)){                // 目錄
                    rm_empty_dir($curfile);          // 如果是目錄則繼續遍歷
                    if(count(scandir($curfile))==2){ // 目錄為空,=2是因為. 和 ..存在
                        rmdir($curfile);             // 刪除空目錄
                    }
                }
            }
        }
        closedir($handle);
    }
}

//複製檔案
function phpcopy($path1,$path2){
if (copy($path1, $path2)) {
    // 檔案複製成功
} else {
    // 檔案複製失敗
}
}
//複製更名檔案
function phprename($path1,$path2){
    if (rename($path1, $path2)) {
        // 檔案複製成功
        echo "移動檔案".$path1."=>".$path2."<br>\n";
    } else {
       echo "移動檔案失敗";
    }
}


get_dir_list($config['sdir']);

echo "根目錄檔案";
print_r($dirall_a);
    if(count($dirall_a)>0){
        for($i=0;$i<count($dirall_a);$i++){
            $a=$dirall_a[$i];
            $b=$a.".html";
            echo "修改檔名".$a."=>".$b;
            phprename($a,$b);
        }
    }
//echo "子目錄檔案";
//print_r($dirall);

echo "第1層目錄".$brn;
echo "<pre>\n";
print_r($dirall);
echo "</pre>\n";
echo "第2層目錄".$brn;
echo "<pre>\n";
print_r($dirall3);
echo "</pre>\n";
echo "將.html檔案全部移動到上一層目錄".$brn;
echo "刪除所有空目錄".$brn;
rm_empty_dir($config['sdir']);//刪除所有空目錄
?>