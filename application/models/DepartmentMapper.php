<?php
	class Application_Model_DepartmentMapper
	{
		function __construct()
		{
			$this->db = new Application_Model_DbTable_Department();
		}

		/* 根据学院ID查找相关信息 */
		public function findDept($DeptID)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('DeptID = ?', $DeptID);
        	$arr = $this->db->fetchAll($where)->toArray();

        	return $arr;
		}

		/* 根据学院Code查找相关信息 */
		public function findDeptByCode($DeptCode)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('DeptCode = ?', $DeptCode);
        	$arr = $this->db->fetchAll($where)->toArray();

        	return $arr;
		}
		
		/* 查找所有学院信息 */
		public function findAllDept()
		{
			$arr = $this->db->fetchAll()->toArray();
			
			return $arr;
		}
		
	}