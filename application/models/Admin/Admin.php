<?php
class Application_Model_Admin_Admin
{
	/**
	可以统计中文字符串长度的函数
	@param $str 要计算长度的字符串
	@param $type 计算长度类型，0(默认)表示一个中文算一个字符，1表示一个中文算两个字符
	*
	*/
	function abslength($str)
	{
		if(empty($str)){
			return 0;
		}
		if(function_exists('mb_strlen')){
			return mb_strlen($str,'utf-8');
		}
		else {
			preg_match_all("/./u", $str, $ar);
			return count($ar[0]);
		}
	}
	/**
		utf-8编码下截取中文字符串,参数可以参照substr函数
		@param $str 要进行截取的字符串
		@param $start 要进行截取的开始位置，负数为反向截取
		@param $end 要进行截取的长度
	*/
	function utf8_substr($str,$start=0,$end) 
	{
		if(empty($str)){
			return false;
		}
		if (function_exists('mb_substr')){
			if(func_num_args() >= 3) {
				$end = func_get_arg(2);
				return mb_substr($str,$start,$end,'utf-8');
			}
			else {
				mb_internal_encoding("UTF-8");
				return mb_substr($str,$start);
			}       
	 
		}
		else {
			$null = "";
			preg_match_all("/./u", $str, $ar);
			if(func_num_args() >= 3) {
				$end = func_get_arg(2);
				return join($null, array_slice($ar[0],$start,$end));
			}
			else {
				return join($null, array_slice($ar[0],$start));
			}
		}
	}
	//**************************************************
	//利用正则表达式从文章在获取图片的路径
	function GetImageSrc($body)
	{
	   if(!isset($body)){
		return '';
	   }else{
		preg_match_all ("/<(img|IMG)(.*)(src|SRC)=[\"|'|]{0,}([h|\/].*(jpg|JPG|jpeg|JPEG|gif|GIF|png|PNG))[\"|'|\s]{0,}/isU",$body,$out);
		return $out[4];
	   }
	}
	

