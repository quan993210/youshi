<?php
function getAuthImage($text) 
{
	$im_x = 74;  	//宽度
	$im_y = 22;		//高度	
	$im = imagecreatetruecolor($im_x, $im_y);
	
	//验证码字符颜色
	$font_r = mt_rand(0, 100);
	$font_g = mt_rand(0, 100);
	$font_b = mt_rand(0, 100);
	$text_c = ImageColorAllocate($im, $font_r, $font_g, $font_b);
	
	//背景色
	$bg_r = 230;
	$bg_g = 230;
	$bg_b = 230;	
	$buttum_c = ImageColorAllocate($im, $bg_r, $bg_g, $bg_b);
	
	imagefill($im, 0, 0, $buttum_c);

	putenv('GDFONTPATH=' . realpath('.'));
	
	$font = 't1.ttf';
	
	for ($i=0;$i<strlen($text);$i++)
	{
		$tmp = substr($text, $i, 1);
		$array = array(-1, 1);
		$p = array_rand($array);
		$an = $array[$p] * mt_rand(1, 10);		//角度
		$size = 16;		//字体大小
		imagettftext($im, $size, $an, 5+$i*$size, 20, $text_c, $font, $tmp);    //可以修改字符位置
	}


/*	$distortion_im = imagecreatetruecolor ($im_x, $im_y);

	imagefill($distortion_im, 0, 0, $buttum_c);
	
	for ( $i=0; $i<$im_x; $i++) 
	{
		for ( $j=0; $j<$im_y; $j++) 
		{
			$rgb = imagecolorat($im, $i , $j);
			if( (int)($i+20+sin($j/$im_y*2*M_PI)*10) <= imagesx($distortion_im)&& (int)($i+20+sin($j/$im_y*2*M_PI)*10) >=0 ) 
			{
				$val = 1; //扭曲程度
				imagesetpixel ($distortion_im, (int)($i+10+sin($j/$im_y*2*M_PI-M_PI*0.1)*$val) , $j , $rgb);
			}
		}
	}
	*/
	//加入干扰象素;
	$count = 260;//干扰像素的数量
	for($i=0; $i<$count; $i++)
	{
		$randcolor = ImageColorallocate($im, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
		imagesetpixel($im, mt_rand()%$im_x , mt_rand()%$im_y , $randcolor);
	}

	$rand = mt_rand(5,30);
	$rand1 = mt_rand(15,25);
	$rand2 = mt_rand(5,10);
	for ($yy=$rand; $yy<=+$rand+2; $yy++)
	{
		for ($px=-80;$px<=80;$px=$px+0.1)
		{
			$x=$px/$rand1;
			if ($x!=0)
			{
				$y=sin($x);
			}
			$py=$y*$rand2;

			//imagesetpixel($im, $px+80, $py+$yy, $text_c);
		}
	}

	//设置文件头;
	//Header("Content-type: image/JPEG");

	//以PNG格式将图像输出到浏览器或文件;
	ImagePNG($im);

	//销毁一图像,释放与image关联的内存;
	ImageDestroy($im);
	ImageDestroy($im);
}
//验证码文字生成函数
function make_rand($length=4, $format="ALL")
{
	$str="";
	switch($format) 
	{ 
			case 'ALL':
				$str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'; 
				break;
			case 'CHAR':
				$str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
				break;
			case 'NUMBER':
				$str='0123456789'; 
				break;
			default :
				$str='ABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
				break;
    }
	$str_len = strlen($str)-1;
	$result="";
	for($i=0;$i<$length;$i++)
	{
		$num[$i]=rand(0,$str_len);
		$result.=$str[$num[$i]];
	}
	return $result;
}


//输出调用
$checkcode = make_rand(4, "NUMBER");
session_start();//将随机数存入session中
$_SESSION['safecode']=md5($checkcode);
getAuthImage($checkcode);
?>