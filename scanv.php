<?php

/*********************************************************
- Tool Scan shell vesion 1.2
- Code by : [K]id  
- Fixed 5.3 by: Vagabondst
**********************************************************/
error_reporting (E_ALL);	// Mở báo lỗi 
ini_set("memory_limit","2000M");
ini_set("safe_mode","off");
$safe_mode = @ini_get('safe_mode');
if (!$safe_mode)
set_time_limit(0);
if (@!isset($_POST['key']))
{
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Scan Shell v1.2</title>
<style>
body { 
font-family:arial;
}
</style>
</head>
<body bgcolor=#F0F0F0 text=green size=2><center>
<form method='post' action=''>
<div style="border-style: solid; border-width: 1px; padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px">
<h1>Scan Shell - v1.2</h1> 
				  <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Thư mục: <input type='text' size=70 name='folder' value='<?echo $_SERVER['DOCUMENT_ROOT']?>/'/></div>  				  
                  &nbsp;<div>&nbsp; Từ khóa:&nbsp; <input type='text' size=70 name='key' value='base64_decode'/></div>  
                  <p><input type='submit' name='submit' value='Tìm kiếm' /></p>  </div>
				  <div align=left><br>Step 1: Nhập đường dẫn thư mục cần quét</div>
				  <div align=left>Step 2: Nhập thư từ khoá cần tìm</div>
            </form>

<br><br>
---------------------------------------------------------------------------------
<br><br>Copyright 2016 <a title="Vagabondst" href="http://chiaseit.net"><font color=red>ChiaSeIT.net</font></a><br/> 
<br>---------------------------------------------------------------------------------
</center>

</body>
</html>
  <?
}
else
{
if ($_POST['folder']) $folder = $_POST['folder']; else $folder = $_SERVER['DOCUMENT_ROOT'];
define('Keyword',$_POST['key']);									// Tìm kiếm từ khoá
define('TAB',"&nbsp;&nbsp;&nbsp;&nbsp;");						
define('IGNORE_EXTENSIONS',"jpg pdf zip psd doc gif swf xls gz txt");	// Không tìm những File 
define("MAX_SIZE",1024*1024*1024);									// Size tối đa
date_default_timezone_set ("Asia/Krasnoyarsk");
define("IGNORE_BEFORE", strtotime('2012-07-07') );				// Tìm File trước ngày
$shell = $_SERVER["PHP_SELF"];

function findexts($filename)
{
	$filename = strtolower($filename) ;
	$exts = explode("[/\\.]", $filename) ;
	$n = count($exts)-1;
	$exts = $exts[$n];
	return strtolower($exts);
} 

function check_dir($directory,$level) {
	global $virus_detected, $all, $detect_errors_only, $detected_Keyword_in_test_script;

	$indent='';
	for ($count=0;$count<$level;$count++) {
		$indent.=TAB;
	}
	$level++;
	$read_dir=opendir($directory);	// Mở thư muc hiện tại
	while ($file=readdir($read_dir)) {
		$filepath=$directory.'/'.$file;
		if ($detect_errors_only && $virus_detected) {
			exit;
		}
		if (is_dir($filepath)) {
			// Thư mục
			if ( ($file<>'.') && ($file<>'..') ) {
				check_dir($filepath,$level);
			}
		}
		else {
			if (is_file($filepath)) {
				// Tệp
				if ( (is_readable($filepath) )  &&  (!stristr(IGNORE_EXTENSIONS, findexts($file)))  ) {
					if ((filesize($filepath)< MAX_SIZE) && (filemtime($filepath)>IGNORE_BEFORE) ){
						$fileentry=date('H:i j/m/Y ',filemtime($filepath)).'- '.$directory.'<font color=red>/</font>'.$file;
						$filestring=file_get_contents($filepath);
						$found=stripos($filestring,Keyword); // PHP 5 ONLY
						$found=stristr($filestring,Keyword);flush();
						if ($found==false) {
							if ( (!$detect_errors_only) && (!$all) ) {
							echo($filepath.'  <font color=#FFFFFF>OK</font><br/>');
							}
						}
						else {
							if ($file=='scan_file.php'){
								$detected_Keyword_in_test_script=true;
							}
							else {
								$virus_detected=true;
								if ($detect_errors_only) {
									echo('<b style="color:#555">Tệp tin đáng nghi phát hiện</b><br/>');
								}
								else {
									echo(TAB.'<b style="color:#555">'.$fileentry.'</b><br/>');
								}
							} 
						}
						$found='';
					}
					else {
						if ( (!$detect_errors_only) && (!$all) ) {
							echo($filepath.'  <b style="color:yellow">NOT CHECKED - File quá lơn hoặc đã tồn tại trước đó</b><br/>');
						}
					}
				}
				else {
					if ( (!$detect_errors_only) && (!$all) ) {
						echo($filepath.'  <b style="color:yellow">NOT CHECKED - không thuộc kiểu tìm kiếm</b><br/>');
					}
				}
			}
			else {
			}
		}
	}	
	closedir($read_dir);
} 

$virus_detected=false;
$all=true;
$detect_errors_only=false;
$detected_Keyword_in_test_script=false;
if (isset($_GET['all'])) {
	$all=false;
}
if (isset($_GET['detect_errors_only'])) {
	$detect_errors_only=true;
}

echo<<<END1
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Scan Shell v1.2</title>
<style>
body { 
font-family:arial;
}
</style>
</head>
<body bgcolor=#F0F0F0 text=green size=2>
END1;
?>
<div style="border-style: solid; border-width: 1px; padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px">
<h2>Truy xuất từ khóa "<?echo Keyword?>" trong hệ thống !</h2>
Bỏ qua loại file : <font color=orange><?echo IGNORE_EXTENSIONS?>.</font><br/>
Bỏ qua File vượt quá : <font color=orange><?echo MAX_SIZE?> bytes.</font><br/>
Tìm kiếm File trước ngày :<font color=orange> <?echo date('j/m/Y',IGNORE_BEFORE)?>.</font><br/><br></div>
<br>[<a href="<?echo $shell?>?all">Hiển thị tất cả</a>]
<br/><br/>
<?

if ($all) {
	echo('<font color=green>Danh sách file tìm thấy:</font><br/><br/>');
}
check_dir($folder,0);



if ($virus_detected) {
	echo('<br/><b style="color:##555">Tìm kiếm thành công!</b><br>&nbsp;<br/>');
}
else {
echo('<br/><b style="color:#55">Không có File chứa từ khóa!</b><br>&nbsp;<br/>');
}

?>
</body>
</html>
<?
}
?>