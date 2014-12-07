<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        $this ->_helper->layout->disableLayout();
    }

    public function indexAction()
    {
        
    }

    //验证登录
    public function loginAction()
    {
		$username = strip_tags(trim($this->getRequest()->getParam('username')));
        $password = md5(strip_tags(trim($this->getRequest()->getParam('password'))));

        $UserMapper = new Application_Model_UserMapper();
        $arr = $UserMapper->checkUser($username,$password);

        if(!empty($arr))
        {
            if($arr[0]['Activate'] == 1)
            {
                $DeptMapper = new Application_Model_DepartmentMapper();
                $DeptID = $arr[0]['Department'];
                $arrDept = $DeptMapper->findDept($DeptID);
                if(!empty($arrDept))
                {
                    $DeptCode = $arrDept[0]['DeptCode'];
                    $DeptName = $arrDept[0]['DeptName'];
                }

                $session = new Zend_Session_Namespace('user');
                $session->UserID = $arr[0]['UID'];
                $session->UserName = $arr[0]['UserName'];
                $session->RealName = $arr[0]['RealName'];
                $session->DeptID = $arr[0]['Department'];
                $session->DeptCode = $DeptCode;
                $session->DeptName = $DeptName;
                $session->Type = $arr[0]['Type'];
                $session->LastLoginTime = $arr[0]['LastLoginTime'];
                if ($this->getRequest()->getParam('remember') == "on")
                     $session->setExpirationSeconds(3600);
                else $session->setExpirationSeconds(1800);

                $res = $UserMapper->updateLastLoginTime($arr[0]['UID']);
                $this->_redirect('/admin');
            } else {
                $string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"未授权用户！\");location.href = \"/login\";</script>";
                echo $string;
                exit;
            }
        }  else {
            $string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"登陆失败！\");location.href = \"/login\";</script>";
            echo $string;
            exit;
        }
    }
	
	//注销
    public function logoutAction()
    {
        $type = $this->_request->getParam("type");

        if ($type=='noalert') {
            $session = new Zend_Session_Namespace('user');
            unset($_SESSION);
            $_SESSION=array();
            session_destroy();
            $this->_redirect('/login');
            exit;
        } else {
            $this->_helper->layout->disableLayout();
            $session = new Zend_Session_Namespace('user');
            unset($_SESSION);
            $_SESSION=array();
            session_destroy();
            $string = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'><script language=\"JavaScript\">alert(\"注销成功！\");location.href = \"/login\";</script>";
            echo $string;
            exit;
        }
        
    }

}

