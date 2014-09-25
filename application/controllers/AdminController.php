<?php

class AdminController extends Zend_Controller_Action
{

	protected $_UserInfo;

	public function init()
	{
		$this->_helper->layout->setLayout('admin');

		$seesion = new Zend_Session_Namespace('user');
		if(!isset($_SESSION['user']['UserName'])){
		$this->_redirect('/login');
		}
	}

	public function indexAction()
	{
		$ArticleMapper = new Application_Model_ArticleMapper();
		if ($_SESSION['user']['DeptID']==0) {
			$AllPub = $ArticleMapper->countArticleByStamp(0,2147483647,1);
			$AllUnPub = $ArticleMapper->countArticleByStamp(0,2147483647,0);
			$res_UnRead = $ArticleMapper->findArticleByStampForList('all',-1,-1,-1,$_SESSION['user']['LastLoginTime'],-1);
			$res_Self = $ArticleMapper->findArticleForList('all',-1,-1,$_SESSION['user']['UserID']);
			$res_SelfReject = $ArticleMapper->findArticleForList('reject',-1,-1,$_SESSION['user']['UserID']);

			$this->view->All = $AllPub['count'] + $AllUnPub['count'];
			$this->view->UnRead = count($res_UnRead);
			$this->view->Self = count($res_Self);
			$this->view->res_Self = $res_Self;
			$this->view->SelfReject = count($res_SelfReject);
			
		} else {
			$NowOfWeek = date("w")?date("w"):7;
			$ThisWeekBeginStamp = strtotime(date('Y-m-d',time()-86400*($NowOfWeek-1)).' 00:00:00');
			$res_SelfThisWeek = $ArticleMapper->findArticleByStampForList('all',-1,-1,$_SESSION['user']['UserID'],$ThisWeekBeginStamp,-1);
			$res_Self = $ArticleMapper->findArticleForList('all',-1,-1,$_SESSION['user']['UserID']);
			$res_SelfReject = $ArticleMapper->findArticleForList('reject',-1,-1,$_SESSION['user']['UserID']);
			$res_Dept = $ArticleMapper->findArticleForList('all',$_SESSION['user']['DeptID'],-1,-1);
			$res_DeptWait = $ArticleMapper->findArticleForList('wait',$_SESSION['user']['DeptID'],-1,-1);
			$res_DeptReject = $ArticleMapper->findArticleForList('reject',$_SESSION['user']['DeptID'],-1,-1);

			$this->view->SelfThisWeek = count($res_SelfThisWeek);
			$this->view->Self = count($res_Self);
			$this->view->SelfReject = count($res_SelfReject);
			$this->view->Dept = count($res_Dept);
			$this->view->DeptWait = count($res_DeptWait);
			$this->view->DeptReject = count($res_DeptReject);

			$this->view->res_Self = $res_Self;
		}

		$DeptMapper = new Application_Model_DepartmentMapper();
		$arr = $DeptMapper->findAllDept();
		$this->view->arrDept = $arr;

		$ColumnMapper = new Application_Model_ColumnMapper();
		$arr = $ColumnMapper->findAllColumn();
		$this->view->arrColumn = $arr;
	}

