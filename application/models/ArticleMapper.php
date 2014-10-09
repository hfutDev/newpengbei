<?php
	/**
	* 文章数据库操作
	*/
	class Application_Model_ArticleMapper
	{
		function __construct()
		{
			$this->db = new Application_Model_DbTable_Article();
		}

		/**
		* 查找某一小栏目的所有文章
		*/ 
		public function findTopicAllArticle($type,$order)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('type=?',$type);

			$res = $this->db->fetchAll($where,$order)->toArray();
			return $res;
		}
		
		/**
		* 点击量+1
		*/
		public function clickArticle($ID)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('ID=?',$ID);
			
			$res = $this->db->fetchAll($where)->toArray();
			$click = $res[0]['RealClick'] + 1;
			$set = array('RealClick'=>$click);
			$resback = $this->db->update($set,$where);
			return $resback;
		}
		
		/**
		* 顶
		*/
		public function goodArticle($id)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('id=?',$id);
			
			$res = $this->db->fetchAll($where)->toArray();
			$good = $res[0]['good']+1;
			$set = array('good'=>$good);
			$resback = $this->db->update($set,$where);
			return $resback;
		}
		
		/**
		* 分页查找文章信息
		*/
		public function findPageArticle($type,$currentPage,$orderBy)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('type=?',$type)
				   . $ab->quoteInto(' And top=?',0);
			$where_top = $ab->quoteInto('type=?',$type)
					    .$ab->quoteInto(' And top=?',1);
			if($orderBy==1){
				$order = 'click DESC';
			} elseif($orderBy==0) {
				$order = 'WriteTime DESC';
			} else {
				$order = 'title ASC';
			}
			$backArr = array();
			if($currentPage==1){
				$count_top = TOP;
				$order_top = 'WriteTime DESC';
				$c = time();
				$res_top = $this->db->fetchAll($where_top,$order_top,$count_top)->toArray();
				$res_top_num = count($res_top);
				$count=10-$res_top_num;
				$res = $this->db->fetchAll($where,$order,$count,$offset=null)->toArray();
				$res_num = count($res);
				for($i=0;$i<$res_top_num;$i++){
					$backArr[$i]['id'] = $res_top[$i]['id'];
					$backArr[$i]['title'] = $res_top[$i]['title'];
					$backArr[$i]['time'] = $res_top[$i]['time'];
					$backArr[$i]['top'] = $res_top[$i]['top'];
				}
				
				for($j=0;$j<$res_num;$j++){
					$backArr[$res_top_num+$j]['id'] = $res[$j]['id'];
					$backArr[$res_top_num+$j]['title'] = $res[$j]['title'];
					$backArr[$res_top_num+$j]['time'] = $res[$j]['time'];
					$backArr[$res_top_num+$j]['top'] = $res[$j]['top'];
				}
			} else {
				$one_offset = count($this->db->fetchAll($where_top,$order_top,$count_top)->toArray());
				$count = 10;
				$offset = $count*($currentPage-1)-$one_offset;
				$res = $this->db->fetchAll($where,$order,$count,$offset)->toArray();
				
				for($i=0;$i<count($res);$i++){
					$backArr[$i]['id'] = $res[$i]['id'];
					$backArr[$i]['title'] = $res[$i]['title'];
					$backArr[$i]['time'] = $res[$i]['time'];
					$backArr[$i]['top'] = $res[$i]['top'];
				}
			}
			
			return $backArr;
		}

		/**
		* 前台根据id查找文章以及上下文章
		*/ 
		public function findIdArticle($id)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('id=?',$id);

			$arr = $this->db->fetchAll($where)->toArray();


			$click=$arr[0]['click']+1;
			$trueclick=$arr[0]['trueclick']+1;
			$set=array('click'=>$click,
						'trueclick'=>$trueclick
						);
			$res=$this->db->update($set,$where);
			$type=$arr[0]['type'];
			$where=$ab->quoteInto('id<?',$id)
					.$ab->quoteInto('And type=?',$type);
			$order="id DESC";
			$arr_up=$this->db->fetchAll($where,$order,$count=1,$offset=NULL)->toArray();

			$where=$ab->quoteInto('id>?',$id)
					.$ab->quoteInto('And type=?',$type);
			$order="id ASC";
			$arr_next=$this->db->fetchAll($where,$order,$count=1,$offset=NULL)->toArray();

			$backInfo = array('arr_up'   => $arr_up,
							  'arr'      => $arr,
							  'arr_next' => $arr_next
						);

			return $backInfo;
		}
		
		/**
		* 栏目分页输出
		*/ 
		public function pageArticle($type,$currentpage,$endid)
		{
			$ab = $this->db->getAdapter();

			$where=$ab->quoteInto('type = ?',$type);
			$order="WriteTime DESC";
			$offset = 10*($currentpage-1);

			$arr=$this->db->fetchAll($where,$order,$count=10,$offset)->toArray();
			
			$total=count($this->db->fetchAll($where,$order)->toArray());
			$arr[0]['total']=$total;
			
			if($endid==-1){
				$arr[0]['nump']=1;
			} else {
				$arr[0]['nump']=0;
			}

			return $arr;  
		}
		
		/**
		* 查找文章信息 用于列表
		*/
		public function findArticleForList($Type,$DeptID,$Column,$UserID)
		{
			$ab = $this->db->getAdapter();
			$select = $ab->select();
			
			if ($Type == 'all')		$where = 'Published < 10';	//其实此处应为null，但考虑下面有And条件，故设定一个无用的判断(所有文章都符合)
			if ($Type == 'wait')	$where = 'Published = 0';
			if ($Type == 'reject')	$where = 'Published = -1';
			if ($Type == 'publish')	$where = 'Published > 0';
			if ($DeptID != -1)
				$where .= ' And DeptID='.$DeptID;
			// if ($Column != -1)
			if(!is_numeric($Column)){
				switch ($Column) {
					case 'xw': $where .= ' And ColumnID in (1,2,3)'; break;
					case 'zx': $where .= ' And ColumnID in (4,5,6,7,8)'; break;
					case '1': $where .= ' And ColumnID in (1,2,3,4,5,6,7,8)'; break;
				}
			}
			else if ($Column != -1)
				$where .= ' And ColumnID='.$Column;
			if ($UserID != -1)
				$where .= ' And WriterID='.$UserID;

			// $order = "WriteTime DESC";
			$select->from('article', array('ID','ColumnID','DeptID','Title','PublishTime'))
			->where($where)
			->order('WriteTime DESC');
			// $sql = $select->__toString();
			$result = $ab->fetchAll($select);  

			return $result;
		}
		
		public function findArticleForListAll($Type,$DeptID,$Column,$UserID){
			$ab = $this->db->getAdapter();
			$select = $ab->select();
			
			if ($Type == 'all')		$where = 'Published < 10';	//其实此处应为null，但考虑下面有And条件，故设定一个无用的判断(所有文章都符合)
			if ($Type == 'wait')	$where = 'Published = 0';
			if ($Type == 'reject')	$where = 'Published = -1';
			if ($Type == 'publish')	$where = 'Published > 0';
			if ($DeptID != -1)
				$where .= ' And DeptID='.$DeptID;
			// if ($Column != -1)
			if(!is_numeric($Column)){
				switch ($Column) {
					case 'xw': $where .= ' And ColumnID in (1,2,3)'; break;
					case 'zx': $where .= ' And ColumnID in (4,5,6,7,8)'; break;
					case '1': $where .= ' And ColumnID in (1,2,3,4,5,6,7,8)'; break;
				}
			}
			else if ($Column != -1)
				$where .= ' And ColumnID='.$Column;
			if ($UserID != -1)
				$where .= ' And WriterID='.$UserID;

			// $order = "WriteTime DESC";
			$select->from('article', '*')
			->where($where)
			->order('WriteTime DESC');
			// $sql = $select->__toString();
			$result = $ab->fetchAll($select);  

			return $result;
		}

		/**
		* 查找文章 用于列表
		*/
		public function findArticleByStampForList($Type,$DeptID,$Column,$UserID,$BeginStamp,$EndStamp)
		{
			$ab = $this->db->getAdapter();
			if ($Type == 'all')		$where = $ab->quoteInto('Published < ?',10);	//其实此处应为null，但考虑下面有And条件，故设定一个无用的判断(所有文章都符合)
			if ($Type == 'wait')	$where = $ab->quoteInto('Published = ?',0);
			if ($Type == 'reject')	$where = $ab->quoteInto('Published = ?',-1);
			if ($Type == 'publish')	$where = $ab->quoteInto('Published > ?',0);
			if ($DeptID != -1)
				$where .= $ab->quoteInto(' And DeptID=?',$DeptID);
			if ($Column != -1)
				$where .= $ab->quoteInto(' And ColumnID=?',$Column);
			if ($UserID != -1)
				$where .= $ab->quoteInto(' And WriterID=?',$UserID);
			if ($BeginStamp != -1)
				$where .= $ab->quoteInto(' And WriteTime>=?',$BeginStamp);
			if ($EndStamp != -1)
				$where .= $ab->quoteInto(' And WriteTime<=?',$EndStamp);

			$order = "WriteTime DESC";
			$rows=$this->db->fetchAll($where,$order)->toArray();

			return $rows;
		}
		
		/**
		* 按时间统计发帖量
		*/
		public function countArticleByStamp($BeginStamp,$EndStamp,$Published)
		{
			$ab = $this->db->getAdapter();
			$where  = $ab->quoteInto('WriteTime>=?',$BeginStamp);
			$where .= $ab->quoteInto(' And WriteTime<=?',$EndStamp);
			if ($Published)
				$where .= $ab->quoteInto(' And Published>=?',0);
			else
				$where .= $ab->quoteInto(' And Published<=?',0);

			$order = "DeptID ASC";
			$rows = $this->db->fetchAll($where,$order)->toArray();

			// 统计各院数量
			for ($i=0; $i<20; $i++) $res[$i] = 0;
			for ($i=0; $i<count($rows); $i++) {
				$DeptID = $rows[$i]['DeptID'];
				$res[$DeptID]++;
			}

			$res['count'] = count($rows);

			return $res;
		}
		
		/**
		* 插入文章内容
		*/
		public function writeArticle($ID,$Title,$Author,$DeptID,$Column,$TopAtDept,$TopAtAll,$WriteTime,$PublishTime,$Article,$FakeClick,$OriginalWriterID)
		{
			if ((!empty($ID))&&(!empty($OriginalWriterID)))
				$WriterID = $OriginalWriterID;
			else
				$WriterID = $_SESSION['user']['UserID'];

			if ($_SESSION['user']['Type'] != 3){
				if ((!empty($ID))&&($WriterID!=$_SESSION['user']['UserID']))
					$Published = 2;//修正状态
				else
					$Published = 1;//通过状态
				$CheckerID = $_SESSION['user']['UserID'];
			} else {
				$Published = 0;
				$CheckerID = 0;//ID0不存在，代表未审核
			}
			
			$imgurlCheck = new Application_Model_Admin_Admin();
			$imgurl=$imgurlCheck->GetImageSrc($Article);
			$imgurl=implode(",", $imgurl);

			$arr = array('Title'		=> $Title, 
						 'ColumnID'		=> $Column,
						 'DeptID'		=> $DeptID,
						 'Article'		=> $Article,
						 'ImgUrl'		=> $imgurl,
						 'Author'		=> $Author,
						 'WriterID'		=> $WriterID,
						 'CheckerID'	=> $CheckerID,
						 'WriteTime'	=> $WriteTime,
						 'PublishTime'	=> $PublishTime,
						 'Published'	=> $Published,
						 'FakeClick'	=> $FakeClick,
						 'TopAtDept'	=> $TopAtDept,
						 'TopAtAll'		=> $TopAtAll,
						);

			if (!empty($ID)) {
				$ab=$this->db->getAdapter();
				$where=$ab->quoteInto('ID=?',$ID);
				$res=$this->db->update($set=$arr,$where);
			} else {
				$res=$this->db->insert($arr);
			}
			// $this->checkTopArticleNum($type);
			
			return $res;
		}

		
		/**
		* 判断文章置顶数目是否超过规定值
		* 如果超过了，则将最近发布的置顶帖自动转为不置顶
		*/ 
		public function checkTopArticleNum($type)
		{
			$ab=$this->db->getAdapter();
			$where=$ab->quoteInto('type=?',$type)
				  .$ab->quoteInto('And top=?','1');
			$res=$this->db->fetchAll($where,$order='WriteTime DESC');
			$res_num=count($res);
			if($res_num>TOP){
				$top_id = $res[$res_num-1]['id'];
				//var_dump($top_id);
				$where=$ab->quoteInto('id=?',$top_id);
				$set=array('top'=>0);
				$back=$this->db->update($set,$where);
			}
		}
		
		/**
		* 根据id查找文章
		*/ 
		public function findArticleById($ID)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('ID=?',$ID);

			$arr = $this->db->fetchAll($where)->toArray();
			
			return $arr;
		}
		
		/**
		* 审核文章
		*/
		public function checkArticle($ID)
		{
			$ab = $this->db->getAdapter();
			$where = $ab->quoteInto('ID=?',$ID);
			
			$set=array('Published'=>1);

			$res = $this->db->update($set,$where);;
			
			return $res;
		}
		
		/**
		* 退回稿件
		*/
		public function rejectArticle($ID,$Reason)
		{
			$arr = array('Published'	=> -1,
						 'RejectReason'	=> $Reason,);

			$ab=$this->db->getAdapter();
			$where=$ab->quoteInto('ID=?',$ID);
			$res=$this->db->update($set=$arr,$where);

			$res = $this->db->update($set,$where);;
			
			return $res;
		}
		
		/**
		* 删除文章
		*/
		public function deleteArticle($ID)
		{
			if (!is_array($ID))
				$ID=array($ID);

			$ab=$this->db->getAdapter();

			foreach ($ID as $ID) {
				$where=$ab->quoteInto('ID=?',$ID);
				$arr=$this->db->fetchAll($where)->toArray();

				//如果存在图片，则提取图片的存储路径。删除图片
				if ($arr[0]['ImgUrl']!='') {
					$weburl=WEBURL;
					$weburlLength=WEBURLLENGTH;
					$array=explode(',', $arr[0]['ImgUrl']);
					foreach ($array as $ImgUrl) {
						//$ImgUrl=ltrim($ImgUrl,$weburl);
						$ImgUrl=substr($ImgUrl,$weburlLength);
						unlink($ImgUrl);
					}
				} 

				//删除完图片文件以后，进行文章内容的删除
				$del=$this->db->delete($where);
				if ($del!='') {
					$info="文章已成功删除！";
				}  else {
					$info="删除文章失败，请重新删除！";
				}
			}
			
			return $info;
		}

	}
