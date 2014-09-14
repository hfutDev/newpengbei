<?php
	/**
	* 最近公告数据库操作
	*/
	class Application_Model_AnnounceMapper
	{
		function __construct()
		{
			$this->db = new Application_Model_DbTable_Announce();
		}

		/**
		* 查询公告
		*/ 
		public function getAnnounceMapper($count)
		{
			$order="id DESC";
			$arr=$this->db->fetchAll($where=NULL,$order,$count,$offset=NULL)->toArray();
			
			return $arr;
		}
		
		/**
		* 插入公告
		*/ 
		public function insertAnnounce($nav,$act,$id)
		{
			$announce=stripslashes($nav);
			$arr=array(
						'nav'        => $announce,
						'editor'     => $_SESSION['user']['username'],
						'time'       => date('Y-m-d'),
						'xianxitime' => time()
					);
			if($act=='update'){
				$ab = $this->db->getAdapter();
				$where = $ab->quoteInto('id=?',$id);
				$res = $this->db->update($set=$arr,$where);
			} else {
				$res = $this->db->insert($arr);
			}
			
			return $res;
		}
		
		/**
		* 根据id查找公告
		*/
		public function findIdAnnounce($id)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('id=?',$id);
			
			$arr = $this->db->fetchAll($where);
			
			return $arr;
		}
		
		/**
		* 删除公告
		*/
		public function deleteAnnounce($getid)
		{
			if (!is_array($getid)) {
				$id = array('0'=>$getid);
			} else $id=$getid;
			
			$ab = $this->db->getAdapter();

			foreach ($id as $id) {
				$where = $ab->quoteInto('id=?',$id);
				$row = $this->db->delete($where);
				if($row==1){
					$info="删除成功！";
				} else {
					$info="删除失败，请重新删除！";
				}
			}
			
			return $info;
		}

	}