	/* 文章列表页面 */
	public function listarticleAction()
	{
		//获取学院信息
		if ($_SESSION['user']['DeptID'] == 0) {
			$DeptCode=$this->_request->getParam("dept");
			$DeptMapper = new Application_Model_DepartmentMapper();
			$arrDept = $DeptMapper->findDeptByCode($DeptCode);
			if(!empty($arrDept)) $DeptID = $arrDept[0]['DeptID'];
			else $DeptID = -1;	//如果没有指定院系信息，默认查找所有学院文章（-1代表所有学院）
		} else {
			$DeptID = $_SESSION['user']['DeptID'];
		}

		//获取栏目信息
		$ColumnCode=$this->_request->getParam("column");
		$ColumnMapper = new Application_Model_ColumnMapper();
		$arrColumn = $ColumnMapper->findColumnByCode($ColumnCode);
		if(!empty($arrColumn)) $Column = $arrColumn[0]['ColumnID'];
		else $Column = -1; 		//如果没有指定栏目信息，默认查找所有栏目文章（-1代表所有栏目）

		//判断当前用户能获取文章的权限
		$Self = 'no';								//默认查找所有文章
		$Self = $this->_request->getParam("by");	//判断是否查找自己的文章
		if ($Self == 'self'){
			$UserID=$_SESSION['user']['UserID'];
		} else {
			$UserID=-1;			//如果没有指定用户信息，默认查找所有用户文章（-1代表所有用户）
		}

		//获取查找文章的时间区间
		$BeginStamp = $this->_request->getParam("from");
		$EndStamp   = $this->_request->getParam("until");
		if (empty($BeginStamp)) $BeginStamp = -1;
		if (empty($EndStamp))   $EndStamp   = -1;


		/* 根据获取到的信息从数据库中查找文章 */
		$ArticleMapper = new Application_Model_ArticleMapper();

		$num=20; $page=1; //设置每一页显示的文章数目 //设置第一页显示

		//查找全部文章
		$rows_all = $ArticleMapper->findArticleByStampForList('all',$DeptID,$Column,$UserID,$BeginStamp,$EndStamp);
		$paginator_all = new Zend_Paginator(new Zend_Paginator_Adapter_Array($rows_all)); //调用分页
		$paginator_all->setItemCountPerPage($num); //设置每一页显示的文章数目
		$paginator_all->setCurrentPageNumber($page); //设置第一页显示
		$paginator_all->setCurrentPageNumber($this->_getParam('page')); //从url获取需要显示的页码

		//查找待审核的文章
		$rows_wait = $ArticleMapper->findArticleByStampForList('wait',$DeptID,$Column,$UserID,$BeginStamp,$EndStamp);
		$paginator_wait = new Zend_Paginator(new Zend_Paginator_Adapter_Array($rows_wait)); //调用分页
		$paginator_wait->setItemCountPerPage($num); //设置每一页显示的文章数目
		$paginator_wait->setCurrentPageNumber($page); //设置第一页显示
		$paginator_wait->setCurrentPageNumber($this->_getParam('page')); //从url获取需要显示的页码

		//查找已退稿的文章
		$rows_reject = $ArticleMapper->findArticleByStampForList('reject',$DeptID,$Column,$UserID,$BeginStamp,$EndStamp);
		$paginator_reject = new Zend_Paginator(new Zend_Paginator_Adapter_Array($rows_reject)); //调用分页
		$paginator_reject->setItemCountPerPage($num); //设置每一页显示的文章数目
		$paginator_reject->setCurrentPageNumber($page); //设置第一页显示
		$paginator_reject->setCurrentPageNumber($this->_getParam('page')); //从url获取需要显示的页码

		//查找已审核或修正的文章
		$rows_publish = $ArticleMapper->findArticleByStampForList('publish',$DeptID,$Column,$UserID,$BeginStamp,$EndStamp);
		$paginator_publish = new Zend_Paginator(new Zend_Paginator_Adapter_Array($rows_publish)); //调用分页
		$paginator_publish->setItemCountPerPage($num); //设置每一页显示的文章数目
		$paginator_publish->setCurrentPageNumber($page); //设置第一页显示
		$paginator_publish->setCurrentPageNumber($this->_getParam('page')); //从url获取需要显示的页码


		/* 返回view的信息 */
		if (($_SESSION['user']['DeptID'] == 0)&&($_SESSION['user']['Type'] != 3)){
			$this->view->DeptCodeSelected  = $DeptCode;
			$AllDeptMapper = new Application_Model_DepartmentMapper();
			$arrAllDept = $AllDeptMapper->findAllDept();
			$this->view->arrAllDept = $arrAllDept;
		}

		$this->view->PublishType=$this->_request->getParam("type");
		if ($_SESSION['user']['Type'] == 1) {
			if ($DeptID != -1) {
				$this->view->ListTitle .= $arrDept[0]['DeptName'];
				$this->view->ListTitle .= " 的 ";
			}
		}

		$ColumnCode=$this->_request->getParam("column");
		$ColumnMapper = new Application_Model_ColumnMapper();
		$arrColumn = $ColumnMapper->findColumnByCode($ColumnCode);
		if(!empty($arrColumn)) $this->view->ListTitle .= $arrColumn[0]['ColumnName'];
		else $this->view->ListTitle .= "全部文章";

		$this->view->paginator_all = $paginator_all;
		$this->view->paginator_wait = $paginator_wait;
		$this->view->paginator_reject = $paginator_reject;
		$this->view->paginator_publish = $paginator_publish;

	}

