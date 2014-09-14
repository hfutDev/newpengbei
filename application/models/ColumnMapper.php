<?php
	class Application_Model_ColumnMapper
	{
		function __construct()
		{
			$this->db = new Application_Model_DbTable_Column();
		}

		/* 根据栏目ID查找相关信息 */
		public function findColumn($ColumnID)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('ColumnID = ?', $ColumnID);
        	$arr = $this->db->fetchAll($where)->toArray();

        	return $arr;
		}

		/* 根据栏目Code查找相关信息 */
		public function findColumnByCode($ColumnCode)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('ColumnCode = ?', $ColumnCode);
        	$arr = $this->db->fetchAll($where)->toArray();

        	return $arr;
		}
		
		/* 查找所有栏目信息 */
		public function findAllColumn()
		{
			$arr = $this->db->fetchAll()->toArray();
			
			return $arr;
		}
		
	}