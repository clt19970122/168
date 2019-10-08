<?php

namespace Backet\Controller;

#use Think\Controller;

class CoreController extends CommController {

    private $urls = array(
        "syscat" => "Goods/catlist",
        "goods" => "Goods/index",
        "shops" => "Shop/index",
        "account" => "Users/index",
        "sys_ratio" => "Shop/ratio",
        "sys_rindex" => "Retrn/index",
        "sys_rfixed" => "Retrn/fixed",
        "sys_rupgrade" => "Retrn/upgrade",
        "sys_mindex" => "Membs/index",
        "fares" => "Fares/index",
        #
        "sysads" => "Dash/ads",
        "sysmsg" => "Dash/pub",
        "systrans" => "Dash/trans",
        "sysconfig" => "Dash/basic",
        "orders" => "Goods/order",
        "order_fixed" => "Fixed/index",
        "money_draw" => "Fina/index",
    );

    /**
     * 添加
     * @param type $model
     */
    public function addon($model, $redc = null) {
        $db = D($model);
        if (!$db->create(I("post."), 1)) {
            return get_op_put(0, $db->getError());
        }
        $db->startTrans();
        if (!$db->add()) {
            $db->rollback();
            return get_op_put(0, "添加失败");
        }
        $this->addon_check($model);
        #
        $db->commit();
        $urls = $this->urls[$model];
        if ($redc != null) {
            $urls = $this->urls[$redc];
        }
        return get_op_put(1, "添加成功", U($urls));
    }

    /**
     * 添加检测
     * @param type $model
     */
    private function addon_check($model) {
        //$model == "goods" ? session("goods_auth_i", null) : null;
        $model == "goods" ? closeSysLn($model, 1, $this->ssid) : null;
        $model == "fares" ? closeSysLn($model, 1, $this->ssid) : null;
        $model == "bang" ? closeSysLn($model, 1, $this->ssid) : null;
    }

    /**
     * 编辑客户信息
     * @param type $model
     */
    public function edits($model, $redc = null) {
        $db = D($model);
        #
        if (!$db->create(I("post."), 2)) {
            return get_op_put(0, $db->getError());
        }
        $db->startTrans();
        if (!$db->where("id='" . I("post.ids") . "'")->save()) {
            $db->rollback();
            return get_op_put(0, "没有修改");
        }
        $db->commit();
        $urls = $this->urls[$model];
        if ($redc != null) {
            $urls = $this->urls[$redc];
        }
        return get_op_put(1, "修改成功", U($urls));
    }

    /**
     * 删除客户
     * @return type
     */
    public function dels($model, $redc = null) {
        $db = D($model);

        $post = I("post.");
        if (!$db->where($post)->delete()) {
            return get_op_put(0, "删除失败");
        }
        $urls = $this->urls[$model];
        if ($redc != null) {
            $urls = $this->urls[$redc];
        }
        return get_op_put(1, $post, U($urls));
    }

}