	/* 文章发表页面 */
	public function writearticleAction()
	{
		/* 文章提交 */
		$type = $this->_request->getParam("type");
		$ID = $this->_request->getParam("id");
		if ($type == "post")
		{
			$Title=$this->getRequest()->getParam('title');
			$Author=$this->getRequest()->getParam('author');
			if (!empty($ID))
				$OriginalWriterID=$this->getRequest()->getParam('writerid');
			else
				$OriginalWriterID=null;
			//学院判断
			if (($_SESSION['user']['Type'] == 1)||($_SESSION['user']['DeptID'] == 0))
			{
				$DeptID=$this->getRequest()->getParam('deptid');
				if ($DeptID=="-1")
				{
					$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"学院选择错误！\\n请修改后重新发表。\");history.back();</script>";
					echo $string;
					exit;
				}
			} else {
				$DeptID=$_SESSION['user']['DeptID'];
			}
			//栏目判断
			$Column=$this->getRequest()->getParam('column');
			if ($Column=="0")
			{
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"栏目选择错误！\\n请修改后重新发表。\");history.back();</script>";
				echo $string;
				exit;
			}
			//置顶判断
			if ($_SESSION['user']['Type'] != 3)
			{
				if ($this->getRequest()->getParam('topatdept') == "on") $TopAtDept=1;
				else $TopAtDept=0;
				if (($_SESSION['user']['Type'] == 1)&&($this->getRequest()->getParam('topatall') == "on")) $TopAtAll=1;
				else $TopAtAll=0;
				if ((($_SESSION['user']['Type'] == 1)&&($DeptID == 0))&&(($TopAtAll)||($TopAtDept)))
				{ $TopAtAll=1; $TopAtDept=1; }
			} else {
				$TopAtDept=0;
				$TopAtAll=0;
			}
			//发帖时间
			if (!empty($ID))
				$WriteTime = $this->_request->getParam("writetime");
			else
				$WriteTime = time();
			//发布时间判断(前台显示)
			if ($_SESSION['user']['Type'] != 3)
			{
				$PublishTime=strtotime($this->getRequest()->getParam('publishtime'));//转换为时间戳格式
				if (empty($PublishTime)) $PublishTime=$WriteTime;
			} else $PublishTime=$WriteTime;
			//初始点击量
			$FakeClick=$this->getRequest()->getParam('fakeclick');
			if(preg_match("/^[0-9]*[1-9][0-9]*$/",$FakeClick)!=1) $FakeClick = rand(100,200);//判断是否正整数
			//文章内容
			$Article=stripslashes($this->getRequest()->getParam('article'));
			
			$strcheck = new Application_Model_Admin_Admin();
			$Title = $strcheck->utf8_substr($Title,0,40);
			$Author = $strcheck->utf8_substr($Author,0,20);
			
			$ArticleMapper = new Application_Model_ArticleMapper();
			$i = $ArticleMapper->writeArticle($ID,$Title,$Author,$DeptID,$Column,$TopAtDept,$TopAtAll,$WriteTime,$PublishTime,$Article,$FakeClick,$OriginalWriterID);

			if (!empty($ID))
				$TypeString = "修改";
			else
				$TypeString = "发表";

			if (!isset($i)) {
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"".$TypeString."文章失败\\n请修改后重新发表。\");history.back();</script>";
				echo $string;
				exit;
			} else {
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"成功".$TypeString."文章！\");location.href = \"/admin/listarticle\";</script>";
				echo $string;
				exit;
			}
		} else {
			$DeptMapper = new Application_Model_DepartmentMapper();
			$arr = $DeptMapper->findAllDept();
			$this->view->arrDept = $arr;

			$ColumnMapper = new Application_Model_ColumnMapper();
			$arr = $ColumnMapper->findAllColumn();
			$this->view->arrColumn = $arr;

			$time=date('Y-m-d H:i:s');
			$this->view->time = $time;
			$this->view->fakeclick = rand(100,200);
			$this->view->info = "添加文章";

		}
	}

