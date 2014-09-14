<?php
	/**
	* 添加水印
	* $water/Pos 水印的位置(0-9)
	* $imgPath 待加水印的图片的路径
	* $waterPath 水印图片的路径
	* 如何要添加文字水印，只支持png格式的图片
	*/
	class Application_Model_Admin_AddWaterPic
	{
		function __construct($waterPos,$imgPath,$waterPath,$yes,$str)
		{
			$this->waterPos = $waterPos;
			$this->imgPath = $imgPath;
			$this->waterPath = $waterPath;
			$this->yes = $yes;
			$this->str = $str;
		}
		
		//判断是否存在图片和水印图
		public function exitPic($imgPath)
		{
			if (!empty($imgPath) && (file_exists($imgPath))) {
				$imgPathInfo = getimagesize($imgPath);

				switch ($imgPathInfo['mime']) {
					case 'image/gif':$img_id = imagecreatefromgif($imgPath);break;
					case 'image/jpeg':$img_id = imagecreatefromjpeg($imgPath);break;
					case 'image/png':$img_id = imagecreatefrompng($imgPath);break;
					default:;break;
				}

				$imgPathInfo['img_id'] = $img_id;
			}
			return($imgPathInfo);
		}

		//加水印的位置
		public function addPosition($waterPos,$ground_w,$ground_h,$w,$h)
		{
			switch($waterPos) 
			{ 
				case 0://随机 
				$posX = rand(0,($ground_w - $w)); 
				$posY = rand(0,($ground_h - $h)); 
				break; 
				case 1://1为顶端居左 
				$posX = 0; 
				$posY = 0; 
				break; 
				case 2://2为顶端居中 
				$posX = ($ground_w - $w) / 2; 
				$posY = 0; 
				break; 
				case 3://3为顶端居右 
				$posX = $ground_w - $w; 
				$posY = 0; 
				break; 
				case 4://4为中部居左 
				$posX = 0; 
				$posY = ($ground_h - $h) / 2; 
				break; 
				case 5://5为中部居中 
				$posX = ($ground_w - $w) / 2; 
				$posY = ($ground_h - $h) / 2; 
				break; 
				case 6://6为中部居右 
				$posX = $ground_w - $w; 
				$posY = ($ground_h - $h) / 2; 
				break; 
				case 7://7为底端居左 
				$posX = 0; 
				$posY = $ground_h - $h; 
				break; 
				case 8://8为底端居中 
				$posX = ($ground_w - $w) / 2; 
				$posY = $ground_h - $h; 
				break; 
				case 9://9为底端居右 
				$posX = $ground_w - $w; 
				$posY = $ground_h - $h; 
				break; 
				default://随机 
				$posX = rand(0,($ground_w - $w)); 
				$posY = rand(0,($ground_h - $h)); 
				break; 
			} 
			$pos['X'] = $posX;
			$pos['Y'] = $posY;
			return $pos;
		}

		public function addWaterPhoto()
		{
			$water_info = $this->exitPic($this->waterPath);
			$w = $water_info[0];
			$h = $water_info[1];
			$water_im = $water_info['img_id'];

			$ground_info = $this->exitPic($this->imgPath);
			$ground_w = $ground_info[0];
			$ground_h = $ground_info[1];
			$ground_im = $ground_info['img_id'];

			$waterPos = $this->waterPos;

			$pos = $this->addPosition($waterPos,$ground_w,$ground_h,$w,$h);
			$posX = $pos['X'];
			$posY = $pos['Y'];

			//imagecopy($ground_im, $water_im, $posX, $posY-30, 0, 0, $w,$h); 

			if ($this->yes) {
				imagecopy($ground_im, $water_im, $posX, $posY-30, 0, 0, $w,$h); 
				$R = 254;
				$G = 254;
				$B = 254;
				$angle = 0;
				$fontsize = FONTSIZE;
				$fontfile = FONTPATH;
				$str = $this->str; 
				$color = ImageColorAllocate($ground_im, $R , $G , $B);
				ImageTTFText($ground_im,$fontsize,$angle,$posX+10,$posY+20,$color,$fontfile,$str);
			} else {
				imagecopy($ground_im, $water_im, $posX, $posY, 0, 0, $w,$h); 
			}

			@unlink($this->imgPath); 
			switch($ground_info['mime'])//取得背景图片的格式 
			{ 
				case 'image/gif':imagegif($ground_im,$this->imgPath);break; 
				case 'image/jpeg':imagejpeg($ground_im,$this->imgPath);break; 
				case 'image/png':imagepng($ground_im,$this->imgPath);break; 
				default:;break;
			}

			//释放内存 
			if(isset($water_info)) unset($water_info); 
			if(isset($water_im)) imagedestroy($water_im); 
			unset($ground_info); 
			imagedestroy($ground_im); 
		}

	}
