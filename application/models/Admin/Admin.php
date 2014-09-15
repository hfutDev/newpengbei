<?php
class Application_Model_Admin_Admin
{
	/**
	����ͳ�������ַ������ȵĺ���
	@param $str Ҫ���㳤�ȵ��ַ���
	@param $type ���㳤�����ͣ�0(Ĭ��)��ʾһ��������һ���ַ���1��ʾһ�������������ַ�
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
		utf-8�����½�ȡ�����ַ���,�������Բ���substr����
		@param $str Ҫ���н�ȡ���ַ���
		@param $start Ҫ���н�ȡ�Ŀ�ʼλ�ã�����Ϊ�����ȡ
		@param $end Ҫ���н�ȡ�ĳ���
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
	//����������ʽ�������ڻ�ȡͼƬ��·��
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
	* function my_image_resize ����ͼƬ����ͼ
	* $src_file : ��Ҫ����ͼƬ���ļ���  
	* $dst_file : ������ͼƬ�ı����ļ���
	* $new_width: ������ͼƬ�Ŀ� 
	* $new_height:������ͼƬ�ĸ�    
	*/
	function my_image_resize($src_file, $dst_file , $new_width , $new_height) 
	{
		if($new_width <1 || $new_height <1) {
			echo "ͼƬ�������ô���!";
			exit();
		}
		
		if(!file_exists($src_file)) {
			echo $src_file . " ͼƬ������!";
			exit();
		}
		
		// ͼ������
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
			echo "ͼƬ��ʽ��֧�֣�ͼƬ��ʽ��֧�� jpg , gif , png";
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
				echo "����ͼƬʧ��!";
			exit();
		}
		
		//��ȡͼƬ�Ŀ���
		$w=imagesx($src_img);
		$h=imagesy($src_img);
		$ratio_w=1.0 * $new_width / $w;
		$ratio_h=1.0 * $new_height / $h;
		$ratio=1.0;
		
		// ���ɵ�ͼ��ĸ߿��ԭ���Ķ�С���򶼴� ��ԭ����ȡ������Ŵ�ȡ�������С����С�ı����ͱȽ�С�ˣ�
		if( ($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)) {
			if($ratio_w < $ratio_h) {
				$ratio = $ratio_h ; // ���һ����ȵı����ȸ߶ȷ����С�����ո߶ȵı�����׼���ü���Ŵ�
			}else {
				$ratio = $ratio_w ;
			}
			
			// ����һ���м����ʱͼ�񣬸�ͼ��Ŀ�߱� ��������Ŀ��Ҫ��
			$inter_w=(int)($new_width / $ratio);
			$inter_h=(int) ($new_height / $ratio);
			$inter_img=imagecreatetruecolor($inter_w , $inter_h);
			imagecopy($inter_img, $src_img, 0,0,0,0,$inter_w,$inter_h);
			// ����һ�������߳���Ϊ��С����Ŀ��ͼ��$ratio��������ʱͼ��
			// ����һ���µ�ͼ��
			$new_img=imagecreatetruecolor($new_width,$new_height);
			imagecopyresampled($new_img,$inter_img,0,0,0,0,$new_width,$new_height,$inter_w,$inter_h);
			switch($type) {
				case IMAGETYPE_JPEG :
					imagejpeg($new_img, $dst_file,100); // �洢ͼ��
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
		
		// 2 Ŀ��ͼ�� ��һ���ߴ���ԭͼ��һ����С��ԭͼ ���ȷŴ�ƽ��ͼ��Ȼ��ü�
		// =if( ($ratio_w < 1 && $ratio_h > 1) || ($ratio_w >1 && $ratio_h <1) )
		else{
			$ratio=$ratio_h>$ratio_w? $ratio_h : $ratio_w; //ȡ��������Ǹ�ֵ
			// ����һ���м�Ĵ�ͼ�񣬸�ͼ��ĸ߻���Ŀ��ͼ����ȣ�Ȼ���ԭͼ�Ŵ�
			$inter_w=(int)($w * $ratio);
			$inter_h=(int) ($h * $ratio);
			$inter_img=imagecreatetruecolor($inter_w , $inter_h);
			//��ԭͼ���ű�����ü�
			imagecopyresampled($inter_img,$src_img,0,0,0,0,$inter_w,$inter_h,$w,$h);
			// ����һ���µ�ͼ��
			$new_img=imagecreatetruecolor($new_width,$new_height);
			imagecopy($new_img, $inter_img, 0,0,0,0,$new_width,$new_height);
			switch($type) {
				case IMAGETYPE_JPEG :
					imagejpeg($new_img, $dst_file,100); // �洢ͼ��
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
	* ����·�����ַ�
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