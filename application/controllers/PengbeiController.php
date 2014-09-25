<?php

class PengbeiController extends Zend_Controller_Action
{

	public function init()
	{
		$this->_helper->layout->setLayout('pengbei');
	}

	public function indexAction()
	{

		$this->view->PageType = "index";

		$ArticleMapper = new Application_Model_ArticleMapper();
		$arrListrdxw = $ArticleMapper->findArticleForList('publish',-1,1,-1);
		$arrListbbfc = $ArticleMapper->findArticleForList('publish',-1,2,-1);
		$arrListhdkj = $ArticleMapper->findArticleForList('publish',-1,3,-1);
		$arrListczzl = $ArticleMapper->findArticleForList('publish',-1,4,-1);
		$arrListcysh = $ArticleMapper->findArticleForList('publish',-1,5,-1);
		$arrListcgxy = $ArticleMapper->findArticleForList('publish',-1,6,-1);

		$this->view->arrListrdxw = $arrListrdxw;
		$this->view->arrListbbfc = $arrListbbfc;
		$this->view->arrListhdkj = $arrListhdkj;
		$this->view->arrListczzl = $arrListczzl;
		$this->view->arrListcysh = $arrListcysh;
		$this->view->arrListcgxy = $arrListcgxy;
	}

	public function indexdataAction(){
		$this->_helper->layout()->disableLayout();
		$this->view->PageType = "index";

		$ArticleMapper = new Application_Model_ArticleMapper();
		$arrListyw = $ArticleMapper->findArticleForList('publish',-1,1,-1);
		$arrListxy = $ArticleMapper->findArticleForList('publish',-1,2,-1);
		$arrListtx = $ArticleMapper->findArticleForList('publish',-1,3,-1);
		$arrListtz = $ArticleMapper->findArticleForList('publish',-1,4,-1);
		$arrListxs = $ArticleMapper->findArticleForList('publish',-1,5,-1);
		$arrListjy = $ArticleMapper->findArticleForList('publish',-1,6,-1);
		$arrListky = $ArticleMapper->findArticleForList('publish',-1,7,-1);
		$arrListqg = $ArticleMapper->findArticleForList('publish',-1,8,-1);

		$arrListAjax = array(
		'yw' =>	$arrListyw,
		'xy' =>	$arrListxy,
		'tx' =>	$arrListtx,
		'tz' =>	$arrListtz,
		'xs' =>	$arrListxs,
		'jy' =>	$arrListjy,
		'ky' =>	$arrListky,
		'qg' =>	$arrListqg
		);
		echo json_encode($arrListAjax);
	}
	public function deptAction()
	{
		$this->view->PageType = "dept";
		$DeptCode = $this->_request->getParam("id");

		if (!empty($DeptCode)) {
			$DeptMapper = new Application_Model_DepartmentMapper();
			$arr = $DeptMapper->findDeptByCode($DeptCode);
			$this->view->arrDept = $arr;
			$DeptID = $arr[0]['DeptID'];

			$ArticleMapper = new Application_Model_ArticleMapper();
			$arrListrdxw = $ArticleMapper->findArticleForList('publish',$DeptID,1,-1);
			$arrListbbfc = $ArticleMapper->findArticleForList('publish',$DeptID,2,-1);
			$arrListhdkj = $ArticleMapper->findArticleForList('publish',$DeptID,3,-1);
			$arrListczzl = $ArticleMapper->findArticleForList('publish',$DeptID,4,-1);
			$arrListcysh = $ArticleMapper->findArticleForList('publish',$DeptID,5,-1);
			$arrListcgxy = $ArticleMapper->findArticleForList('publish',$DeptID,6,-1);

			$this->view->arrListrdxw = $arrListrdxw;
			$this->view->arrListbbfc = $arrListbbfc;
			$this->view->arrListhdkj = $arrListhdkj;
			$this->view->arrListczzl = $arrListczzl;
			$this->view->arrListcysh = $arrListcysh;
			$this->view->arrListcgxy = $arrListcgxy;
		} else {
			$this->_redirect('/pengbei');
			exit;
		}
	}

