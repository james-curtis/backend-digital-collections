<?php


namespace app\api\controller;


use comservice\GetRedis;
use logicmodel\IndexLogic;
use think\Controller;
use think\Request;
use think\Db;
use datamodel\GoodsUsers;
use datamodel\Goods;
use comservice\Response;
use app\admin\model\ChipComposeRecord;

class Index extends BaseController
{
    private $indexLogic;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->indexLogic = new IndexLogic();
        $this->redis = GetRedis::getRedis();
    }


    public function md5()
    {
        $phone = '18287103239';
        $type = 3;
        $key = 'Mibai699SETdDEkdhKEHkdhkDhekb12DIdhk';
        $sign = md5($phone . $type . $key);
        var_dump($sign);
    }

    /**
     * 获取网站配置
     * @return \think\response\Json
     */
    public function siteConfig()
    {
        $config = [
            'appLogo' => config('site.app_logo'),
            'name' => config('site.name'),
            'beian' => config('site.beian'),
            'isMarketOpen' => intval(config('site.is_trade')) === 1,
            'appMarketCloseImg' => config('site.app_market_close_img'),
            'appRegisterBackgroundImage' => config('site.app_register_background_image'),
            'contactPhone' => config('site.contact_phone_1'),
            'contactQR' => config('site.zqh'),
        ];
        return json(addWebSiteUrl($config));
    }

    /**
     * 轮播图列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bannerList($type = 1)
    {
        return json($this->indexLogic->bannerList($type));
    }

    /**
     * 系统公告列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function noticeList()
    {
        return json($this->indexLogic->noticeList());
    }

    public function calendar()
    {
        return json($this->indexLogic->calendar());
    }

    /*
    客服
    */
    public function contacts()
    {
        $config = Db::name('config')->where('group', 'contact')->field('id,title,value')->select();
        $config = addWebSiteUrl($config, ['value']);
        return json($config);
    }


    public function cs()
    {
        $config = Db::name('config')->where('group', 'baiduchain')->field('id,title,value')->select();
        return json($config[7]['value']);
    }

    //优先购
    public function yxg()
    {
        $good_id = $this->request->post('goods_id');
        $users = Db::name('users')->where('id', $this->uid)->find();
        $goodss = Db::name('goods')->where('id', $good_id)->find();
        $config = Db::name('config')->where('group', 'timeconfiguration')->select();

        $data['times'] = 0;

        #查出是否持有赋能的产品
        $fn = Db::name('goods')->where('fnstatus', 1)->select();
        if ($fn) {
            $fn_list = array();
            foreach ($fn as $k => $v) {
                $fn_list[] = $fn[$k]['id'];
            }
            $goods_users = Db::name('goods_users')->where('uid', $this->uid)->where('status', 1)->select();
            foreach ($goods_users as $key => $value) {
                if (in_array($goods_users[$key]['goods_id'], $fn_list)) {
                    $data['times'] = $config[0]['value'];
                }
            }
        }
        if ($users['yxg'] == 1) {
            $data['times'] = $config[0]['value'];
        }
        $data['start_time'] = $goodss['start_time'];
        $data['end_time'] = $goodss['end_time'];
        return json($data);
    }


    //重复折叠
    public function GoodsUsersList()
    {
        $status = $this->request->post('status');
        $where['uid'] = $this->uid;
        $where['is_del'] = 0;
        $where['status'] = $status;
        $goodsusers = Db::name('goods_users')->distinct(true)->where($where)->field('goods_id')->select();
        $data = array();

        foreach ($goodsusers as $key => $value) {
            $goods = Db::name('goods')->where('id', $value['goods_id'])->find();
            $where['goods_id'] = $goods['id'];
            $gooduser = Db::name('goods_users')->where($where)->count();
            $goodsusers[$key]['count'] = $gooduser;
            $goodsusers[$key]['id'] = $goods['id'];
            $goodsusers[$key]['image'] = 'http://' . $_SERVER['HTTP_HOST'] . $goods['image'];
            $goodsusers[$key]['name'] = $goods['name'];
        }
        $data = $goodsusers;

        return json($data);
    }


    public function collectionList()
    {
        $uid = $this->uid;
        $status = $this->request->post('status');
        $id = $this->request->post('id');
        $where['gu.uid'] = $uid;
        $where['gu.is_del'] = 0;
        $where['gu.goods_id'] = $id;
        if ($status == 1) {
            $where['gu.status'] = $status;
        } elseif ($status == 2) {
            $where['gu.status'] = 2;
        } else {
            return json('产品状态错误');
        }
        $data = (new GoodsUsers())->alias('gu')
            ->join('goods g', 'g.id = gu.goods_id')
            ->where($where)
            ->field(['gu.id', 'g.name goods_name', 'g.image goods_image', 'gu.status', 'gu.price', 'gu.class_id', 'gu.operation_id', 'gu.jlstatus'])
            ->order(['gu.id desc'])
            ->select();
        if (!empty($data)) {
            $data = collection($data)->toArray();
            $data = addWebSiteUrl($data, ['goods_image']);
            return json($data);
        }
        return json('暂无数据', []);
    }

    public function Ranking()
    {
        $page = $this->request->post('page');
        $pagesize = $this->request->post('pagesize');
        $count = Db::name('users')->where('id', 'not in', 1)->count();
        $users = Db::name('users')->where('id', 'not in', 1)->order('group_person_count desc')->page($page, $pagesize)->field('id,nick_name,group_person_count')->select();
        $data = ['count' => $count, 'data' => $users, 'page' => $page, 'pagesize' => $pagesize];
        return json($data);
    }

    //合成列表
    public function synthesisList()
    {
        $page = intval($_POST['page'] ?? 1);
        $pagesize = 10;
        $goods = new Goods();
        $goodsusers = new GoodsUsers();
        $chipComposeRecordModel = new ChipComposeRecord();

        $count = $goods->where(['hcstatus' => 1, 'is_manghe' => 2])->count();
        //合成列表
        $goodslist = $goods->where(['hcstatus' => 1, 'is_manghe' => 2, 'is_del' => 0])->page($page, $pagesize)->field('id,image,hcgoods_id,name,stock limitnum,surplus')->select();

        $hc_list = array();


        // print_r($hc_list);
        // exit();
        // $goods_users=Db::name('goods_users')->where(['uid'=>$this->uid,'status'=>1,'is_del'=>0])->select();
        // foreach ($goods_users as $key=>$value){
        //     if(in_array($goods_users[$key]['goods_id'],$fn_list)){
        //         $data['times']=$config[0]['value'];
        //     }
        // }
        $arr = 0;
        foreach ($goodslist as $value) {
            $hccs = $chipComposeRecordModel->where(['goods_id' => $value['id'], 'user_id' => $this->uid])->count();
            $counts = explode(',', $value['hcgoods_id']);
            //合成藏品数组个数
            $hccpcounts = count(explode(',', $value['hcgoods_id']));
            //用户持有合成藏品列表
            $goodsuserss = Db::name('goods_users')->where(['uid' => $this->uid, 'status' => 1, 'is_del' => 0])->whereIn('goods_id', $value['hcgoods_id'])->select();
            //用户持有合成藏品个数
            $goodsusers = Db::name('goods_users')->where(['uid' => $this->uid, 'status' => 1, 'is_del' => 0])->whereIn('goods_id', $value['hcgoods_id'])->count();

            $value['image'] = 'http://' . $_SERVER['HTTP_HOST'] . $value['image'];
            if ($goodsusers >= $hccpcounts) {

                $num = $goodsusers % $hccpcounts;

                foreach ($goodsuserss as $values) {
                    $hc_list[] = $values['goods_id'];
                    //print_r($hc_list);
                }
                for ($i = 0; $i < floor($goodsusers / $hccpcounts); $i++) {
                    if (!array_diff($counts, $hc_list)) {
                        $arr = $arr + 1;

                    }
                }

                $value['hcsycs'] = $arr;
            } else {
                $value['hcsycs'] = 0;
            }
        }
        $data = ['count' => $count, 'data' => $goodslist, 'page' => $page, 'pagesize' => $pagesize];
        return json($data);
    }

    //合成详情
    public function synthesis()
    {
        $id = $this->request->post('id');
        $goods = new Goods();
        $goodsusers = new GoodsUsers();
        $chipComposeRecordModel = new ChipComposeRecord();
        $goodslist = $goods->where('id', $id)->field('id,image,hcgoods_id,name,content')->find();
        $goodslist['image'] = 'http://' . $_SERVER['HTTP_HOST'] . $goodslist['image'];
        $data['goodslist'] = $goodslist;
        $hccs = $chipComposeRecordModel->where(['goods_id' => $id, 'user_id' => $this->uid])->count();
        $counts = explode(',', $goodslist['hcgoods_id']);
        //合成藏品数组个数
        $hccpcounts = count(explode(',', $goodslist['hcgoods_id']));
        //用户持有合成藏品列表
        $goodsuserss = Db::name('goods_users')->where(['uid' => $this->uid, 'status' => 1, 'is_del' => 0])->whereIn('goods_id', $goodslist['hcgoods_id'])->select();
        //用户持有合成藏品个数
        $goodsusers = Db::name('goods_users')->where(['uid' => $this->uid, 'status' => 1, 'is_del' => 0])->whereIn('goods_id', $goodslist['hcgoods_id'])->count();

        $arr = 0;

        if ($goodsusers >= $hccpcounts) {

            $num = $goodsusers % $hccpcounts;

            foreach ($goodsuserss as $values) {
                $hc_list[] = $values['goods_id'];
                //print_r($hc_list);
            }
            for ($i = 0; $i < floor($goodsusers / $hccpcounts); $i++) {
                if (!array_diff($counts, $hc_list)) {
                    $arr = $arr + 1;

                }
            }

            $data['hcsycs'] = $arr;
        } else {
            $data['hcsycs'] = 0;
        }

        return json($data);
    }

    /**
     * 碎片合成
     */
    public function chipCompose()
    {

        $goodsid = $this->request->post('id');

        //$code=0;
        $goodInfo = Db::name('goods')->where('id', $goodsid)->field('id,price,hcgoods_id')->find();
        $goods_id = explode(',', $goodInfo['hcgoods_id']);

        $chipComposeRecordModel = new ChipComposeRecord();
        $chipComposeRecordData['user_id'] = $this->uid;
        $chipComposeRecordData['goods_id'] = $goodInfo['id'];
        $chipComposeRecordData['compose_num'] = 1;
        //添加到我的藏品中
        $goodsUsersData = new GoodsUsers();
        $goods_user_number = $goodsUsersData->where(['goods_id' => $goodsid])->whereNotNull('number')->order('id', 'desc')->value('number');
        if ($goods_user_number) {
            $goods_user_number = str_pad($goods_user_number + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $goods_user_number = '000001';
        }
        $time = date('Y-m-d H:i:s');
        $goods_number = uniqueNum();
        $usersGoods['uid'] = $this->uid;
        $usersGoods['goods_id'] = $goodInfo['id'];
        $usersGoods['goods_number'] = $goods_number;
        $usersGoods['price'] = $goodInfo['price'];
        $usersGoods['create_time'] = $time;
        $usersGoods['status'] = 1; //待出售
        $usersGoods['number'] = $goods_user_number;
        $results = $goodsUsersData->insertGetId($usersGoods);
        if (!$results) {
            Db::rollback();
            return json('碎片合成失败!');
        }
        Db::startTrans();
        $result = $chipComposeRecordModel->save($chipComposeRecordData);
        if (!$result) {
            Db::rollback();
            return json('碎片合成失败!');
        }
        if ($results) {
            $goods_users = Db::name('goods_users')->where('id', $results)->find();
            $goods = Db::name('goods')->where('id', $goods_users['goods_id'])->find();
            $users = Db::name('users')->where('id', $this->uid)->find();

            foreach ($goods_id as $value) {
                $goodsuserss = Db::name('goods_users')->where(['uid' => $this->uid, 'status' => 1, 'is_del' => 0, 'goods_id' => $value])->min('id');
                Db::name('goods_users')->where('id', $goodsuserss)->update(['is_del' => 1]);
            }
            Db::name('goods_users')->where('id', $results)->update(['jlstatus' => 2]);
            //   库存问题
            //库存剩余递减 销量递增
            Db::name('goods')->where(['id' => $goodsid])->setDec('surplus', 1);
            Db::name('goods')->where(['id' => $goodsid])->setInc('sales', 1);
            $redis = GetRedis::getRedis();
            // redis 减少库存
            $kc = $this->redis->rpop('goods_kc_' . $goodsid);
            if (!$kc) {
                Db::rollback();

                $responsData = Response::fail('碎片合成失败!库存不足');
                return json($responsData);
            }
            Db::commit();


            $code = 1;
            $responsData = Response::success('success');
            return json($responsData);
        } else {
            return json('碎片信息错误!');
        }

    }

    /**公告详情
     * @return \think\response\Json
     */
    public function noticeDesc()
    {

        $noticeid = $this->request->param('noticeid');

        return json($this->indexLogic->noticeDesc($noticeid));
    }

    /**藏品购买说明
     * @return mixed
     */
    public function cp_gmsm()
    {

        return json(config('site.instructions'));;
    }

}