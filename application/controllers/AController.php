<?php

/* 用于将旧版网站重定向(301)至新版网站 */
class AController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_redirect('/pengbei',array('code'=>301));
    }

    public function kuaibaozhongxinAction()
    {
        $this->_redirect('/pengbei/list/column/rdxw',array('code'=>301));
    }

    public function xuechangyuwoAction()
    {
        $this->_redirect('/pengbei/list/column/bbfc',array('code'=>301));
    }

    public function caogenxiaoyuanAction()
    {
        $this->_redirect('/pengbei/list/column/cgxy',array('code'=>301));
    }


}