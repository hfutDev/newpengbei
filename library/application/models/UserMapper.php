<?php
	class Application_Model_UserMapper
	{
		function __construct()
		{
			$this->db = new Application_Model_DbTable_User();
		}

		/**
		* 检测用户是否存在
		*/
		public function checkUser($username,$password)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('UserName = ?', $username)
					.$ab->quoteInto('AND Password = ?', $password);
			$arr = $this->db->fetchAll($where)->toArray();

			return $arr;
		}

		/**
		* 更新用户上次登录时间
		*/
		public function updateLastLoginTime($id)
		{
			$arr = array('LastLoginTime'=>time(),);
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('UID = ?',$id);
			$res = $this->db->update($set=$arr,$where);

			return $res;
		}
		
		/**
		* 查找所有用户信息
		*/
		public function findAllUser()
		{
			$arr = $this->db->fetchAll()->toArray();
			
			return $arr;
		}
		
		/**
		* 查找able用户信息
		*/
		public function findAbleUser()
		{
			$ab = $this->db->getAdapter();
			$where=$ab->quoteInto('Activate = ?',1);
			$arr = $this->db->fetchAll($where)->toArray();
			
			return $arr;
		}
		
		/**
		* 查找disable用户信息
		*/
		public function findDisableUser()
		{
			$ab = $this->db->getAdapter();
			$where=$ab->quoteInto('Activate = ?',0);
			$arr = $this->db->fetchAll($where)->toArray();
			
			return $arr;
		}
		
		/**
		* 查找指定用户信息
		*/
		public function findUserByID($id)
		{
			$ab = $this->db->getAdapter();
			$where=$ab->quoteInto('UID = ?',$id);
			$arr = $this->db->fetchAll($where)->toArray();
			
			return $arr;
		}
		
		/**
		* 按用户名查找用户信息
		*/
		public function findUserByName($username)
		{
			$ab = $this->db->getAdapter();
			$where=$ab->quoteInto('UserName = ?',$username);
			$arr = $this->db->fetchAll($where)->toArray();
			
			return $arr;
		}
		
		/**
		* 将新用户信息插入数据库
		*/
		public function addUser($UserName,$RealName,$Password,$DeptID,$Type)
		{
			$Activate = 1;
			$LastLoginTime = 0;
			$arr = array('UserName'		=>$UserName,
						 'RealName'		=>$RealName,
						 'Password'		=>md5($Password),
						 'Department'	=>$DeptID,
						 'Type'			=>$Type,
						 'Activate'		=>$Activate,
						 'LastLoginTime'=>$LastLoginTime,
						);
			$res = $this->db->insert($arr);
			
			return $res;
		}
		
		/**
		* 修改用户信息
		*/
		public function editUser($UID,$UserName,$RealName,$DeptID,$Type)
		{
			$arr = array('UserName'		=>$UserName,
						 'RealName'		=>$RealName,
						 'Department'	=>$DeptID,
						 'Type'			=>$Type,
						);
			$ab=$this->db->getAdapter();
			$where=$ab->quoteInto('UID=?',$UID);
			$res=$this->db->update($set=$arr,$where);

			return $res;
		}
		
		/**
		* 修改用户密码
		*/
		public function modifyUserPwd($UID,$Password)
		{
			$arr = array('Password'=>md5($Password),);
			$ab=$this->db->getAdapter();
			$where=$ab->quoteInto('UID=?',$UID);
			$res=$this->db->update($set=$arr,$where);

			return $res;
		}
		
		/**
		* 启用用户
		*/
		public function ableUser($id)
		{
			$arr = array('Activate'=>1);
			$ab = $this->db->getAdapter();
			$where=$ab->quoteInto('UID=?',$id);
			$res=$this->db->update($set=$arr,$where);
			
			return $res;
		}
		
		/**
		* 停用用户
		*/
		public function disableUser($id)
		{
			$arr = array('Activate'=>0);
			$ab = $this->db->getAdapter();
			$where=$ab->quoteInto('UID=?',$id);
			$res=$this->db->update($set=$arr,$where);
			
			return $res;
		}
	}