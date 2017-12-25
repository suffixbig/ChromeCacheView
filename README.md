# ChromeCacheView
----------------------------------------

如何把一個線上的網站抓下來成為全靜態網頁
===
作者:台灣碼農

目的:各位前端設計師是否有這樣的需求?要抓下一整個網站，保留其原本目錄結構，存為全靜態網頁，並且點擊連結要跳到相對應頁面。
一個網站正常成型=>草圖=>設計=>前端工程師:純html切版=>後端工程師套版=>完工
我一個在隔壁公司上班的朋友，估且稱他為，連作品都沒有的前端，我問?你作品都有沒有保留的?他答上線的東西就沒辦法再搞回全靜態網頁了。
原來他不會!ㄎㄎ !有方法的!連那種要登入才能看到的頁面都能抓下來。

1. 使用ChromeCacheView.exe 這個工具 把我瀏覽過的網頁，依照原本的目錄結構抓下來
2. 搭配一個.htaccess檔案 讓抓下來的靜態網頁 點擊連結都能要跳到相對應頁面

----------------------------------------
ChromeCacheView 工具下載處

[[英文版]](http://www.nirsoft.net/utils/chrome_cache_view.html) [[中文版]](https://freewarehome.tw/pc/chromecacheview/)

![ChromeCacheView](https://i.imgur.com/tI0q7mp.jpg)

![ChromeCacheView3](https://i.imgur.com/eeEBnjY.jpg)

![ChromeCacheView2](https://i.imgur.com/34N2NfZ.jpg)

ChromeCacheView 是一款小巧的工具程式，可讀取 Google Chrome 網頁瀏覽器的暫存區或快取 (cache) 資料夾，並顯示目前儲存在快取中的所有檔案。對於快取中的每個檔案，程式會顯示下列資訊：網址、內容類型、檔案大小、最後修改時間、最後存取時間、存取次數、失效時間、伺服器名稱以及其他資料。

因此，您可以輕易地選取快取清單中一個或多個項目，然後將這些檔案
保存下來

----------------------------------------
我舉例網上一個別人的網站網址結構如下

----------------------------------------

首頁			https://coinp2p.io/
登入			https://coinp2p.io/Login
註冊			https://coinp2p.io/Register
所有刊登		https://coinp2p.io/ItemList/
	刊登內容頁	https://coinp2p.io/ItemDetail?Item_id=121576994
我要刊登		https://coinp2p.io/PostItem
幫助
	參考現價	https://coinp2p.io/CoinPrice
	問題回報	https://coinp2p.io/Report
帳號設定		https://coinp2p.io/AccountSettings

----------------------------------------
使用ChromeCacheView.exe 這個工具 抓下來的檔案結構會是

----------------------------------------
首頁			/
登入			/Login.html
註冊			/Register/Report.html
所有刊登		/ItemList/ItemList.html
	刊登內容頁	/ItemDetail.html
我要刊登		/PostItem/PostItem.html
幫助
	參考現價	/CoinPrice/CoinPrice.html
	問題回報	/Report/Report.html
帳號設定		/AccountSettings/AccountSettings.html

----------------------------------------

該如何做呢-我們要利用 .htaccess 簡化網址的原理
===
```
為了簡化網址或是為了安全理由, 會希望將副檔名隱藏. 也就是說原本
http://www.xxx.yyy/test.php
只需要
http://www.xxx.yyy/test
就可以連上, 這樣可以讓網站所有連結都不出現副檔名
這就像yahoo的新服務 知識+ 一樣裡頭的連結完全沒出現.php

下面講一下我找到的設定方法
1.
到apache的http.conf找到下面這行
#LoadModule rewrite_module modules/mod_rewrite.so
這一行註解拿掉
LoadModule rewrite_module modules/mod_rewrite.so
於是rewrite_mod模組啟動了! (他的功用請參考官方資訊)
2.
在 http.conf加入

AllowOverride all #這行的意思是說, 允許 .htaccess 檔的內容覆蓋這邊的設定
Order allow,deny
Allow from all

3.
新增一個 .htaccess檔在剛剛的目錄底下, 在裡頭寫入

Options +FollowSymlinks
#下行是設定要不要啟用rewrite engine, 這是runtime的設定檔,不需restartserver
RewriteEngine on
#下行是將檔名中沒有slash 和 . 的檔名, 轉向到 .php的檔案
RewriteRule ^([^./]+)/?$ $1.php [L]

4. 剛剛已經針對某個目錄做設定, 之後若底下的子目錄也要有同樣功能 ,請再將 .htaccess檔直接複製到要套用的子目錄即可
```
更多關於.htaccess檔案的參數用法
===
http://fecbob.pixnet.net/blog/post/38253781-apache-rewrite%E5%AF%A6%E7%8F%BEurl%E7%9A%84%E8%B7%B3%E8%BD%89%E5%92%8C%E5%8A%9F%E8%83%BD%E8%AE%8A%E6%95%B8%E5%90%8D%E7%A8%B1%E8%B7%B3%E8%BD%89-

當然上面的例子是 php 用的 我們html靜態頁面用的.htaccess不一樣
===

方法1 思路
===

```
無論/app 或 /app/ 都跳=> /app/app.htm

http://h61.test.com/miner/?coin=btc 會=>
http://h61.test.com/miner/miner.htm?coin=btc
```

## .htaccess檔案 內容 修改如下

```
#超級模擬原網站結構
# 打開重寫引擎
RewriteEngine on
# 設定基準目錄，從根目錄開始比對
RewriteBase /
#要排除不比對的目錄
RewriteRule ^(application|system) - [F,L]
# 重新比對網址，若請求的檔案不存在。
RewriteCond %{REQUEST_FILENAME} !-f
# 重新比對網址，倘若請求網址是資料夾。只有當目標的檔名不是一個資料夾時，才讓下方的改寫規則作用。
#RewriteCond %{REQUEST_FILENAME} !-d
# INDEX.HTML的規則保留 不用打副檔名可以呼叫到的規則保留
# 無論/app 或 /app/ 都跳=> /app/app.htm
RewriteRule ^(.*)/$ $1/$1.htm [L]
#http://h61.test.com/miner/?coin=btc 會=>http://h61.test.com/miner/miner.htm?coin=btc
```

方法2 思路
===

```
-單層目錄結構處理法
把單層目錄的檔案拷貝到根目錄下
1.打/ 跳到首頁 index.html
2.http://h62.test.com/ItemList/ => http://h62.test.com/ItemList
```

## .htaccess檔案 內容 修改如下

```
Options +FollowSymlinks
#超級模擬原網站結構
# 打開重寫引擎
RewriteEngine on
# 設定基準目錄，從根目錄開始比對
RewriteBase /
#要排除不比對的目錄
RewriteRule ^(application|system) - [F,L]
# 重新比對網址，若請求的檔案不存在。
RewriteCond %{REQUEST_FILENAME} !-f
# 重新比對網址，倘若請求網址是資料夾。只有當目標的檔名不是一個資料夾時，才讓下方的改寫規則作用。
RewriteCond %{REQUEST_FILENAME} !-d
# INDEX.HTML的規則保留 不用打副檔名可以呼叫到的規則保留
# 把網址的/去掉
RewriteRule ^(.*)[/]$ $1 [R]
```

我後來又把檔案修改如下
## .htaccess檔案 內容 修改如下
```
<IfModule rewrite_module>
    RewriteEngine On
    # 去吧 強制走https .*
    #RewriteCond %{HTTPS} !=on
    #RewriteRule .* https://%{SERVER_NAME}%{REQUEST_URI} [END,R=301]

    # 找看看 .htm 檔案
    RewriteCond %{REQUEST_FILENAME}.htm -s
    RewriteRule . %{REQUEST_FILENAME}.htm [END]

    RewriteCond %{REQUEST_FILENAME}.html -s
    RewriteRule . %{REQUEST_FILENAME}.html [END]

    # 再找看看 .php 檔案
    RewriteCond %{REQUEST_FILENAME}.php -s
    RewriteRule . %{REQUEST_FILENAME}.php [END]
</IfModule>
```

## 這個方法還要搭配一支移動檔案的程式
動作會把所有*.html 拷貝到上1層目錄,最多處理3層目錄
好處:可以刪掉多餘的子目錄
壞處:這支程式只能執行一次，不行執行2次
php判斷並刪除空目錄及空子目錄的方法

```php
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
```

失敗的原因 
===
1.你的伺服器根本不吃.htaccess 設定
2.apache 沒有 httpd的rewrite模組（mod_rewrite）

# httpd.conf 設定有問題

```
win版有此行
LoadModule rewrite_module modules/mod_rewrite.so

LINUX版有如何在CentOS 7 Apache设置mod_rewrite
httpd -M
如果rewrite_module不会在输出里出现，通过编辑使它00-base.conf文件vi编辑：
[https://www.howtoing.com/how-to-set-up-mod-rewrite-for-apache-on-centos-7/](https://www.howtoing.com/how-to-set-up-mod-rewrite-for-apache-on-centos-7/)

<Directory /var/www/html>
AllowOverride All
</Directory>
 
<IfModule dir_module>
    DirectoryIndex index.php index.html index.htm
</IfModule>
```

成功範例
===
[http://h63.bkk.tw/](http://h63.bkk.tw/)

## 將線上的網站抓下來成為全靜態網頁的方法 這只是我想出方法3個當中其中一個，在LINE台灣工程師快滿500人的群中，3個月來僅有碼農,瓜瓜,凱大,Eddie 少數人分享過教學,讓我感覺台灣工程師技術交流的程度遠不及對岸，特此拋磚引玉，看有沒有人可以提出其他種寫法。(使用hackmd共享筆記會比較容易打字)

本筆記hackmd網址
===
https://hackmd.io/s/SycHsoznZ 

本筆記github位置
===
https://github.com/suffixbig/ChromeCacheView
