<?php
namespace Backet\Controller;


use Think\Db;

class SaleController extends CommController
{
    /**
     *进入经销存添加页
     */
    public function stockAndSale()
    {
        $goods = M('goods');
        $where["ind"] = 1;
        $where["status"] = 1;
        $on_goods = $goods->where($where)->field('id,name,gsn')->select();
        $this->assign('list', $on_goods);
        $this->display();
    }

    /**
     *进销存--增加
     */
    public function save_add()
    {
        $data = I('post.');
        $uid = session('homelc_ssid');
        $res = AddStockAndSale($uid, $data['gid'], $data['where'], $data['num'], 2, $data['remark'],$data['supplier']);
        if ($res) {
            return get_op_put(1, "添加成功");
        }else{
            return get_op_put(0, "添加失败");
        }
    }

    /**
     *经销存 -- 列表
     */
    public function salelist()
    {
        $ware = M('goods_ware');
        $outList = M('goods_outlist');
        $get = I('get.');
        if ($get['where'] ===-1){
            unset($get['where']);
        }
        //获取上架添加产品
        $goods = M('goods');
        $where["ind"] = 1;
        $where["status"] = 1;
        $on_goods = $goods->where($where)->field('id,name,gsn')->select();
//        $gid =$get['gid'] ==null?8:$get['gid'];
        if($get['gid']==null){
            $arr_id =array_column($on_goods,'id');
            $gid =array('in',implode(',',$arr_id));
        }else{
            $gid =$get['gid'];
        }
        //产品总计
        $data_info = $ware->where(['goods_id'=>$gid])->find();
        $where['g_id'] = $gid;
        //判断进出货类型
        if(isset($get['type'])){
            $where['type'] = $get['type'];
        }
        if($get['way']!=''){
            $get['where']=$get['way'];
        }
        //判断出的
        if($get['where']!=''){
            $where['where_in'] = $get['where'];
        }

        //时间
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["addtime"] = array("gt", $start);
            }if(!$start){
                $where["addtime"] = array("lt", $ends);
            }
            if( $ends > $start) {
                $where["addtime"] = array(array("gt", $start), array("lt", $ends));
            }
            if ($ends == $start){
                $where["addtime"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        //
        //产品列表
        $list = poPage($outList, $where,'id asc');
//       $list = $outList->where($where)->select();
        $all_count=0;
        foreach ($list['list'] as $k => $v) {
            $goods_info =getIDGoodsInfo($v['g_id']);
            $res_data =explode('/',$goods_info['net']);
            $list['list'][$k]['time'] = date('Y-m-d H:i:s', $v['addtime']);
            $list['list'][$k]['inOutType'] = $v['type'] ==2?($v['where_in']==0?'内部调拨':'采购入库'): ($v['where_in']==1?'中通仓库':'公司仓库') ;//进出方式
            $list['list'][$k]['model'] = $res_data[0];//规格型号
            $list['list'][$k]['unit'] = $res_data[1];//计量单位
            $list['list'][$k]['inwhere'] = getOutType($v['where_in']);//进出库地址
            //用户信息
            $list['list'][$k]['username'] = $v['uid'] ==0?'线下出货':getUserInf($v['uid'],'name');//计量单位
            //小计
            $all_count += $v['nums'];
        }
        if($where){
            //为出货
            $where['type'] =1;
            $data_info['all_sale'] =$outList->where($where)->sum('nums');//总出
            $data_info['all_sale'] = $data_info['all_sale']==null?0: $data_info['all_sale'];
        }


        //输出模板
        if($get['type']==1){ //出货
            $temple ="sale/out";
        }elseif($get['type']==2){
            $temple ="sale/in";
        }elseif($get['type']==3){ //库存及记录
            unset($list);
           //获取库存列表
            $alldata =$this->getInventory($get);
            $list =$alldata;
            $on_goods =$alldata['ongoods'];
            $temple ="sale/save";
        }elseif($get['type']==4){
            $temple ="sale/table";
        }
        if($get['where']==null){
            $get['where'] =-1;
        }
        $this->assign('goods', $on_goods);
        $this->assign('temple', $temple);
        $this->assign('list', $list);
        $this->assign('data_info', $data_info);
        $this->assign('all_count', $all_count);
        $this->assign('get', $get);
        $this->display();
    }

    /**
     * 获取库存列表
     */
    public function  getInventory($get){
        $goods = M('goods');
        $goods_ware =M('goods_ware');
        $all_save =$goods_ware->select();
        foreach ($all_save as $k =>$v){
            $all_pro[]=$v['goods_id'];
            $goods_info =getIDGoodsInfo($v['goods_id']);
            $res_data =explode('/',$goods_info['net']);
            $all_save[$k]['model'] = $res_data[0];//规格型号
            $all_save[$k]['unit'] = $res_data[1];//计量单位
        }
        $where["id"] = array('in',implode(',',$all_pro));
        $on_goods = $goods->where($where)->field('id,name,gsn')->select();
        return ['list'=>$all_save,'ongoods'=>$on_goods];
//        $this->assign('goods', $on_goods); //产品
//        $temple ="sale/save";
//        $this->assign('temple', $temple);
//        $this->display('salelist');
    }

    /**
     *财务报表 --进销存汇总表
     */
    public function getTableList(){
        set_time_limit(0);
        $get=I('get.');
        $p =$get['p']==null?1:$get['p'];
        $goods_outlist = M('goods_outlist');
        if($get['where']!=null){
            $out_where =$get['where']!=1?array('neq',1):$get['where'];
            $where['where_in'] =$out_where;
        }
        if($get['start']||$get['ends']) {
            $where['addtime'] = array('between', [strtotime($get['start']), strtotime($get['ends'])+86400]);
        }
        if($get['gid']) {
            $where['g_id'] = $get['gid'];
        }
        if($get['table']==2){
            $where['type'] =1;
        }
        if($get['user']!=null){
            $accounr["name"] = array("like", "%" . $get["user"] . "%");
            $id =M('account')->where($accounr)->field('id')->select();
            $get_id=array_column($id,'id');
            $where['uid'] =array('in',implode(',',$get_id));
        }
        $resData =$goods_outlist->where($where)->order('id asc')->select();
//        数据分页
       /* $start =($p-1)*20;
        $for_count =count($resData)-$start<20?count($resData)-$start:$start;
        $page =count($resData)/20;*/
//        for($i=$start;$i<$for_count;$i++) {
            $all_count =0;
            foreach ($resData as $k => $v) {
                    //统计数量
                $all_count +=$v['nums'];
                //获取产品信息
                $goods_info = getIDGoodsInfo($v['g_id']);
                $res_data = explode('/', $goods_info['net']);
                $resData[$k]['model'] = $res_data[0];//规格型号
                $resData[$k]['unit'] = $res_data[1];//计量单位
                $resData[$k]['time'] = date('Y-m-d H:i:s', $v['addtime']);
                //获取价格的信息 --联创购买价
                $prices = doPrice($goods_info['gsn'], 6);
                $resData[$k]['prices'] = $prices;
                //获取成本价
                //成本单价
                $resData[$k]['now_cost'] = $goods_info['gross'];
                $resData[$k]['maori'] = $resData[$k]['prices']-$resData[$k]['now_cost'];
                $resData[$k]['gross_maori'] =round($resData[$k]['maori']/$resData[$k]['prices'],4)*100 .'%' ;
                //获取用户信息
                $username = getUserInf($v['uid'], 'name');
                $level = getUserInf($v['uid'], 'level');
                $username == null ? '线下出货' : $username;
                $resData[$k]['username'] = $username;
                $resData[$k]['level'] = getUserLevel($level);
                $resData[$k]['upuser'] =getRecUser($v['uid']) ;
                $resData[$k]['where'] = $v['where_in'] == 1 ? '中通仓库' : '公司仓库';
                $resData[$k]['outnums'] = 0;
                $resData[$k]['innums'] = 0;
                if ($v['type'] == 1) {
                    $resData[$k]['outnums'] = $v['nums'];
                    //小计
                    $in_all_count +=$v['nums'];
                } elseif ($v['type'] == 2) {
                    $resData[$k]['innums'] = $v['nums'];
                    //小计
                    $out_all_count +=$v['nums'];
                }
            }
//        }
        //分页


        echo json_encode(['data'=>$resData,'inall'=>$out_all_count,'outall'=>$in_all_count,'allcount'=>$all_count],1);
    }

    /**
     *销售成本分析表 --没用
     */
    public function profitList(){
        $get =I('get.');
        $goods_outList =M('goods_outlist');
        if($get['where']){
            $where['where_in'] =$get['where'];
        }
        if($get['start']||$get['ends']) {
            $where['addtime'] = array('between', [$get['start'], $get['ends']]);
        }
        if($get['gid']) {
            $where['g_id'] = $get['gid'];
        }
        $resData =$goods_outList->where($where)->order('id asc')->select();
        foreach ($resData as $k=>$v){
            //获取产品信息
            $goods_info =getIDGoodsInfo($v['g_id']);
            $res_data =explode('/',$goods_info['net']);
            $resData[$k]['model'] = $res_data[0];//规格型号
            $resData[$k]['unit'] = $res_data[1];//计量单位
            $resData[$k]['time'] = date('Y-m-d H:i:s', $v['addtime']);
            //获取价格信息 --联创
            $prices =doPrice($goods_info['gsn'],6);
            $resData[$k]['prices'] = $prices;
            //获取用户信息
            $username =getUserInf($v['uid'],'username');
            $username ==null?'线下出货':$username;
            $resData[$k]['username'] = $username;
            $resData[$k]['where'] = $v['where_in']==1?'中通仓库':'公司仓库';
            $resData[$k]['outnums']=0;
            $resData[$k]['innums']=0;
            if($v['type'] ==1){
                $resData[$k]['outnums'] = $v['nums'];
            }elseif($v['type'] ==2){
                $resData[$k]['innums'] = $v['nums'];
            }
        }
    }

    /**
     *添加出货记录--线下
     */
    public function addOutCode(){
        if(IS_POST){
            $data =I('post.');
            $good_outlist =M('goods_outlist');
            $goods_ware =M('goods_ware');
            $goods =M('goods');
            $goods_info =$goods->where(['id'=>$data['gid']])->find();//查询产品
            $res_info =$goods_ware->where(['goods_id'=>$data['gid']])->find();
            //添加出货记录
            $add_data  =[
                'g_id'=>$data['gid'],
                'uid'=>0,
                'name'=>$goods_info['name'],
                'nums'=>$data['num'],
                'remake'=>$data['remark'],
                'type'=>1,
                'where_in'=>$data['where'],
                'addtime'=>time(),
                'rest_save'=>$res_info['all_save']-$data['num'],
            ];
            $res =$good_outlist->add($add_data);
            /*if($res){
                $this->success('添加成功',U('salelist'));
            }*/
            if ($res) {
                //扣除库存
                $update["company_save"]= array("exp", "company_save-" .$data['num']);//公司库存减
                $update["all_save"]= array("exp", "all_save-" .$data['num']);//扣除总库存
                $update["all_sale"]= array("exp", "all_sale+" .$data['num']);//增加总出
                $goods_ware->where(['goods_id'=>$data['gid']])->save($update);
                return get_op_put(1,"添加成功");
            }else{
                return get_op_put(0,"添加失败");
            }

        }else{
            $goods = M('goods');
            $where["ind"] = 1;
            $where["status"] = 1;
            $on_goods = $goods->where($where)->field('id,name,gsn')->select();
            $this->assign('list', $on_goods);
            $this->display();
        }
    }

    /**
     *获取返利的列表详情 2018-11-27 18:06:03 add
     */
    public function  getRebackMoneyList(){
        $get =I('get.');
        $acc_money =M('acc_money');
        $acc_num =M('acc_nums');
        $where['models'] ="REBACK";
        if($get['start']||$get['ends']){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
            }if(!$start){
                $where["times"] = array("lt", $ends);
            }
            if( $ends > $start) {
                $where["times"] = array('between',[$start,$ends]);
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        $all_reback =$acc_money->where($where)->order('id desc')->select();
        $all_reback_money=0;
        foreach ($all_reback as $k=>$v){
            if($v['money'] ==0){
                unset($all_reback[$k]);
            }else{
                //获取出货的用户
                $out_user =$acc_num ->where(['sn'=>$v['sn']])->field('aboutid')->find();
                //出货人为公司的话
                if($out_user['aboutid']!=0){
                    unset($all_reback[$k]);
                }else{
                    $all_reback[$k]['name'] =getUserInf($v['uid'],'nickname');
                    $all_reback[$k]['level'] =getUserLevel(getUserInf($v['uid'],'level'));
                    $all_reback[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
                    $all_reback_money+=$v['money'];
                }
            }
        }
        $this->assign('data',$all_reback);
        $this->assign('money',$all_reback_money);
        $this->assign('get',$get);
        $this->display();
    }
}
