<?php
namespace app\index\model;
use think\Model;
use think\Db;
class ExperimentModel extends Model
{
    public function getComment(){
        if(session('?login')){
            $id=session('usr_id');
            $type=session('usr_type');
            if($type=="1"){
                if (session('?class_id')) {
                    $class_id=session('class_id');
                    $result = Db::table('take')->where('class_id',$class_id)->count();
                    $student_num = $result;

                    $join = [['experiment b','a.class_id=b.class_id and a.n_th=b.n_th']];
                    $result = Db::table('do_experiment')->alias('a')->join($join)->where('b.class_id',$class_id)->where('a.stu_id',$id)->field('b.n_th,a.score as given_score,b.sum_grade as total_score,a.comment as remark')->order('n_th')->select();
                    $array=$result;

                    $join = [['experiment b','a.class_id=b.class_id and a.n_th=b.n_th']];
                    $result = Db::table('do_experiment')->alias('a')->join($join)->where('b.class_id',$class_id)->field('b.n_th,truncate(avg(score),1) as average,truncate(max(score),1) as highest,truncate(min(score),1) as lowest')->group('b.class_id,b.n_th')->order('n_th')->select();

                    for ($i=0; $i < count($result); $i++) { 
                        $array[$i]['average']=$result[$i]['average'];
                        $array[$i]['highest']=$result[$i]['highest'];
                        $array[$i]['lowest']=$result[$i]['lowest'];

                        $join = [['experiment b','a.class_id=b.class_id and a.n_th=b.n_th']];
                        $array[$i]['rank']=Db::table('do_experiment')->alias('a')->join($join)->where('b.class_id',$class_id)->where('b.n_th',$result[$i]['n_th'])->where('a.score','>',$array[$i]['given_score'])->count()+1;

                        $array[$i]['student_num']=$student_num;
                    }
                    $result=$array;
                    if(!$result){
                        $result='failure';
                    }
                }else{
                    $result='false_noClassID';
                }
            }else{
                $result='false_type';
            }
        }else{
            $result='false_unlogin';
        }
        $arr = array('result' => $result);
        return json_encode($arr,JSON_UNESCAPED_UNICODE);
    }

    public function showAns($n_th){
        if(session('?login')){
            $id=session('usr_id');
            $type=session('usr_type');
            if($type=="2"){
                if (session('?class_id')) {
                    $class_id=session('class_id');

                    $join=[
                        ['student b','b.stu_id=a.stu_id']
                    ];
                    $result = Db::table('do_experiment')->alias('a')->join($join)->where('a.class_id',$class_id)->where('n_th',$n_th)->field('a.stu_id as studentID,b.stu_name as studentName,a.url as URL')->select();
                    session('experiment_th',$n_th);
                }else{
                    $result='false_noClassID';
                }
            }else{
                $result='false_type';
            }
        }else{
            $result='false_unlogin';
        }

        $arr = array('result' => $result);
        return json_encode($arr,JSON_UNESCAPED_UNICODE);
    }

    public function setComment($json){
        $array = json_decode($json);
        $commentArr=array();
        if(session('?login')){
            $id=session('usr_id');
            $type=session('usr_type');
            if($type=="2"){
                if (session('class_id')) {
                    $class_id=session('class_id');
                    if(session('?experiment_th')){
                        $experiment_th=session('experiment_th');
                        for($i=0;$i<count($array);$i++){
                            $stu_id=$array[$i]->studentID;
                            $score=$array[$i]->score;
                            $comment=$array[$i]->comment;

                            $result=Db::table('do_experiment')->where('class_id',$class_id)->where('n_th',$experiment_th)->where('stu_id',$stu_id)->update(['score'=>$score,'comment'=>$comment]);
                        }
                        $result="success";
                    }else{
                        $result="false_noExperimentTh";
                    }
                }else{
                    $result="false_noClassID";
                }
            }else{
                $result="false_type";
            }
        }else{
            $result="false_unlogin";
        }
        session('experiment_th',null);
        $arr = array('result' => $result);
        return json_encode($array,JSON_UNESCAPED_UNICODE);
    }

    public function getTracer(){
        if(session('?login')){
            $id=session('usr_id');
            $type=session('usr_type');
            if($type=="2"){
                if (session('?class_id')) {
                    $class_id=session('class_id');
                    $result = Db::table('do_experiment')->where('class_id',$class_id)->field('n_th,COUNT(stu_id) as done')->group('n_th')->select();
                    if(!$result){
                        $result='failure';
                    }
                    $array=$result;
                    $result = Db::table('take')->where('class_id',$class_id)->count();
                    for ($i=0; $i < count($array); $i++) { 
                        $array[$i]['total']=$result;
                    }
                    $result=$array;
                }else{
                    $result='false_noClassID';
                }
            }else{
                $result='false_type';
            }
        }else{
            $result='false_unlogin';
        }
        $arr = array('result' => $result);
        return json_encode($arr,JSON_UNESCAPED_UNICODE);
    }
}