	public function listAction()
	{
		$this->view->PageType = "list";
		$DeptCode = $this->_request->getParam("dept");
		$ColumnCode = $this->_request->getParam("column");

		if (!empty($ColumnCode)) {
			$ColumnMapper = new Application_Model_ColumnMapper();
			$arr = $ColumnMapper->findColumnByCode($ColumnCode);
			$this->view->arrPageColumn = $arr;
			$arr = $ColumnMapper->findallColumn();
			$this->view->arrColumn = $arr;

			$DeptID = -1;
			if (!empty($DeptCode)) {
				$DeptMapper = new Application_Model_DepartmentMapper();
				$arr = $DeptMapper->findDeptByCode($DeptCode);
				$this->view->arrDept = $arr;
				$DeptID = $arr[0]['DeptID']?$arr[0]['DeptID']:-1;
			}

			$ArticleMapper = new Application_Model_ArticleMapper();
			$arrListrdxw = $ArticleMapper->findArticleForList('publish',$DeptID,1,-1);
			$arrListbbfc = $ArticleMapper->findArticleForList('publish',$DeptID,2,-1);
			$arrListhdkj = $ArticleMapper->findArticleForList('publish',$DeptID,3,-1);
			$arrListczzl = $ArticleMapper->findArticleForList('publish',$DeptID,4,-1);
			$arrListcysh = $ArticleMapper->findArticleForList('publish',$DeptID,5,-1);
			$arrListcgxy = $ArticleMapper->findArticleForList('publish',$DeptID,6,-1);

			$this->view->arrListrdxw = $arrListrdxw;
			$this->view->arrListbbfc = $arrListbbfc;
			$this->view->arrListhdkj = $arrListhdkj;
			$this->view->arrListczzl = $arrListczzl;
			$this->view->arrListcysh = $arrListcysh;
			$this->view->arrListcgxy = $arrListcgxy;

			switch ($ColumnCode) {
				case 'rdxw': $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrListrdxw)); break;
				case 'bbfc': $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrListbbfc)); break;
				case 'hdkj': $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrListhdkj)); break;
				case 'czzl': $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrListczzl)); break;
				case 'cysh': $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrListcysh)); break;
				case 'cgxy': $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrListcgxy)); break;
				// default : $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($arrListrdxw)); break;
			}
			$num=10; $page=1; //设置每一页显示的文章数目 //设置第一页显示
			$paginator->setItemCountPerPage($num); //设置每一页显示的文章数目
			$paginator->setCurrentPageNumber($page); //设置第一页显示
			$paginator->setCurrentPageNumber($this->_getParam('page')); //从url获取需要显示的页码
			$this->view->paginator = $paginator;
		} else {
			$this->_redirect('/pengbei');
			exit;
	
		}
	}

	public function articleAction()
	{
		$ID = $this->_request->getParam("id");

		if (!empty($ID)){
			$ArticleMapper = new Application_Model_ArticleMapper();
			$arrArticle = $ArticleMapper->findArticleById($ID);
			session_start();//开启session并在下面进行判断，以便在前台不显示未审核的文章
			if (($arrArticle[0]['Published']>=1)||(isset($_SESSION['user']['Type']))) {
				//点击量+1
				if (!isset($_SESSION['user']['Type'])) $ArticleMapper->clickArticle($ID);

				$DeptMapper = new Application_Model_DepartmentMapper();
				$arr = $DeptMapper->findDept($arrArticle[0]['DeptID']);
				$this->view->arrDept = $arr;

				$ColumnMapper = new Application_Model_ColumnMapper();
				$arr = $ColumnMapper->findColumn($arrArticle[0]['ColumnID']);
				$this->view->arrPageColumn = $arr;
				$arr = $ColumnMapper->findallColumn();
				$this->view->arrColumn = $arr;

				$this->view->arrArticle=$arrArticle;

				$DeptID = $arrArticle[0]['DeptID']?$arrArticle[0]['DeptID']:-1;

				$arrListrdxw = $ArticleMapper->findArticleForList('publish',$DeptID,1,-1);
				$arrListbbfc = $ArticleMapper->findArticleForList('publish',$DeptID,2,-1);
				$arrListhdkj = $ArticleMapper->findArticleForList('publish',$DeptID,3,-1);
				$arrListczzl = $ArticleMapper->findArticleForList('publish',$DeptID,4,-1);
				$arrListcysh = $ArticleMapper->findArticleForList('publish',$DeptID,5,-1);
				$arrListcgxy = $ArticleMapper->findArticleForList('publish',$DeptID,6,-1);

				$this->view->arrListrdxw = $arrListrdxw;
				$this->view->arrListbbfc = $arrListbbfc;
				$this->view->arrListhdkj = $arrListhdkj;
				$this->view->arrListczzl = $arrListczzl;
				$this->view->arrListcysh = $arrListcysh;
				$this->view->arrListcgxy = $arrListcgxy;
			} else {
				$this->_redirect('/pengbei',array('code'=>301));
				exit;
			}
		} else {
			$this->_redirect('/pengbei');
			exit;
		}
	}


}