	/* 上传图片页面 */
	public function imageuploadAction(){
		$ArticleMapper = new Application_Model_ImageinfoMapper();
		$method = $this->_request->getParam("method");
		if($method == "post"){
			$deptid = $this->_request->getParam("deptid");
			//实例化文件上传类 
			$upload = new Zend_File_Transfer();
			$upload->addValidator('Size', false, 5 * 1024 * 1024);
			$upload->addValidator('Extension', false, 'jpg,gif,png');

			if (!$upload->isValid()) {
			    echo "<script>alert('格式不符或文件过大，请重新尝试');location.href = '/admin/imageupload';</script>";
				exit();
			}
			//获取上传的文件表单，可以有多项
			$fileInfo = $upload->getFileInfo();
			$parseImg = new Application_Model_Admin_Admin();
			$filetmp = $parseImg->resize_image($fileInfo['imageFile']['name'], $fileInfo['imageFile']['tmp_name'], '480', '280');
			imagedestroy($fileInfo['imageFile']['tmp_name']);
			//获取后缀名，这里imageFile为上传表单file控件的name
			$ext = explode(".",$fileInfo['imageFile']['name'])[1];
			//定义生成目录
			$dir = './upload' . date('/Y/m/');
			//文件重新命名
			do {
			    $filename = date('His') . rand(100000, 999999) . '.' . $ext;
			} while (file_exists($dir . $filename));
			 
			//如果目录不存在则创建目录
			if (!file_exists($dir)) {
				mkdir($dir, 0, true);
			}
			//将图片信息插入数据库
			
			$i = $ArticleMapper->uploadImageInfo($filename, $_SESSION['user']['RealName'], $deptid);
			if(!isset($i)){
				echo "<script>alert('图片信息上传失败，请重新尝试');location.href = '/admin/imageupload';</script>";
				exit();
			}
			//将图片正式写入
			imagejpeg($filetmp,$dir.'/'.$filename,100);
			imagedestroy($filetmp);
			
			echo "<script>alert('上传成功！');location.href = '/admin/imageupload';</script>";
		} else {
			//加载列表
			$DeptMapper = new Application_Model_DepartmentMapper();
			$arr = $DeptMapper->findAllDept();
			$this->view->arrDept = $arr;
			$this->view->imageArr = $ArticleMapper->selectImageInfo($_SESSION['user']['RealName']);
		}

	}

	public function imageajaxAction(){
		$method = $this->_request->getParam("method");
		if($method == "del"){
			$this->_helper->layout()->disableLayout();
			$imageName = $this->_request->getParam("name");
			$time = $this->_request->getParam("time");
			$imageUrl = $dir = './upload'.$time.$imageName;
			$ImageinfoMapper = new Application_Model_ImageinfoMapper();
			$i = $ImageinfoMapper->delImageInfo($imageName);
			if(!isset($i)){
				echo "<script>alert('图片信息删除失败，请重新尝试');location.href = '/admin/imageupload';</script>";
				exit();
			}
			echo unlink($imageUrl);
		}
	}

	/* 文章编辑修正页面 */
	public function editarticleAction()
	{
		$ID = $this->_request->getParam("id");

		if (!empty($ID)){
			$ArticleMapper = new Application_Model_ArticleMapper();
			$arrArticle = $ArticleMapper->findArticleById($ID);
			if (!empty($arrArticle)) {
				$DeptMapper = new Application_Model_DepartmentMapper();
				$arr = $DeptMapper->findAllDept();
				$this->view->arrDept = $arr;

				$ColumnMapper = new Application_Model_ColumnMapper();
				$arr = $ColumnMapper->findAllColumn();
				$this->view->arrColumn = $arr;

				$this->view->arrArticle=$arrArticle;
			} else {
				$string = "<script language=\"JavaScript\">history.back();</script>";
				echo $string;
				exit;
			}
		} else {
			$string = "<script language=\"JavaScript\">history.back();</script>";
			echo $string;
			exit;
		}
	}
	
