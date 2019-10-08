<?php
/**
 * Created by PhpStorm.
 * User: uuu
 * Date: 2019/7/17
 * Time: 13:50
 */


namespace Backet\Controller;

class ApplyController extends CommController {

    /**
     *获取申请列表
     */
    public function index(){
        $get =I('get.');
        $model =M('order_zb');
        $where=[];
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
            }if(!$start){
                $where["times"] = array("lt", $ends);
            }
            if( $ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends));
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . trim($get["sn"]) . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . trim($get["phone"]) . "%") : null;
        $list= poPage($model, $where);
        foreach($list['list'] as $k=>$v){
            $list['list'][$k]['username'] =getUserInf($v['uid'],'name');
            $list['list'][$k]['userphone'] =getUserInf($v['uid'],'phone');
            $list['list'][$k]['times'] =date('Y-m-d H:i:s',$v['add_time']);
        }
        $this->assign('list',$list);
        $this->assign('get',$get);
        $this->display();
    }
}