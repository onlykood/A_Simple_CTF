<?php
session_start();
if(!isset($_SESSION['flag'])){
	include '404.html';
	die();
}
$flag=$_SESSION['flag'];
//1.创建画布，默认的背景是黑色  
$im=imagecreatetruecolor(100,100);  
//默认是黑色背景，修改为白色  
$white=imagecolorallocate($im,255,255,255);
$black=imagecolorallocate($im, 0, 0, 0);
$red=imagecolorallocate($im, 255, 0, 0);
imagefill($im,0,0,$white);  

#for($x=0;$x<100;$x++){
#	imagesetpixel($im,$x,1,$red);
#}
//画横线
for($y=1;$y<100;$y+=2){
	for($x=0;$x<100;$x++){
		imagesetpixel($im,$x,$y,$black);
	}
}

//画竖线
for($x=0;$x<100;$x+=9){
	for($y=0;$y<100;$y++){
		imagesetpixel($im, $x, $y, $white);
	}
}


$x=1;
$y=0;


$str="Let's look this lyrics:The black sky hangs down,The bright stars follow,The insect winged insect flies,Who are you missing,The space star bursts into tears,The ground rose withers,The cold wind blows the cold wind to blow,So long as has you to accompany,The insect fly rests,A pair of pair only then beautiful,Did not fear darkness only fears brokenheartedly,No matter is tired,Also no matter four cardinal points.Emmmm,It looks like you don't care about this lyrics. Well, this is flag:".$flag;
if(strlen($str)>550){
	die();
}
$str=str_pad($str,550,$str);
#echo strlen($str).'<br>';
#echo $str;
#die();
for($i=0;$i<strlen($str);$i++){
	//转化为二进制
	$a=base_convert(unpack('H*', $str[$i])[1], 16, 2);
	$a=str_pad($a,8,'0',STR_PAD_LEFT);
	for($j=0;$j<strlen($a);$j++){
		if($a[$j]){
			imagesetpixel($im, $x, $y, $black);
		}
		$x+=1;
	}
	if(($i+1)%11==0){
		$x=1;
		$y+=2;
		#break;
	}
	else{
		$x+=1;
	}
	#print_r($a);
	#echo ' ';
}

header('Content-type:application/octet-stream');
header('Content-Disposition:attachment;filename=1.png');
readfile('qr.png');
imagepng($im);
echo "Just to be luckily~";
//4.销毁该图片（释放内存--服务器内存）  
imagedestroy($im);  