	/**
	* 审核文章
	*/
	public function checkarticleAction()
	{
		$ID=$this->getRequest()->getParam('id');
		
		$ArticleMapper = new Application_Model_ArticleMapper();
		$res = $ArticleMapper->checkArticle($ID);
		
		if($res){
			$info="文章已成功审核！";
		} else {
			$info="文章审核失败！";
		} 
		$this->_redirect("/admin/listarticle");//admin?info=$info
	}

	/**
	* 文章退稿
	*/
	public function rejectarticleAction()
	{
		$ID = $this->getRequest()->getParam('id');
		$type = $this->_request->getParam("type");

		$this->_helper->layout->disableLayout();

		if ($type == 'post') {
			if (!empty($ID)) {
				if ($this->getRequest()->getParam('problem_format') == "on") 
					$Reason = "缩进存在问题，";
				else
					$Reason = "";

				if ($this->getRequest()->getParam('problem_image') == "on") 
					$Reason .= "图片存在问题，";
				else
					$Reason .= "";

				if ($this->getRequest()->getParam('problem_content') == "on") 
					$Reason .= "内容存在问题，";
				else
					$Reason .= "";

				$Reason .= $this->getRequest()->getParam('reason');

				$ArticleMapper = new Application_Model_ArticleMapper();
				$res = $ArticleMapper->rejectArticle($ID,$Reason);

				if (!isset($res)) {
					$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"退稿失败 %>_<%\");history.back();</script>";
					echo $string;
					exit;
				} else {
					$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"退稿成功！\");location.href = \"/admin/listarticle\";</script>";
					echo $string;
					exit;
				}
			} else {
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"退稿失败 %>_<%\");history.back();</script>";
				echo $string;
				exit;
			}
		} else {
			$this->view->ID = $ID;
			$ArticleMapper = new Application_Model_ArticleMapper();
			$arrArticle = $ArticleMapper->findArticleById($ID);
			if (!empty($arrArticle)) {
				$DeptMapper = new Application_Model_DepartmentMapper();
				$arr = $DeptMapper->findDept($arrArticle[0]['DeptID']);
				$this->view->DeptName = $arr[0]['DeptName'];

				$ColumnMapper = new Application_Model_UserMapper();
				$arr = $ColumnMapper->findUserByID($arrArticle[0]['WriterID']);
				$this->view->RealName = $arr[0]['RealName'];

				$this->view->Title = $arrArticle[0]['Title'];
				$this->view->Date = date('Y-m-d',$arrArticle[0]['WriteTime']);;
			} else {
				echo "非法访问";
				exit;
			}
		}
	}

	/**
	* 删除文章及文章中存在的图片
	*/
	public function deletearticleAction()
	{
		$ID=$this->getRequest()->getParam('id');
		
		$ArticleMapper = new Application_Model_ArticleMapper();
		$info = $ArticleMapper->deleteArticle($ID);

		$this->_redirect("/admin/listarticle");//admin?info=$info
	}


