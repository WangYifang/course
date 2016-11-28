<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
class Test extends Controller
{
    public function sparrow()
    {

        return $this->fetch();
    }

    public function swallow()
    {
        $this->getLearningExTitles();
        // $this->display('getLearningExTitles'); 
        return $this->fetch();
    }

    public function getLearningExTitles(){
        // ��ѯ���� ����ÿҳ��ʾ10������
        $list = Db::table('learning_ex')->order('release_time desc')->paginate(5);
        // �ѷ�ҳ���ݸ�ֵ��ģ�����list
        $this->assign('list', $list);
    }
}
?>