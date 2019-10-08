<?php

namespace Backet\Model;

use Think\Model;

class GoodsModel extends Model {

    protected $_validate = array(
        array('gsn', 'require', '系统错误,请重新进入此页'),
        array('catid', 'require', '请选择分类'),
        array('name', 'require', '请填写商品名称'),
        array('price', 'require', '请填写体验价格'),
        array('price', 'checkPrice', '体验价格格式不正确', 1, 'callback', 1),
        array('level', 'checkLevel', '请填写完整商品等级价格', 1, 'callback', 3),
        array('files', 'checkImgs', '请上传图片', 1, 'callback', 1),
        array('context', 'require', '请填写商品描述'),
    );
    #
    protected $_auto = array(
        array("status", 1),
        array('level', 'getLevel', 1, 'callback'),
        array('level', 'setLevel', 2, 'callback'),
        array('times', 'time', 3, 'function'),
    );

    /**
     * 检测图片
     * @return type
     */
    protected function checkImgs() {
        $goods_img = M("goods_img");
        #
        $where["gsn"] = I("post.gsn");
        $count = $goods_img->where($where)->count();
        return $count > 0 ? true : false;
    }

    /**
     * 检查价格
     */
    protected function checkPrice($data) {
        if ($data <= 0) {
            return false;
        }
        if (!is_numeric($data)) {
            return false;
        }
        $tmp = explode(".", $data);
        if (strlen($tmp[1]) > 2) {
            return false;
        }
        return true;
    }

    /**
     * 检查等级价格
     */
    protected function checkLevel() {
        $data = I("post.level");

        if (count($data) != 6) {
            return false;
        }
        foreach ($data as $k => $v) {
            if (!$this->checkPrice($v)) {
                return false;
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////

    protected function getLevel() {
        $data = I("post.level");
        $sn = I("post.gsn");
        #
        $goods_level = M("goods_level");
        #
        $put = array();
        foreach ($data as $k => $v) {
            $put[] = array(
                "lid" => $k,
                "gsn" => $sn,
                "price" => $v
            );
        }
        $goods_level->addAll($put);
    }

    protected function setLevel() {
        $data = I("post.level");
        $sn = I("post.gsn");
        #
        $goods_level = M("goods_level");
        foreach ($data as $k => $v) {
            $where = array("lid" => $k, "gsn" => $sn);
            $count = $goods_level->where($where)->count();
            if ($count <= 0) {
                $where["price"] = $v;
                $goods_level->add($where);
                continue;
            }
            $save["price"] = $v;
            $goods_level->where($where)->save($save);
        }
    }


    public function getList($parent_id = 0,$uid){
        //1,获取所有数据
        $arr='';
        //var_dump($uid);exit;
        $rows=order_pick($uid,$arr);

        //2.排序 缩进
        $rows = $this->getChildren($rows,$uid,0);
        return $rows;
    }
    /**
     * 获取儿子
     * @param $rows 所有数据
     * @param $parent_id 父分类id
     * @param $deep 节点深度
     */


    private function getChildren(&$rows,$parent_id,$deep = 0){
       // var_dump($rows);exit;
        static $children = [];//帮我们保存找到的所有子孙
        foreach($rows as $child){//循环遍历所有数据，获取需要的节点
            if ($child['uid'] == $parent_id) {
                $child['sn'] = str_repeat("---",$deep*2).$child['sn'];
                $children[] = $child;//节点AAA
                //再次调用找儿子的方法
                $this->getChildren($rows,$child['aboutid'],$deep+1);
            }
        }
        return $children;
    }

}
