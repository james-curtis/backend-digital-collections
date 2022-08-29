<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/28 0028
 * Time: 22:05
 */

namespace logicmodel;


use comservice\Response;
use datamodel\Banner;
use datamodel\Calendar;
use datamodel\CalendarGoods;
use datamodel\News;
use datamodel\Notice;

class IndexLogic
{

    /**
     * 轮播图列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bannerList($type){
       $data =  (new Banner())->where(['is_del'=>0,'is_show'=>1,'type'=>$type])->select();
        if($data){
            $data = collection($data)->toArray();
            $data = addWebSiteUrl($data,['image']);
            return Response::success('success',$data);
        }
        return Response::success('暂无数据',[]);
    }

    /**
     * 公告列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function noticeList(){
        $data = (new Notice())->where(['is_del'=>0,'is_show'=>1])->order(['order asc'])->select();
        if($data){
            $data = collection($data)->toArray();
            foreach ($data as &$v){
                $v['content'] = content($v['content']);
            }
            $data = addWebSiteUrl($data,['image']);
            return Response::success('success',$data);
        }
        return Response::success('暂无数据',[]);
    }

    /**
     * 发售日历
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function calendar(){
        $calendar =  (new Calendar())
            ->where(['is_show'=>1,'is_del'=>0,'start_time'=>['>=',date('Y-m-d H:i:s')]])
            ->field(['id','start_time'])
            ->select();
        if(empty($calendar)) return Response::success('暂无数据', []);
        $calendar = collection($calendar)->toArray();
        $calendarGoodsData=  new CalendarGoods();
        foreach ($calendar as &$v){
            $goods = $calendarGoodsData->alias('cg')
                ->join('goods g','g.id =cg.goods_id')
                ->join('goods_category gc','gc.id = g.goods_category_id')
                ->where(['cg.is_show'=>1,'cg.is_del'=>0,'calendar_id'=>$v['id']])
                ->field(['g.name','g.image','g.price','g.stock','gc.name goods_category_name'])
                ->order(['cg.order asc'])
                ->select();
            if(!empty($goods)){
                $goods = collection($goods)->toArray();
                $goods = addWebSiteUrl($goods,['image']);
                $v['goods'] =  $goods;
            }else{
                $v['goods'] = [];
            }
        }
        $calendar =  $this->groupVisit($calendar);
        return Response::success('success',$calendar);
    }
    public function groupVisit($visit)
    {
        $visit_list = [];
        foreach ($visit as $v) {
            $date = date('m月d日', strtotime($v['start_time']));
            $visit_list[$date][] = $v;
        }
        $list = [];
        foreach ($visit_list as $k=>$v) {
            $list[] = ['time'=>$k,'data'=>$v];
        }
        //halt($list);
        foreach ($list as $k=>$v){
            $data =  $this->visit($v['data']);
            $list[$k]['data'] = $data;
        }

        return $list;
    }
    public function visit($visit)
    {

        $visit_list = [];
        foreach ($visit as $v) {
            $date = date('H:i', strtotime($v['start_time']));
            $visit_list[$date][] = $v;
        }
        $data = [];
        foreach ($visit_list as $k=>$v){
            $data[] = ['time'=>$k,'data'=>$v];
        }
        return $data;

    }

    /**公告详情
     * @param $id
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function noticeDesc($id){
        $notice_decs= (new Notice())->where(['id'=>$id])->find();
        if (!empty($notice_decs)){

            $notice_decs['image']='http://'.$_SERVER['HTTP_HOST'].$notice_decs['image'];

            $notice_decs=$notice_decs->toArray();
            return Response::success('success',$notice_decs);
        }

        return Response::success('暂无数据',[]);

    }

}