	/**
	* function my_image_resize 生成图片缩略图
	* $src_file : 需要处理图片的文件名  
	* $dst_file : 生成新图片的保存文件名
	* $new_width: 生成新图片的宽 
	* $new_height:生成新图片的高    
	*/
	function my_image_resize($src_file, $dst_file , $new_width , $new_height) 
	{
		if($new_width <1 || $new_height <1) {
			echo "图片宽、高设置错误!";
			exit();
		}
		
		if(!file_exists($src_file)) {
			echo $src_file . " 图片不存在!";
			exit();
		}
		
		// 图像类型
		//echo $src_file;exit;
		//$type=exif_imagetype($src_file);
		$type = strrchr($src_file,'.');
		$type = ltrim($type,'.');
		switch($type) {
			case jpg:
				$type=IMAGETYPE_JPEG ;
				break;
			case png:
				$type=IMAGETYPE_PNG ;
				break;
			case gif:
				$type=IMAGETYPE_GIF ;
				break;
		}
		//echo $type;exit;
		$support_type=array(IMAGETYPE_JPEG , IMAGETYPE_PNG , IMAGETYPE_GIF);
		if(!in_array($type, $support_type,true)) {
			echo "图片格式不支持，图片格式仅支持 jpg , gif , png";
			exit();
		}
		//Load image
		switch($type) {
			case IMAGETYPE_JPEG :
				$src_img=imagecreatefromjpeg($src_file);
				break;
			case IMAGETYPE_PNG :
				$src_img=imagecreatefrompng($src_file);
				break;
			case IMAGETYPE_GIF :
				$src_img=imagecreatefromgif($src_file);
				break;
			default:
				echo "载入图片失败!";
			exit();
		}
		
		//获取图片的宽、高
		$w=imagesx($src_img);
		$h=imagesy($src_img);
		$ratio_w=1.0 * $new_width / $w;
		$ratio_h=1.0 * $new_height / $h;
		$ratio=1.0;
		
		// 生成的图像的高宽比原来的都小，或都大 ，原则是取大比例放大，取大比例缩小（缩小的比例就比较小了）
		if( ($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)) {
			if($ratio_w < $ratio_h) {
				$ratio = $ratio_h ; // 情况一，宽度的比例比高度方向的小，按照高度的比例标准来裁剪或放大
			}else {
				$ratio = $ratio_w ;
			}
			
			// 定义一个中间的临时图像，该图像的宽高比 正好满足目标要求
			$inter_w=(int)($new_width / $ratio);
			$inter_h=(int) ($new_height / $ratio);
			$inter_img=imagecreatetruecolor($inter_w , $inter_h);
			imagecopy($inter_img, $src_img, 0,0,0,0,$inter_w,$inter_h);
			// 生成一个以最大边长度为大小的是目标图像$ratio比例的临时图像
			// 定义一个新的图像
			$new_img=imagecreatetruecolor($new_width,$new_height);
			imagecopyresampled($new_img,$inter_img,0,0,0,0,$new_width,$new_height,$inter_w,$inter_h);
			switch($type) {
				case IMAGETYPE_JPEG :
					imagejpeg($new_img, $dst_file,100); // 存储图像
					return 1;
					break;
				case IMAGETYPE_PNG :
					imagepng($new_img,$dst_file,9);
					return 1;
					break;
				case IMAGETYPE_GIF :
					imagegif($new_img,$dst_file,100);
					return 1;
					break;
				default:
					return 0;
					break;
			}
		} // end if 1
		
		// 2 目标图像 的一个边大于原图，一个边小于原图 ，先放大平普图像，然后裁剪
		// =if( ($ratio_w < 1 && $ratio_h > 1) || ($ratio_w >1 && $ratio_h <1) )
		else{
			$ratio=$ratio_h>$ratio_w? $ratio_h : $ratio_w; //取比例大的那个值
			// 定义一个中间的大图像，该图像的高或宽和目标图像相等，然后对原图放大
			$inter_w=(int)($w * $ratio);
			$inter_h=(int) ($h * $ratio);
			$inter_img=imagecreatetruecolor($inter_w , $inter_h);
			//将原图缩放比例后裁剪
			imagecopyresampled($inter_img,$src_img,0,0,0,0,$inter_w,$inter_h,$w,$h);
			// 定义一个新的图像
			$new_img=imagecreatetruecolor($new_width,$new_height);
			imagecopy($new_img, $inter_img, 0,0,0,0,$new_width,$new_height);
			switch($type) {
				case IMAGETYPE_JPEG :
					imagejpeg($new_img, $dst_file,100); // 存储图像
					return 1;
					break;
				case IMAGETYPE_PNG :
					imagepng($new_img,$dst_file,9);
					return 1;
					break;
				case IMAGETYPE_GIF :
					imagegif($new_img,$dst_file,100);
					return 1;
					break;
				default:
					return 0;
					break;
			}
		}// if3
	}// end function my_image_resize
	
	/**
	* 处理路径的字符
	*/
	function handleImgUrlString($imglink,$imgurl)
	{
		$newimgarr=str_replace($imglink,"",$imgurl);
		$newimgarr=ltrim($newimgarr,",");
		$newimgarr=rtrim($newimgarr,",");
		$newimgarr=str_replace(",,",",",$newimgarr);
		
		return $newimgarr;
	}
	
	function resize_image($filename, $tmpname, $xmax, $ymax)
	{	
	    $ext = explode(".", $filename);
	    $ext = $ext[count($ext)-1];

	    if($ext == "jpg" || $ext == "jpeg")
	        $im = imagecreatefromjpeg($tmpname);
	    else if($ext == "png")
	        $im = imagecreatefrompng($tmpname);
	    else if($ext == "gif")
	        $im = imagecreatefromgif($tmpname);
	    $x = imagesx($im);
	    $y = imagesy($im);
	    $im2 = imagecreatetruecolor($xmax, $ymax);
	    imagecopyresized($im2, $im, 0, 0, 0, 0, $xmax, $ymax, $x, $y);
	    return $im2;
	}

	
	
}