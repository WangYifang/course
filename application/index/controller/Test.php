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
        $event = controller('Index');
        $list=$event->getLearningExTitles();
        // �ѷ�ҳ���ݸ�ֵ��ģ�����list
        $this->assign('list', $list);
        return $this->fetch();
    }
}
?>