	/**
	* 修改密码
	*/
	public function modifypwdAction()
	{
		$type = $this->_request->getParam("type");
		$this->view->Type = $type;
		if ($type == 'post') {
			$oldpwd = md5(strip_tags(trim($this->_request->getParam("oldpwd"))));
			$newpwd = strip_tags(trim($this->_request->getParam("newpwd")));
			$repeatnewpwd = strip_tags(trim($this->_request->getParam("repeatnewpwd")));

			$UserMapper = new Application_Model_UserMapper();
			$arr = $UserMapper->checkUser($_SESSION['user']['UserName'],$oldpwd);

			if (!empty($arr)) {
				if (($newpwd!="")&&($newpwd==$repeatnewpwd)) {
					$res = $UserMapper->modifyUserPwd($arr[0]['UID'],$newpwd);

					if (!isset($res)) {
						$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"修改失败 %>_<%\");history.back();</script>";
						echo $string;
						exit;
					} else {
						$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"修改成功！\\n请用新密码重新登录\");location.href = \"/login/logout/type/noalert\";</script>";
						echo $string;
						exit;
					}
				} else {
					$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"新密码有问题\\n请修改后重新提交\");history.back();</script>";
				echo $string;
				exit;
				}
				
			} else {
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"原密码错误\");history.back();</script>";
				echo $string;
				exit;
			}
			
		} else if ($type == 'facebox') {
			$this->_helper->layout->disableLayout();
		}
	}

	/**
	* 用户管理
	*/
	public function usermanagerAction()
	{
		if (($_SESSION['user']['Type'] == 1)&&($_SESSION['user']['DeptID'] == 0)) ;
		else $this->_redirect("/admin");

		$type = $this->_request->getParam("type");
		if ($type == 'add') {
			$UserName = $this->getRequest()->getParam('username');
			$RealName = $this->getRequest()->getParam('realname');
			$Password = $this->getRequest()->getParam('pwd');
			$DeptID   = $this->getRequest()->getParam('deptid');
			$Type     = $this->getRequest()->getParam('usertype');

			$UserMapper = new Application_Model_UserMapper();

			// 判断用户名是否已存在
			$isexist = $UserMapper->findUserByName($UserName);
			if (!empty($isexist)) {
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"用户名已存在\\n请修改后重新添加。\");history.back();</script>";
				echo $string;
				exit;
			}

			// 判断超级管理员是否是中心成员
			if (($DeptID!=0)&&($Type==1)) {
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"用户权限设置错误\\n请修改后重新添加。\");history.back();</script>";
				echo $string;
				exit;
			}

			$result = $UserMapper->addUser($UserName,$RealName,$Password,$DeptID,$Type);
			if (!isset($result)) {
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"用户添加失败 %>_<%\");history.back();</script>";
				echo $string;
				exit;
			} else {
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"成功添加用户！\");location.href = \"/admin/usermanager\";</script>";
				echo $string;
				exit;
			}
		} elseif ($type == 'edit') {
			$UID      = $this->getRequest()->getParam('id');
			$UserName = $this->getRequest()->getParam('username');
			$RealName = $this->getRequest()->getParam('realname');
			$DeptID   = $this->getRequest()->getParam('deptid');
			$Type     = $this->getRequest()->getParam('usertype');

			$UserMapper = new Application_Model_UserMapper();

			// 判断超级管理员是否是中心成员
			if (($DeptID!=0)&&($Type==1)) {
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"用户权限设置错误\\n请修改后重新添加。\");history.back();</script>";
				echo $string;
				exit;
			}

			$result = $UserMapper->editUser($UID,$UserName,$RealName,$DeptID,$Type);
			if (!isset($result)) {
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"用户信息修改失败 %>_<%\");history.back();</script>";
				echo $string;
				exit;
			} else {
				$string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"用户信息修改成功！\");location.href = \"/admin/usermanager\";</script>";
				echo $string;
				exit;
			}
		} else {
			$UserMapper = new Application_Model_UserMapper();
			$num=20; $page=1; //设置每一页显示的文章数目 //设置第一页显示

			// 正常用户
			$rows_able = $UserMapper->findAbleUser();
			$paginator_able = new Zend_Paginator(new Zend_Paginator_Adapter_Array($rows_able)); //调用分页
			$paginator_able->setItemCountPerPage($num); //设置每一页显示的文章数目
			$paginator_able->setCurrentPageNumber($page); //设置第一页显示
			$paginator_able->setCurrentPageNumber($this->_getParam('page')); //从url获取需要显示的页码
			$this->view->paginator_able = $paginator_able;

			// 停用用户
			$rows_disable = $UserMapper->findDisableUser();
			$paginator_disable = new Zend_Paginator(new Zend_Paginator_Adapter_Array($rows_disable)); //调用分页
			$paginator_disable->setItemCountPerPage($num); //设置每一页显示的文章数目
			$paginator_disable->setCurrentPageNumber($page); //设置第一页显示
			$paginator_disable->setCurrentPageNumber($this->_getParam('page')); //从url获取需要显示的页码
			$this->view->paginator_disable = $paginator_disable;
		}
	}

