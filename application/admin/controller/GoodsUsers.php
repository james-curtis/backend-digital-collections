<?php

namespace app\admin\controller;

use app\admin\library\Auth;
use app\common\controller\Backend;
use comservice\Response;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use think\Db;
use think\Exception;
use think\exception\PDOException;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class GoodsUsers extends Backend
{

    /**
     * GoodsUsers模型对象
     * @var \app\admin\model\GoodsUsers
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\GoodsUsers;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("isShowList", $this->model->getIsShowList());
        $this->view->assign("isDelList", $this->model->getIsDelList());
        $this->view->assign("stateList", $this->model->getstateList());
    }

    public function import()
    {
        $file = $this->request->request('file');
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }
        //实例化reader
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
            $this->error(__('Unknown data format'));
        }
        if ($ext === 'csv') {
            $file = fopen($filePath, 'r');
            $filePath = tempnam(sys_get_temp_dir(), 'import_csv');
            $fp = fopen($filePath, "w");
            $n = 0;
            while ($line = fgets($file)) {
                $line = rtrim($line, "\n\r\0");
                $encoding = mb_detect_encoding($line, ['utf-8', 'gbk', 'latin1', 'big5']);
                if ($encoding != 'utf-8') {
                    $line = mb_convert_encoding($line, 'utf-8', $encoding);
                }
                if ($n == 0 || preg_match('/^".*"$/', $line)) {
                    fwrite($fp, $line . "\n");
                } else {
                    fwrite($fp, '"' . str_replace(['"', ','], ['""', '","'], $line) . "\"\n");
                }
                $n++;
            }
            fclose($file) || fclose($fp);

            $reader = new Csv();
        } elseif ($ext === 'xls') {
            $reader = new Xls();
        } else {
            $reader = new Xlsx();
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);
        /**
         * @example
         * Array
         * (
         * [0] => Array
         * (
         * [COLUMN_NAME] => phone
         * [COLUMN_COMMENT] => 手机号
         * )
         * )
         */
        $userTableList = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ? and COLUMN_NAME='phone' limit 1",
            [(new \app\admin\model\Users())->getQuery()->getTable(), $database]);
        $userTablePhoneColumnComment = $userTableList[0]['COLUMN_COMMENT'];
        foreach ($list as $k => $v) {
            if ($importHeadType == 'comment') {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            } else {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }

        $failedList = [];

        Db::startTrans();
        $batchId = uuid() . uuid();
        //加载文件
        $insert = [];
        try {
            if (!$PHPExcel = $reader->load($filePath)) {
                $this->error(__('Unknown data format'));
            }
            $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
            $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
            $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
            $maxColumnNumber = Coordinate::columnIndexFromString($allColumn);
            /**
             * @description excel文件每一列名称
             * @example Array ( [0] => 会员手机号 [1] => 会员ID [2] => 拍品ID )
             */
            $fields = [];
            for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $fields[] = $val;
                }
            }

            $userCacheList = [];
            $goodCacheList = [];
            $goodUserNumberCacheList = [];
            for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
                /**
                 * @description 每一行的值
                 * @example Array ( [0] => 15977774444 [1] => [2] => 96 )
                 */
                $values = [];
                for ($currentColumn = 1; $currentColumn <= $maxColumnNumber; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $values[] = is_null($val) ? '' : $val;
                }
                /**
                 * @description 关联数组 键值对
                 * @examp Array ( [会员手机号] => 15977774444 [会员ID] => [拍品ID] => 96 )
                 */
                $temp = array_combine($fields, $values);

                /**
                 * @description 数据库列名 => 值
                 * @example Array ( [uid] => [goods_id] => 96 )
                 */
                $row = [];
                foreach ($temp as $k => $v) {
                    if (isset($fieldArr[$k]) && $k !== '') {
                        $row[$fieldArr[$k]] = $v;
                    }
                }

                // 优先 `会员ID`
                if (!empty($temp[$userTablePhoneColumnComment]) && empty($row['uid'])) {
                    $phone = $temp[$userTablePhoneColumnComment];
                    if (empty($userCacheList[$phone])) {
                        $currentUser = (\app\admin\model\Users::get(['phone' => $phone]));
                        if (empty($currentUser)) {
                            $failedList[] = [
                                'phone' => $phone,
                                'goods_id' => $row['goods_id'],
                                'batch_id' => $batchId
                            ];
                            continue;
//                            throw new Exception("$phone 不存在");
                        }
                        $userCacheList[$phone] = $currentUser->toArray();
                    }
                    $currentUser = $userCacheList[$phone];


                    $row['uid'] = $currentUser['id'];
                    if (empty($row['goods_id'])) {
                        throw new Exception('拍品ID【goods_id】不能为空');
                    }
                    if (empty($goodCacheList[$row['goods_id']])) {
                        $goodCacheList[$row['goods_id']] = \app\admin\model\Goods::get(['id' => $row['goods_id']])->toArray();
                    }
                    $currentGood = $goodCacheList[$row['goods_id']];
                    $row['price'] = $currentGood['price'];
//                    $row['goods_number'] = uniqueNum();

                    $goods_user_number = '000001';
                    if (empty($goodUserNumberCacheList[$row['goods_id']])) {
                        $goods_user_number = \app\admin\model\GoodsUsers::where(['goods_id' => $row['goods_id']])
                            ->whereNotNull('number')
                            ->order('id', 'desc')
                            ->value('number');
                    } else {
                        $goods_user_number = $goodUserNumberCacheList[$row['goods_id']];
                    }
                    if ($goods_user_number) {
                        $goods_user_number = str_pad(intval($goods_user_number) + 1, 6, '0', STR_PAD_LEFT);
                    } else {
                        $goods_user_number = '000001';
                    }
                    $goodUserNumberCacheList[$row['goods_id']] = $goods_user_number;
                    $row['number'] = $goods_user_number;

                    // 上链
                    if (!empty($temp['上链'])) {
                        $data = CreateChainNfts($currentUser, $currentGood['id'], $currentGood['id']);
                        $row['operation_id'] = $data['data']['operation_id'];
                        $row['contract_address'] = $data['data']['contractAddress'];
                        $row['state'] = 1;
                    }
                }
                $row['create_time'] = datetime(time());

                if ($row) {
                    if ($currentGood['surplus'] <= 0) {
                        throw new Exception("{$currentGood['name']}库存不足");
                    }
                    \datamodel\Goods::where(['id' => $currentGood['id']])->setDec('surplus');
                    \datamodel\Goods::where(['id' => $currentGood['id']])->setInc('sales');
                    --$currentGood['surplus'];
                    ++$currentGood['sales'];
                    $insert[] = $row;
                }
            }
        } catch (\Exception $exception) {
            Db::rollback();
            $this->error($exception->getMessage());
        }
        if (!$insert) {
            Db::rollback();
            $this->error(__('No rows were updated'));
        }

        try {
            //是否包含admin_id字段
            $has_admin_id = false;
            foreach ($fieldArr as $name => $key) {
                if ($key == 'admin_id') {
                    $has_admin_id = true;
                    break;
                }
            }
            if ($has_admin_id) {
                $auth = Auth::instance();
                foreach ($insert as &$val) {
                    if (!isset($val['admin_id']) || empty($val['admin_id'])) {
                        $val['admin_id'] = $auth->isLogin() ? $auth->id : 0;
                    }
                }
            }
            $this->model->saveAll($insert);

            // 保存失败的列表
            $failedModel = new \app\admin\model\GoodsUsersImportFailed();
            $failedModel->where('1=1')->delete();
            $failedModel->saveAll($failedList);
        } catch (PDOException $exception) {
            Db::rollback();
            $msg = $exception->getMessage();
            if (preg_match("/.+Integrity constraint violation: 1062 Duplicate entry '(.+)' for key '(.+)'/is", $msg, $matches)) {
                $msg = "导入失败，包含【{$matches[1]}】的记录已存在";
            };
            $this->error($msg);
        } catch (\Exception $e) {
            Db::rollback();
            $this->error($e->getMessage());
        }
        Db::commit();
        $this->success('本次导入批次：' . $batchId);
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


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
                ->where(['goods_users.is_del' => 0])
                ->where('uid', 'not in', 1)
                ->with(['users', 'goods'])
                ->where($where)
                ->order($sort, $order)
                ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id', 'goods_number', 'price', 'status', 'state', 'is_show', 'create_time', 'order', 'number']);
                $row->visible(['users']);
                $row->getRelation('users')->visible(['phone']);
                $row->visible(['goods']);
                $row->getRelation('goods')->visible(['name']);
            }

            $result = array("total" => $list->total(), "rows" => $list->items());

            return json($result);
        }
        return $this->view->fetch();
    }

    public function del($ids = "")
    {
        $result = $this->model->where(['id' => ['in', $ids]])->update(['is_del' => 1]);
        if ($result) return json(['code' => 1, 'msg' => '删除成功']);
        return json(['code' => 0, 'msg' => '删除失败']);
    }

    //上链
    public function slupdate($ids = "")
    {
        foreach ($ids as $key => $value) {
            $goodsusers = Db::name('goods_users')->where('id', $value)->find();
            $goods = Db::name('goods')->where('id', $goodsusers['goods_id'])->find();
            $users = Db::name('users')->where('id', $goodsusers['uid'])->find();
            $url = config('site.server_url') . $goods['image'];

            if ($users['Nftstatus'] == 0) {
                return json(['code' => 0, 'msg' => '用户账号未上链']);
            }

            if ($goodsusers['state'] == 0 || true) {
                $nfsfx = CreateChainNfts($users, $goodsusers['goods_id'], $url);
                //  print_r($nfsfx);
                //  exit();
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
                            'state' => 1,
                            'operation_id' => $nfsfx['data']['operation_id'],
                            'contract_address' => $nfsfx['data']['contractAddress'],
                        ]);
                }
            } else {
                return json(['code' => 0, 'msg' => '请选择未上链的数据！']);
            }

        }

        if (isset($result)) return json(['code' => 1, 'msg' => '上链成功']);
        return json(['code' => 0, 'msg' => '上链失败']);
    }


    public function add()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = $data['row'];
            $phone = $data['phone'];
            $user = (new \app\admin\model\Users())->where(['phone' => $phone, 'is_del' => 0])->find();
            if (empty($user)) return json(['code' => 0, 'msg' => '会员手机号错误']);
            $goods['goods_number'] = uniqueNum();
            $goods['uid'] = $user['id'];
            $goods['goods_id'] = $data['goods_id'];
            $goods['price'] = $data['price'];
            $goods['order'] = $data['order'];
            $goods['status'] = $data['status'];
            $goods['create_time'] = date('Y-m-d H:i:s');
            $result = $this->model->insertGetId($goods);
            if ($result) return json(['code' => 1, 'msg' => '添加成功']);
            return json(['code' => 0, 'msg' => '添加失败']);
        }
        return $this->fetch();
    }

}
