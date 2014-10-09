<?php
	class Application_Model_ImageinfoMapper{
		function __construct()
		{
			$this->db = new Application_Model_DbTable_Imageinfo();
		}
		/**
		* 插入上传图片的信息
		*
		*/
		public function uploadImageInfo($imageName, $imageOwner, $deptid){
			$arr = array('image_name'		=> $imageName, 
						 'image_date'		=> time(),
						 'image_owner'		=> $imageOwner,
						 'dept_id'			=> $deptid,
						);
			$res=$this->db->insert($arr);
			return $res;
		}
		/**
		* 查询某用户的图片
		*
		*/
		public function selectImageInfo($imageOwner){
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('image_owner=?',$imageOwner);
			$arr = $this->db->fetchAll($where)->toArray();
			return $arr;
		}

		/**
		* 删除选定的图片
		*
		*/
		public function delImageInfo($imageName){
			$ab=$this->db->getAdapter();
			$where=$ab->quoteInto('image_name=?',$imageName);
			$del=$this->db->delete($where);
			return $del;
		}
	}
?>