	/**
	* 弹窗——添加用户
	*/
	public function userboxAction()
	{
		$this->_helper->layout->disableLayout();
		$id = $this->_request->getParam("id");

		if (($_SESSION['user']['Type'] == 1)&&($_SESSION['user']['DeptID'] == 0)) ;
		else {
			echo "非法访问";
			exit;
		}

		$DeptMapper = new Application_Model_DepartmentMapper();
		$arrDept = $DeptMapper->findAllDept();
		$this->view->arrDept = $arrDept;

		if (empty($id)) {
			$this->view->isEdit = 0;
		} else {
			$this->view->isEdit = 1;
			$UserMapper = new Application_Model_UserMapper();
			$User = $UserMapper->findUserByID($id);
			$this->view->User = $User;
		}
	}

	/**
	* 启用用户
	*/
	public function ableuserAction()
	{
		$ID=$this->getRequest()->getParam('id');
		
		$UserMapper = new Application_Model_UserMapper();
		$info = $UserMapper->ableUser($ID);

		$this->_redirect("/admin/usermanager");
	}

	/**
	* 停用用户
	*/
	public function disableuserAction()
	{
		$ID=$this->getRequest()->getParam('id');
		
		$UserMapper = new Application_Model_UserMapper();
		$info = $UserMapper->disableUser($ID);

		$this->_redirect("/admin/usermanager");
	}

	/**
	* 发帖量统计
	*/
	public function statisticsAction()
	{
		if (($_SESSION['user']['Type'] == 1)&&($_SESSION['user']['DeptID'] == 0)) ;
		else $this->_redirect("/admin");

		$DeptMapper = new Application_Model_DepartmentMapper();
		$arr = $DeptMapper->findAllDept();
		$this->view->arrDept = $arr;

		$PrevMonthBeginStamp = strtotime(date('Y',time()).'-'.(date('m',time())-1)."-1 00:00:00");
		$PrevMonthEndStamp   = strtotime(date('Y',time()).'-'.date('m',time())."-1 00:00:00")-1;

		$NowOfWeek = date("w")?date("w"):7;
		$ThisBeginStamp = strtotime(date('Y-m-d',time()-86400*($NowOfWeek-1)).' 00:00:00');
		$ThisEndStamp   = $ThisBeginStamp + 604799;
		$PrevBeginStamp = $ThisBeginStamp - 604800;
		$PrevEndStamp   = $PrevBeginStamp + 604799;

		$ArticleMapper = new Application_Model_ArticleMapper();
		$AllArticle = $ArticleMapper->countArticleByStamp(0,2147483647,1);
		$AllUnPubArticle = $ArticleMapper->countArticleByStamp(0,2147483647,0);

		$PrevMonthArticle = $ArticleMapper->countArticleByStamp($PrevMonthBeginStamp,$PrevMonthEndStamp,1);
		$PrevMonthUnPubArticle =$ArticleMapper->countArticleByStamp($PrevMonthBeginStamp,$PrevMonthEndStamp,0);

		$ThisWeekArticle = $ArticleMapper->countArticleByStamp($ThisBeginStamp,$ThisEndStamp,1);
		$ThisWeekUnPubArticle = $ArticleMapper->countArticleByStamp($ThisBeginStamp,$ThisEndStamp,0);

		$PrevWeekArticle = $ArticleMapper->countArticleByStamp($PrevBeginStamp,$PrevEndStamp,1);
		$PrevWeekUnPubArticle = $ArticleMapper->countArticleByStamp($PrevBeginStamp,$PrevEndStamp,0);

		$this->view->All = $AllArticle;
		$this->view->AllUnPub = $AllUnPubArticle;
		$this->view->PrevMonth = $PrevMonthArticle;
		$this->view->PrevMonthUnPub = $PrevMonthUnPubArticle;
		$this->view->ThisWeek = $ThisWeekArticle;
		$this->view->ThisWeekUnPub = $ThisWeekUnPubArticle;
		$this->view->PrevWeek = $PrevWeekArticle;
		$this->view->PrevWeekUnPub = $PrevWeekUnPubArticle;
	}

