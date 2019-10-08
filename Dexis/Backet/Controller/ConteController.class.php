<?php

namespace Backet\Controller;

class ConteController extends CommController {

    /**
     * 皇家学院
     */
    public function artlist() {
        $school = M("school");

        $scho_type = M('scho_type');
        #
        $list = poPage($school, $where);
        foreach ($list['list'] as $k=>$v){
            $type_res =$scho_type->where(['id'=>$v['type']])->find();
           $list['list'][$k]['types'] = $type_res['name'];
        }
        $this->assign("list", $list);
        $this->display();
    }

    /**
     * 添加皇家学院
     * @param type $id
     */
    public function artaddon() {
        //获取学院分类
        $scho_type = M('scho_type');
        $res =$scho_type->select();
        $this->assign('list',$res);
        $this->display();
    }

    /**
     * 修改皇家学院
     * @param type $id
     */
    public function artedits($id) {
        $school = M("school");
        #
        $where["id"] = $id;
        $info = $school->where($where)->find();
        $this->assign("info", $info);

        $scho_type = M('scho_type');
        $res =$scho_type->select();
        $this->assign('list',$res);
        $this->display();
    }

    /**
     * 皇家学士
     */
    public function question() {
        $question = M("question");
        #
        $list = poPage($question, $where);
        $this->assign("list", $list);
        $this->display();
    }

    /**
     * 添加皇家学士
     * @param type $id
     */
    public function quesaddon() {
        $ques_cat = M("ques_cat");
        #
        $where["status"] = 1;
        $list = $ques_cat->where($where)->select();
        $this->assign("list", $list);
        $this->display();
    }

    /**
     * 修改皇家学士
     * @param type $id
     */
    public function quesedits($id) {
        $question = M("question");
        $ques_cat = M("ques_cat");
        #
        $where["id"] = $id;
        $info = $question->where($where)->find();
        $this->assign("info", $info);
        #
        $cond["status"] = 1;
        $list = $ques_cat->where($cond)->select();
        $this->assign("list", $list);
        $this->display();
    }

    /**
     *添加新闻分类
     */
    public function addType(){
        $scho_type = M('scho_type');
        $get = I('get.');
        $res =$scho_type->add($get);
        if($res){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }


}
