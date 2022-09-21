<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use logicmodel\AccountLogic;
use logicmodel\MemberLogic;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use think\Db;
use think\Session;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class DrawRecord extends Backend
{
    
    /**
     * DrawRecord模型对象
     * @var \app\admin\model\DrawRecord
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\DrawRecord;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("statusList", $this->model->getStatusList());
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
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        $teamId = Session::get('team_id');
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }

            //如果发
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $list = $this->model
                    ->with(['currency','users'])
                    ->where($where)
                    ->order($sort, $order)
                    ->paginate($limit);

            foreach ($list as $row) {
                $row->visible(['id','order_num','account','reality_account','type','status','refuse','create_time','bank_name','bank_number','bank_owner','bank_branch','ali_name','ali_number','ali_image','wx_name','wx_number','wx_image']);
                $row->visible(['currency']);
				$row->getRelation('currency')->visible(['name']);
				$row->visible(['users']);
				$row->getRelation('users')->visible(['phone']);
            }
            $account = $this->model
                ->with(['currency','users'])
                ->where($where)
                ->sum('draw_record.account');
            $reality_account = $this->model
                ->with(['currency','users'])
                ->where($where)
                ->sum('draw_record.reality_account');
            $fee_account = bcsub($account,$reality_account,2);
            $result = array("total" => $list->total(), "rows" => $list->items(),'account'=>$account,'reality_account'=>$reality_account,'fee_account'=>$fee_account);

            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     * @param $ids
     * @return \app\admin\model\DrawRecord|array|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pass($ids){
        $info = $this->model->find($ids);
        if(empty($info)) return json(['code'=>0,'msg'=>'提现信息错误']);
        if($info['status'] != 0 )return json(['code'=>0,'msg'=>'当前提现已审核']);
        $result = $this->model->where(['id'=>$ids])->update(['status'=>1]);
        if($result) return json(['code'=>1,'msg'=>'审核成功']);
        return json(['code'=>0,'msg'=>'审核失败']);
    }
    /**
     * 审核拒绝
     * @param $ids
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function refuse($ids){
        if(request()->post()){
            $info = $this->model->where(['id'=>$ids])->find();
            if(empty($info)) return json(['code'=>0,'msg'=>'申请信息错误']);
            if(!in_array($info['status'],[0,3]))      $this->error('记录状态错误');
            $refuse = input('post.refuse');
            Db::startTrans();
            $result = $this->model->where(['id'=>$ids])->update(['refuse'=>$refuse,'status'=>2]);
            if($result <= 0){
                Db::rollback();
                $this->error('操作失败');
            }
            $result = (new AccountLogic())->addAccount($info['uid'],$info['currency_id'],$info['account'],'提现拒绝','提现拒绝');
            if($result){
                Db::commit();
                $this->success('操作成功');
            }
            Db::rollback();
            $this->error('操作失败');
        }
        $this->assign('ids',$ids);
        return $this->fetch();
    }



    public function export()
    {
        if ($this->request->isPost()) {
            set_time_limit(0);
            $search = $this->request->post('search');
            $ids = $this->request->post('ids');
            $filter = $this->request->post('filter');
            $op = $this->request->post('op');
            $columns = $this->request->post('columns');


            $excel = new Spreadsheet();
            $excel->getProperties()
                ->setCreator("FastAdmin")
                ->setLastModifiedBy("FastAdmin")
                ->setTitle("标题")
                ->setSubject("Subject");
            $excel->getDefaultStyle()->getFont()->setName('Microsoft Yahei');
            $excel->getDefaultStyle()->getFont()->setSize(12);
            $excel->getDefaultStyle()->applyFromArray(
                array(
                    'fill' => array(
                        'type' => Fill::FILL_SOLID,
                        'color' => array('rgb' => '000000')
                    ),
                    'font' => array(
                        'color' => array('rgb' => "000000"),
                    ),
                    'alignment' => array(
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'indent' => 1
                    ),
                    'borders' => array(
                        'allborders' => array('style' => Border::BORDER_THIN),
                    )
                ));


            $worksheet = $excel->setActiveSheetIndex(0);
            $worksheet->setTitle('标题');
            $whereIds = $ids == 'all' ? '1=1' : ['draw_record.id' => ['in', explode(',', $ids)]];
            $this->request->get(['search' => $search, 'ids' => $ids, 'filter' => $filter, 'op' => $op]);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $line = 1;
            $list = [];
            $columns_arr = explode(',', $columns);

            unset($columns_arr[4]);
            $columns = implode(",", $columns_arr);
            $columns = "draw_record.id,users.phone,draw_record.order_num,draw_record.account,draw_record.reality_account,draw_record.type,draw_record.status,draw_record.refuse,draw_record.bank_name,draw_record.bank_number,draw_record.bank_owner,draw_record.bank_branch,draw_record.ali_name,draw_record.ali_image,draw_record.wx_name,draw_record.wx_image,draw_record.create_time";
//            print_r($worksheet);
//            exit();
//            print_r($where);
//            exit();
            $sql = $this->model
//                ->with(['role'])
                ->with(['currency', 'users'])
                ->field($columns)
                ->where($where)
                ->where($whereIds)
//                ->fetchSql()
//                ->select();
//            print_r($sql);
//            die;
//                ->buildsql();
//            var_dump($sql);
                ->chunk(100, function ($items) use (&$list, &$line, &$worksheet) {
                    $styleArray = array(
                        'font' => array(
                            'color' => array('rgb' => '000000'),
                            'size' => 12,
                            'name' => 'Verdana'
                        ));
                    $list = $items = collection($items)->toArray();

                    foreach ($items as $key => $v) {
                        foreach ($v as $k => $ele) {
                            $tmparray = explode("_text", $k);
                            if (count($tmparray) > 1) {
                                $items[$key][$tmparray[0]] = $ele;
                                unset($items[$key][$k]);
                            }
                        }
                    }
                    foreach ($items as $index => $item) {
                        $line++;
                        $col = 0;
                        foreach ($item as $field => $value) {
                            if (is_array($value)) {
                                $value = implode($value);
                            }
                            $worksheet->setCellValueByColumnAndRow($col, $line, $value);
                            $worksheet->getStyleByColumnAndRow($col, $line)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                            $worksheet->getCellByColumnAndRow($col, $line)->getStyle()->applyFromArray($styleArray);
                            $col++;
                        }
                    }
                }, 'draw_record.id');

            $first = array_keys($list[0]);
            foreach ($first as $k => $ele) {
                $tmparray = explode("_text", $ele);
                if (count($tmparray) > 1) {
                    unset($first[$k]);
                }
            }

            foreach ($first as $index => $item) {
                $worksheet->setCellValueByColumnAndRow($index, 1, __($item));
            }
            $excel->createSheet();
            // Redirect output to a client’s web browser (Excel2007)
            $title = date("YmdHis");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $objWriter = IOFactory::createWriter($excel, 'Xlsx');
            $objWriter->save('php://output');
            return;
        }
    }
}
