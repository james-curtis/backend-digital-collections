<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use comservice\Response;
use fast\Tree;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Model;
use comservice\GetRedis;

// require_once('../xasset/index.php');

/**
 *
 *
 * @icon fa fa-circle-o
 */
class Goods extends Backend
{

    /**
     * Goods模型对象
     * @var \app\admin\model\Goods
     */

    protected $relationSearch = true;
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Goods;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("isShowList", $this->model->getIsShowList());
        $this->view->assign("isMangheList", $this->model->getIsMangheList());
        $this->view->assign("isCanBuyList", $this->model->getIsCanBuyList());
        $this->view->assign("isChipList", $this->model->getIsChipList());
        $this->view->assign("fnstatusList", $this->model->getFnstatusList());
        $this->view->assign("hcstatusList", $this->model->getHcstatusList());
    }

    public function import()
    {
        parent::import();
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**藏品编辑
     * @param $ids
     * @return mixed
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if (empty($params['surplus']) or $params['surplus'] < 0) {
                    $params['surplus'] = 0;
                }

//                $params['stock'] = $row['stock'] + $params['surplus'] - $row['surplus'];
                if ($params['stock'] < $params['surplus']) {
                    return json(['code' => 0, 'msg' => "剩余份额不能大于总份额"]);
                }
                $params['sales'] = $params['stock'] - $params['surplus'];

                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if (!empty($params['surplus'])) {

                        $redis = GetRedis::getRedis();
                        $redis_str = 'goods_kc_';
                        if ($row['is_manghe'] == 1) {
                            $redis_str = 'goods_mh_';
                        }
                        $len = $redis->lLen($redis_str . $ids);

                        // 清除库存
                        $redis->lRem($redis_str . $ids, 1, $len);

                        for ($i = 0; $i < $params['surplus']; $i++) {
                            $redis->lpush($redis_str . $ids, 1);
                        }

                    }
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->where(['goods.is_del' => 0])
                ->with(['coupon', 'goodscategory'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id', 'name', 'title', 'image', 'images', 'price', 'type', 'content', 'order', 'start_time', 'end_time', 'stock', 'sales', 'surplus', 'company_name', 'company_image', 'creator', 'owner', 'casting_name', 'casting_time', 'blockchain', 'contract_address', 'is_show', 'coupon_id', 'label', 'goods_category_id', 'is_manghe', 'is_can_buy', 'is_chip', 'chain_state']);

                $row->visible(['coupon']);
                $row->getRelation('coupon')->visible(['name']);

                $row->visible(['goodscategory']);
                $row->getRelation('goodscategory')->visible(['name']);

            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 上链
     * @param $ids
     * @param $force
     * 重新上链
     * @return array|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function slupdate($ids = "", $force = 0)
    {
        $sys_uid = config('site.system_user_id');
        foreach ($ids as $key => $value) {
            $goods = Db::name('goods')->where('id', $value)->find();
            $users = Db::name('users')->where('id', $sys_uid)->find();
            $url = config('site.server_url') . $goods['image'];

            if ($goods['chain_state'] == 0 || $force) {
                $nfsfx = CreateChainNfts($users, $goods['goods_id'], $url);
                if (array_key_exists('error', $nfsfx)) {
                    return Response::fail($nfsfx['error']);
                    if ($nfsfx['error']['code'] == 'INTERNAL_ERROR') {
                        return json(['code' => 0, 'msg' => $users['nick_name'] . '会员账号上链失败：内部服务错误']);
                    }

                    if ($nfsfx['error']['code'] == 'NOT_FOUND') {
                        return json(['code' => 0, 'msg' => $users['nick_name'] . '会员账号上链失败：访问信息不存在或暂时查询不到']);
                    }
                    if ($nfsfx['error']['code'] == 'FORBIDDEN') {
                        return json(['code' => 0, 'msg' => $users['nick_name'] . '会员账号上链失败：无访问权限']);
                    }
                    if ($nfsfx['error']['code'] == 'BAD_REQUEST') {
                        return json(['code' => 0, 'msg' => $users['nick_name'] . '会员账号上链失败：参数错误']);
                    }
                    if ($nfsfx['error']['code'] == 'REQUEST_ERROR') {
                        return json(['code' => 0, 'msg' => $users['nick_name'] . '会员账号上链失败：重复请求']);
                    }
                    if ($nfsfx['error']['code'] == 'STATUS_ERROR') {
                        return json(['code' => 0, 'msg' => $users['nick_name'] . '会员账号上链失败：状态异常']);
                    }
                }
                if (isset($nfsfx['data'])) {
                    $result = $this->model
                        ->where('id', 'in', $value)
                        ->update([
                            'chain_state' => 1,
                            'creator' => $nfsfx['data']['operation_id'],
                            'owner' => $nfsfx['data']['contractAddress'],
                        ]);
                }
            } else {
                return json(['code' => 0, 'msg' => '请选择未上链的数据！']);
            }

        }

        if (isset($result)) return json(['code' => 1, 'msg' => '上链成功']);
        return json(['code' => 0, 'msg' => '上链失败']);
    }

    public function fnindex()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->where(['goods.is_del' => 0])
                ->with(['coupon', 'goodscategory'])
                ->where('is_manghe', 0)
                ->order($sort, $order)
                ->paginate($limit);

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
    }

    public function goodsList()
    {
        return json($this->model->select());
    }

    public function del($ids = "")
    {
        $id_arr = explode(',', $ids);

        foreach ($id_arr as $val) {

            $redis = GetRedis::getRedis();
            $goods_len = $redis->lLen('goods_kc_' . $val);
            $redis->lRem('goods_kc_' . $val, 1, $goods_len);
        }

        $result = $this->model->where(['id' => ['in', $ids]])->update(['is_del' => 1]);
        if ($result) return json(['code' => 1, 'msg' => '删除成功']);
        return json(['code' => 0, 'msg' => '删除失败']);
    }

    public function list()
    {
        return json($this->model->where(['is_del' => 0])->select());
    }

    protected function selectpage()
    {
        //设置过滤方法
        $this->request->filter(['trim', 'strip_tags', 'htmlspecialchars']);

        //搜索关键词,客户端输入以空格分开,这里接收为数组
        $word = (array)$this->request->request("q_word/a");
        //当前页
        $page = $this->request->request("pageNumber");
        //分页大小
        $pagesize = $this->request->request("pageSize");
        //搜索条件
        $andor = $this->request->request("andOr", "and", "strtoupper");
        //排序方式
        $orderby = (array)$this->request->request("orderBy/a");
        //显示的字段
        $field = $this->request->request("showField");
        //主键
        $primarykey = $this->request->request("keyField");
        //主键值
        $primaryvalue = $this->request->request("keyValue");
        $primaryvalue_row = $primaryvalue;
        //搜索字段
        $searchfield = (array)$this->request->request("searchField/a");
        //自定义搜索条件
        $custom = (array)$this->request->request("custom/a");
        //是否返回树形结构
        $istree = $this->request->request("isTree", 0);
        $ishtml = $this->request->request("isHtml", 0);
        if ($istree) {
            $word = [];
            $pagesize = 999999;
        }
        $order = [];
        foreach ($orderby as $k => $v) {
            $order[$v[0]] = $v[1];
        }
        $field = $field ? $field : 'name';

        //如果有primaryvalue,说明当前是初始化传值
        if ($primaryvalue !== null) {
            $where = [$primarykey => ['in', $primaryvalue]];
            $pagesize = 999999;
        } else {
            $where = function ($query) use ($word, $andor, $field, $searchfield, $custom) {
                $logic = $andor == 'AND' ? '&' : '|';
                $searchfield = is_array($searchfield) ? implode($logic, $searchfield) : $searchfield;
                $searchfield = str_replace(',', $logic, $searchfield);
                $word = array_filter(array_unique($word));
                if (count($word) == 1) {
                    $query->where($searchfield, "like", "%" . reset($word) . "%");
                } else {
                    $query->where(function ($query) use ($word, $searchfield) {
                        foreach ($word as $index => $item) {
                            $query->whereOr(function ($query) use ($item, $searchfield) {
                                $query->where($searchfield, "like", "%{$item}%");
                            });
                        }
                    });
                }
                if ($custom && is_array($custom)) {
                    foreach ($custom as $k => $v) {
                        if (is_array($v) && 2 == count($v)) {
                            $query->where($k, trim($v[0]), $v[1]);
                        } else {
                            $query->where($k, '=', $v);
                        }
                    }
                }
            };
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $this->model->where($this->dataLimitField, 'in', $adminIds);
        }
        $list = [];
        $total = $this->model->where($where)->where(['is_del' => 0])->count();
        if ($total > 0) {
            if (is_array($adminIds)) {
                $this->model->where($this->dataLimitField, 'in', $adminIds);
            }

            $fields = is_array($this->selectpageFields) ? $this->selectpageFields : ($this->selectpageFields && $this->selectpageFields != '*' ? explode(',', $this->selectpageFields) : []);

            //如果有primaryvalue,说明当前是初始化传值,按照选择顺序排序
            if ($primaryvalue !== null && preg_match("/^[a-z0-9_\-]+$/i", $primarykey)) {
                $primaryvalue = array_unique(is_array($primaryvalue) ? $primaryvalue : explode(',', $primaryvalue));
                //修复自定义data-primary-key为字符串内容时，给排序字段添加上引号
                $primaryvalue = array_map(function ($value) {
                    return '\'' . $value . '\'';
                }, $primaryvalue);

                $primaryvalue = implode(',', $primaryvalue);

                $this->model->orderRaw("FIELD(`{$primarykey}`, {$primaryvalue})");
            } else {
                $this->model->order($order);
            }

            $datalist = $this->model->where($where)->where(['is_del' => 0])
                ->page($page, $pagesize)
                ->select();

            foreach ($datalist as $index => $item) {
                unset($item['password'], $item['salt']);
                if ($this->selectpageFields == '*') {
                    $result = [
                        $primarykey => isset($item[$primarykey]) ? $item[$primarykey] : '',
                        $field => isset($item[$field]) ? $item[$field] : '',
                    ];
                } else {
                    $result = array_intersect_key(($item instanceof Model ? $item->toArray() : (array)$item), array_flip($fields));
                }
                $result['pid'] = isset($item['pid']) ? $item['pid'] : (isset($item['parent_id']) ? $item['parent_id'] : 0);
                $list[] = $result;
            }
            if ($istree && !$primaryvalue) {
                $tree = Tree::instance();
                $tree->init(collection($list)->toArray(), 'pid');
                $list = $tree->getTreeList($tree->getTreeArray(0), $field);
                if (!$ishtml) {
                    foreach ($list as &$item) {
                        $item = str_replace('&nbsp;', ' ', $item);
                    }
                    unset($item);
                }
            }
        }
        //这里一定要返回有list这个字段,total是可选的,如果total<=list的数量,则会隐藏分页按钮
        $result = $list;
        if ($primaryvalue_row) {
            $result = [];
            foreach (explode(',', $primaryvalue_row) as $cursor_key_value) {
                $filter_arr = array_filter($list, function ($var) use ($primarykey, $cursor_key_value) {
                    return strval($var[$primarykey]) == strval($cursor_key_value);
                });
                $result[] = array_values($filter_arr)[0];
            }
        }
        return json(['list' => $result, 'total' => $total]);
    }


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {

            $params = $this->request->post("row/a");
            $params['surplus'] = $params['stock'];

            if ($params) {

                $params = $this->preExcludeFields($params);
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                if ($params['is_manghe'] == 2) {
                    $params['hcstatus'] = 1;
                    $params['is_can_buy'] = 0;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }

                    $result = $this->model->allowField(true)->save($params);


                    if ($params['is_manghe'] == 0 || $params['is_manghe'] == 2) {
                        if (!empty($params['stock'])) {

                            $redis = GetRedis::getRedis();

                            $goods_id = $this->model->max('id');

                            // 清除库存
                            // $redis ->lRem('kc',1,$params['stock']);

                            for ($i = 0; $i < $params['stock']; $i++) {
                                $redis->lpush('goods_kc_' . $goods_id, 1);
                            }
                        }
                    }

                    if ($params['is_manghe'] == 1) {
                        if (!empty($params['stock'])) {

                            $redis = GetRedis::getRedis();

                            $goods_id = $this->model->max('id');

                            // 清除库存
                            // $redis ->lRem('kc',1,$params['stock']);

                            for ($i = 0; $i < $params['stock']; $i++) {
                                $redis->lpush('goods_mh_' . $goods_id, 1);
                            }
                        }
                    }


                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }


    public function indexs()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = $this->model
                ->where(['goods.is_del' => 0])
                ->where(['goods.is_manghe' => 0])
                ->with(['coupon', 'goodscategory'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id', 'name', 'title', 'image', 'images', 'price', 'type', 'content', 'order', 'start_time', 'end_time', 'stock', 'sales', 'surplus', 'company_name', 'company_image', 'creator', 'owner', 'casting_name', 'casting_time', 'blockchain', 'contract_address', 'is_show', 'coupon_id', 'label', 'goods_category_id', 'is_manghe', 'is_can_buy', 'is_chip']);

                $row->visible(['coupon']);
                $row->getRelation('coupon')->visible(['name']);

                $row->visible(['goodscategory']);
                $row->getRelation('goodscategory')->visible(['name']);

            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }
}