	/**
	* 自定义统计发帖量
	*/
	public function periodstatisticsAction()
	{
		$this->_helper->layout->disableLayout();
		$type = $this->_request->getParam("type");
		$this->view->type = $type;

		if (($_SESSION['user']['Type'] != 3)&&($_SESSION['user']['DeptID'] == 0)) ;
		else {
			echo "非法访问";
			exit;
		}

		if ($type=="post") {
			$BeginDay = $this->_request->getParam("beginday");
			$EndDay = $this->_request->getParam("endday");
			$BeginStamp = strtotime($BeginDay.' 00:00:00');
			$EndStamp = strtotime($EndDay.' 23:59:59');
			$TodayEndStamp = strtotime(date('Y-m-d').' 23:59:59');
			// 判断日期是否错误
			if (($EndStamp-$BeginStamp<86399)||($EndStamp>$TodayEndStamp)) {
				echo "<br><center><h3 style=\"color:red\">日期选择错误</h3></center>";
				exit;
			} else {
				$ArticleMapper = new Application_Model_ArticleMapper();
				$PubData = $ArticleMapper->countArticleByStamp($BeginStamp,$EndStamp,1);
				$UnPubData = $ArticleMapper->countArticleByStamp($BeginStamp,$EndStamp,0);

				$DeptMapper = new Application_Model_DepartmentMapper();
				$arrDept = $DeptMapper->findAllDept();
				$countDept = count($arrDept);

				if ($PubData['count']||$UnPubData['count']) {
					echo "<table><thead><tr><th>学院名称</th><th>已发表</th><th>未发表</th><th>总数</th></tr></thead><tbody>";
					$css[0] = " class=\"alt-row\"";
					$css[1] = null;
					for ($i=0; $i<$countDept; $i++) {
						$TotalData = $PubData[$i] + $UnPubData[$i];
						echo "<tr".$css[$i%2].">";
						echo "<td><a href=\"/admin/listarticle/dept/".$arrDept[$i]['DeptCode']."/from/".$BeginStamp."/until/".$EndStamp."\" title=\"查看文章\" target=\"_blank\">".$arrDept[$i]['DeptName']."</a></td>";
						echo "<td>".$PubData[$i]."</td>";
						echo "<td>".$UnPubData[$i]."</td>";
						echo "<td>".$TotalData."</td>";
						echo "</tr>";
					}
					$TotalPub = $PubData['count'];
					$TotalUnPub = $UnPubData['count'];
					$Total = $TotalPub + $TotalUnPub;
					echo "<tr class=\"alt-row\">
							<td><a href=\"/admin/listarticle/from/".$BeginStamp."/until/".$EndStamp."\" title=\"查看文章\" target=\"_blank\" style=\"color:#555555;\">总计</a></td>
							<td>$TotalPub</td>
							<td>$TotalUnPub</td>
							<td>$Total</td>
						  </tr>";
					echo "</tbody></table>";
					exit;
				} else {
					echo "<br><center><h3>期间没有文章发表...</h3></center>";
					exit;
				}
			}
		} else {
			$NowOfWeek = date("w")?date("w"):7;
			$PrevWeekEndStamp = strtotime(date('Y-m-d',time()-86400*($NowOfWeek-1)).' 00:00:00')-1;
			$PrevWeekBeginStamp = $PrevWeekEndStamp - 604799;
			$PrevWeekBeginDay = date('Y-m-d',$PrevWeekBeginStamp);
			$PrevWeekEndDay = date('Y-m-d',$PrevWeekEndStamp);
			$this->view->PrevWeekBeginDay = $PrevWeekBeginDay;
			$this->view->PrevWeekEndDay = $PrevWeekEndDay;
		}

	}

}