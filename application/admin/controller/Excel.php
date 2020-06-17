<?php
/**
 * Created by PhpStorm.
 * User: JHR
 * Date: 2019/6/17
 * Time: 15:03
 */
namespace app\admin\controller;
use think\Db;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Excel extends Base {

    public function orderList() {

        $param['search'] = input('param.search','');
        $param['status'] = input('param.status','');
        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $param['refund_apply'] = input('param.refund_apply','');
        $page['query'] = http_build_query(input('param.'));

        $where = " `del`=0";
        $order = " ORDER BY `id` DESC";
        $orderby = " ORDER BY `d`.`id` DESC";
        if($param['status'] !== '') {
            $where .= " AND status=" . $param['status'];
        }
        if($param['refund_apply']) {
            $where .= " AND refund_apply=" . $param['refund_apply'];
        }
        if($param['datemin']) {
            $where .= " AND create_time>=" . strtotime(date('Y-m-d 00:00:00',strtotime($param['datemin'])));
        }
        if($param['datemax']) {
            $where .= " AND create_time<=" . strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])));
        }
        if($param['search']) {
            $where .= " AND (pay_order_sn LIKE '%".$param['search']."%' OR tel LIKE '%".$param['search']."%')";
        }
        try {
            $count = Db::query("SELECT count(id) AS total_count FROM mp_order o WHERE " . $where);
            $sql = "SELECT 
`o`.`id`,`o`.`pay_order_sn`,`o`.`trans_id`,`o`.`receiver`,`o`.`tel`,`o`.`address`,`o`.`pay_price`,`o`.`total_price`,`o`.`carriage`,`o`.`create_time`,`o`.`pay_time`,`o`.`refund_apply`,`o`.`status`,`o`.`refund_apply`,`o`.`tracking_num`,`o`.`tracking_name`,`d`.`order_id`,`d`.`goods_id`,`d`.`num`,`d`.`unit_price`,`d`.`goods_name`,`d`.`attr`,`g`.`pics` 
FROM (SELECT * FROM mp_order WHERE " . $where . $order .") `o` 
LEFT JOIN `mp_order_detail` `d` ON `o`.`id`=`d`.`order_id`
LEFT JOIN `mp_goods` `g` ON `d`.`goods_id`=`g`.`id`
" . $orderby;
            $list = Db::query($sql);
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }
        $count = $count[0]['total_count'];
        if($count > 1000) {
            die('<h3>单次导出订单数量不超过1000条</h3>');
        }


        $order_id = [];
        $newlist = [];
        foreach ($list as $v) {
            $order_id[] = $v['id'];
        }
        $uniq_order_id = array_unique($order_id);
        foreach ($uniq_order_id as $v) {
            $child = [];
            foreach ($list as $li) {
                if($li['order_id'] == $v) {
                    $data['id'] = $li['id'];
                    $data['pay_order_sn'] = $li['pay_order_sn'];
                    $data['pay_price'] = $li['pay_price'];
                    $data['trans_id'] = $li['trans_id'];
                    $data['receiver'] = $li['receiver'];
                    $data['tel'] = $li['tel'];
                    $data['address'] = $li['address'];
                    $data['total_price'] = $li['total_price'];
                    $data['carriage'] = $li['carriage'];
                    $data['status'] = $li['status'];
                    $data['refund_apply'] = $li['refund_apply'];
                    $data['create_time'] = date('Y-m-d H:i:s',$li['create_time']);
                    $data['tracking_name'] = $li['tracking_name'];
                    $data['tracking_num'] = $li['tracking_num'];
                    if($li['pay_time']) {
                        $data['pay_time'] = date('Y-m-d H:i:s',$li['pay_time']);
                    }else {
                        $data['pay_time'] = $li['pay_time'];
                    }
                    $data_child['goods_id'] = $li['goods_id'];
                    $data_child['cover'] = unserialize($li['pics'])[0];
                    $data_child['goods_name'] = $li['goods_name'];
                    $data_child['num'] = $li['num'];
                    $data_child['unit_price'] = $li['unit_price'];
                    $data_child['total_price'] = sprintf ( "%1\$.2f",($li['unit_price'] * $li['num']));
                    $data_child['attr'] = $li['attr'];
                    $child[] = $data_child;
                }
            }
            $data['child'] = $child;
            $newlist[] = $data;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('掌控的历史统计');

        $sheet->getColumnDimension('A')->setWidth(12);//ID
        $sheet->getColumnDimension('B')->setWidth(30);//订单号
        $sheet->getColumnDimension('C')->setWidth(30);//微信订单号
        $sheet->getColumnDimension('D')->setWidth(12);//支付金额
        $sheet->getColumnDimension('E')->setWidth(20);//下单时间
        $sheet->getColumnDimension('F')->setWidth(20);//支付时间
        $sheet->getColumnDimension('G')->setWidth(12);//订单状态
        $sheet->getColumnDimension('H')->setWidth(12);//退款状态
        $sheet->getColumnDimension('I')->setWidth(50);//购买商品
        $sheet->getColumnDimension('J')->setWidth(12);//收货人
        $sheet->getColumnDimension('K')->setWidth(15);//手机号
        $sheet->getColumnDimension('L')->setWidth(60);//收货地址
        $sheet->getColumnDimension('M')->setWidth(30);//物流单号
        $sheet->getColumnDimension('N')->setWidth(12);//快递类型
        $sheet->getColumnDimension('O')->setWidth(12);//运费

//        $sheet->getStyle('A:Z')->applyFromArray([
//            'alignment' => [
//                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
//                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
//            ],
//        ]);
//        $sheet->getStyle('A1')->applyFromArray([
//            'alignment' => [
//                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
//            ],
//        ]);
//
//        $sheet->getStyle('C:D')->getNumberFormat()->setFormatCode( \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
//
        $sheet->mergeCells('A1:O1');

        $sheet->setCellValue('A1', '掌控的历订单统计' . date('Y-m-d H:i:s'));

        $sheet->setCellValue('A2', 'ID');
        $sheet->setCellValue('B2', '订单号');
        $sheet->setCellValue('C2', '微信订单号');
        $sheet->setCellValue('D2', '支付金额');
        $sheet->setCellValue('E2', '下单时间');
        $sheet->setCellValue('F2', '支付时间');
        $sheet->setCellValue('G2', '订单状态');
        $sheet->setCellValue('H2', '退款状态');
        $sheet->setCellValue('I2', '购买商品');
        $sheet->setCellValue('J2', '收货人');
        $sheet->setCellValue('K2', '手机号');
        $sheet->setCellValue('L2', '收货地址');
        $sheet->setCellValue('M2', '物流单号');
        $sheet->setCellValue('N2', '快递类型');
        $sheet->setCellValue('O2', '运费');

        $sheet->getStyle('A2:O2')->getFont()->setBold(true);
        $sheet->getStyle('M')->getNumberFormat()->setFormatCode( \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

        $index = 3;

        foreach ($newlist as $v) {
            $sheet->setCellValue('A'.$index, $v['id']);
            $sheet->setCellValue('B'.$index, $v['pay_order_sn']);
            $sheet->setCellValue('C'.$index, $v['trans_id']);
            $sheet->setCellValue('D'.$index, $v['pay_price']);
            $sheet->setCellValue('E'.$index, $v['create_time']);
            $sheet->setCellValue('F'.$index, $v['pay_time']);
            switch ($v['status']) {
                case 0:$status = '待付款';break;
                case 1:$status = '待发货';break;
                case 2:$status = '待收货';break;
                case 3:$status = '已完成';break;
            }

            $sheet->setCellValue('G'.$index, $status);

            switch ($v['refund_apply']) {
                case 0:$refund_apply = '无';break;
                case 1:$refund_apply = '申请中';break;
                case 2:$refund_apply = '已退款';break;
            }

            $sheet->setCellValue('H'.$index, $refund_apply);
            $goods_detail = '';
            foreach ($v['child'] as $child) {
                $goods_detail .= $child['goods_name'] . "(" . $child['attr'] . " x ".$child['num']."); ";
            }

            $sheet->setCellValue('I'.$index, $goods_detail);
            $sheet->setCellValue('J'.$index, $v['receiver']);
            $sheet->setCellValue('K'.$index, $v['tel']);
            $sheet->setCellValue('L'.$index, $v['address']);
            $sheet->setCellValue('M'.$index, $v['tracking_num']);
            $sheet->setCellValue('N'.$index, $v['tracking_name']);
            $sheet->setCellValue('O'.$index, $v['carriage']);
            $index++;
        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
//header(‘Content-Type:application/vnd.ms-excel‘);//告诉浏览器将要输出Excel03版本文件
        header('Content-Disposition: attachment;filename="order'.date('Y-m-d').'.xlsx"');//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');//禁止缓存

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function userList() {
        $param['inviter_id'] = input('param.inviter_id','');
        $param['share_auth'] = input('param.share_auth','');
        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $param['search'] = input('param.search');

        $page['query'] = http_build_query(input('param.'));


        $where = [];

        if($param['inviter_id'] !== '') {
            $where[] = ['inviter_id','=',$param['inviter_id']];
        }

        if($param['share_auth'] !== '') {
            $where[] = ['share_auth','=',$param['share_auth']];
        }

        if($param['datemin']) {
            $where[] = ['create_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['datemin'])))];
        }

        if($param['datemax']) {
            $where[] = ['create_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])))];
        }

        if($param['search']) {
            $where[] = ['nickname|tel','like',"%{$param['search']}%"];
        }

        $order = ['id'=>'DESC'];
        try {
            $count = Db::table('mp_user')
                ->where($where)
                ->count();
            $list = Db::table('mp_user')->where($where)
                ->order($order)->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('掌控的历史用户统计');

        $sheet->getColumnDimension('A')->setWidth(10);//ID
        $sheet->getColumnDimension('B')->setWidth(12);//用户昵称
        $sheet->getColumnDimension('C')->setWidth(10);//性别
        $sheet->getColumnDimension('D')->setWidth(15);//手机号
        $sheet->getColumnDimension('E')->setWidth(12);//分享人数
        $sheet->getColumnDimension('F')->setWidth(12);//消费金额
        $sheet->getColumnDimension('G')->setWidth(12);//分享权限
        $sheet->getColumnDimension('H')->setWidth(25);//注册时间
        $sheet->getColumnDimension('I')->setWidth(30);//openID

        $sheet->mergeCells('A1:O1');

        $sheet->setCellValue('A1', '掌控的历史用户统计' . date('Y-m-d H:i:s'));

        $sheet->setCellValue('A2', 'ID');
        $sheet->setCellValue('B2', '用户昵称');
        $sheet->setCellValue('C2', '性别');
        $sheet->setCellValue('D2', '手机号');
        $sheet->setCellValue('E2', '分享人数');
        $sheet->setCellValue('F2', '消费金额');
        $sheet->setCellValue('G2', '分享权限');
        $sheet->setCellValue('H2', '注册时间');
        $sheet->setCellValue('I2', 'openID');

        $sheet->getStyle('A2:I2')->getFont()->setBold(true);
//        $sheet->getStyle('M')->getNumberFormat()->setFormatCode( \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

        $index = 3;

        foreach ($list as $v) {
            $sheet->setCellValue('A'.$index, $v['id']);
            $sheet->setCellValue('B'.$index, $v['nickname']);
            switch ($v['sex']) {
                case 0:$sex = '未知';break;
                case 1:$sex = '男';break;
                case 2:$sex = '女';break;
                default:$sex = '未知';
            }
            $sheet->setCellValue('C'.$index, $sex);
            $sheet->setCellValue('D'.$index, $v['tel']);
            $sheet->setCellValue('E'.$index, $v['invite_num']);
            $sheet->setCellValue('F'.$index, $v['spend']);
            switch ($v['share_auth']) {
                case 0:$share_auth = '无';break;
                case 1:$share_auth = '有';break;
                default:$share_auth = $v['share_auth'];
            }

            $sheet->setCellValue('G'.$index, $share_auth);
            $sheet->setCellValue('H'.$index, date('Y-m-d H:i:s',$v['create_time']));
            $sheet->setCellValue('I'.$index, $v['openid']);
            $index++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
//header(‘Content-Type:application/vnd.ms-excel‘);//告诉浏览器将要输出Excel03版本文件
        header('Content-Disposition: attachment;filename="user'.date('Y-m-d').'.xlsx"');//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');//禁止缓存

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

    }

    public function fansList() {
        $param['subscribe'] = input('param.subscribe','');
        $param['scene_id'] = input('param.scene_id','');
        $param['datemin'] = input('param.datemin');
        $param['datemax'] = input('param.datemax');
        $param['search'] = input('param.search','');
        $curr_page = input('param.page',1);
        $perpage = input('param.perpage',10);

        $page['query'] = http_build_query(input('param.'));
        $whereUser = [];

        if($param['subscribe'] !== '') {
            $whereUser[] = ['u.subscribe','=',$param['subscribe']];
        }
        if($param['scene_id'] !== '') {
            $whereUser[] = ['u.scene_id','=',$param['scene_id']];
        }
        if($param['datemin']) {
            $whereUser[] = ['u.sub_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['datemin'])))];
        }
        if($param['datemax']) {
            $whereUser[] = ['u.sub_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])))];
        }
        if($param['search']) {
            $whereUser[] = ['u.nickname','like',"%{$param['search']}%"];
        }

        try {
            $list = Db::table('mp_scene_user')->alias('u')
                ->join('mp_scene c','u.scene_id=c.id','left')
                ->where($whereUser)
                ->field('u.*,c.scene_name')
                ->limit(($curr_page-1)*$perpage,$perpage)
                ->order(['u.id'=>'DESC'])
                ->select();
        } catch (\Exception $e) {
            return ajax($e->getMessage(), -1);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('掌控的历史统计');

        $sheet->getColumnDimension('A')->setWidth(10);//ID
        $sheet->getColumnDimension('B')->setWidth(12);//用户昵称
        $sheet->getColumnDimension('C')->setWidth(10);//性别
        $sheet->getColumnDimension('D')->setWidth(25);//关注时间
        $sheet->getColumnDimension('E')->setWidth(25);//取关时间
        $sheet->getColumnDimension('F')->setWidth(20);//场景来源
        $sheet->getColumnDimension('G')->setWidth(12);//状态

        $sheet->mergeCells('A1:O1');

        $sheet->setCellValue('A1', '掌控的历史粉丝统计' . date('Y-m-d H:i:s'));

        $sheet->setCellValue('A2', 'ID');
        $sheet->setCellValue('B2', '用户昵称');
        $sheet->setCellValue('C2', '性别');
        $sheet->setCellValue('D2', '关注时间');
        $sheet->setCellValue('E2', '取关时间');
        $sheet->setCellValue('F2', '场景来源');
        $sheet->setCellValue('G2', '状态');

        $sheet->getStyle('A2:G2')->getFont()->setBold(true);

        $sheet->getStyle('A')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $index = 3;

        foreach ($list as $v) {
            $sheet->setCellValue('A'.$index, $v['id']);
            $sheet->setCellValue('B'.$index, $v['nickname']);
            switch ($v['sex']) {
                case 0:$sex = '未知';break;
                case 1:$sex = '男';break;
                case 2:$sex = '女';break;
                default:$sex = '未知';
            }
            $sheet->setCellValue('C'.$index, $sex);
            $sheet->setCellValue('D'.$index, date('Y-m-d H:i:s',$v['sub_time']));
            if($v['unsub_time']) {
                $unsub_time = date('Y-m-d H:i:s',$v['unsub_time']);
            }else {
                $unsub_time = '';
            }
            $sheet->setCellValue('E'.$index, $unsub_time);
            $sheet->setCellValue('F'.$index, $v['scene_name']);
            switch ($v['subscribe']) {
                case 0:$subscribe = '已取关';break;
                case 1:$subscribe = '已关注';break;
                default:$subscribe = $v['subscribe'];
            }
            $sheet->setCellValue('G'.$index, $subscribe);
            $index++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
//header(‘Content-Type:application/vnd.ms-excel‘);//告诉浏览器将要输出Excel03版本文件
        header('Content-Disposition: attachment;filename="fans'.date('Y-m-d').'.xlsx"');//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');//禁止缓存

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

    }










//    public function demo() {
//        try {
//            $param['datemin'] = input('param.datemin');
//            $param['datemax'] = input('param.datemax');
//            $param['search'] = input('param.search');
//            $page['query'] = http_build_query(input('param.'));
//
//            $where = [];
//            if($param['search']) {
//                $where[] = ['name|device_num','like',"%{$param['search']}%"];
//            }
//
//            if(session('username') !== config('superman')) {
//                $device_id = Db::table('mp_admin')->where('id','=',session('admin_id'))->value('device_id');
//                $device_ids = explode(',',$device_id);
//                $where[] = ['id','in',$device_ids];
//                if(empty($device_id)) {
//                    $list = [];
//                    $total_sum = 0;
//                }else {
//                    $list = Db::table('mp_device')->where($where)->select();
//                    $total_sum = Db::table('mp_device')->where($where)->sum('total_price');
//                }
//            }else {
//                $list = Db::table('mp_device')->where($where)->select();
//                $total_sum = Db::table('mp_device')->where($where)->sum('total_price');
//            }
//
//            if($param['datemin'] || $param['datemax']) {
//                $whereMoney = [
//                    ['status','=',1]
//                ];
//                if($param['datemin']) {
//                    $whereMoney[] = ['pay_time','>=',strtotime(date('Y-m-d 00:00:00',strtotime($param['datemin'])))];
//                }
//                if($param['datemax']) {
//                    $whereMoney[] = ['pay_time','<=',strtotime(date('Y-m-d 23:59:59',strtotime($param['datemax'])))];
//                }
//                $total_sum = 0;
//                foreach ($list as &$vv) {
//                    $mapMoney = $whereMoney;
//                    $mapMoney[] = ['device_num','=',$vv['device_num']];
//                    $vv['total_price'] = Db::table('mp_order')->where($mapMoney)->sum('total_price');
//                    $total_sum += $vv['total_price'];
//                    unset($mapMoney);
//                }
//
//            }
//        } catch(\Exception $e) {
//            die($e->getMessage());
//        }
//
//        $spreadsheet = new Spreadsheet();
//        $sheet = $spreadsheet->getActiveSheet();
//        $sheet->setTitle('掌控的历史统计');
//
//        $sheet->getColumnDimension('A')->setWidth(30);
//        $sheet->getRowDimension('1')->setRowHeight(30);
//        $sheet->getColumnDimension('B')->setWidth(12);
//        $sheet->getColumnDimension('C')->setWidth(12);
//        $sheet->getColumnDimension('D')->setWidth(12);
//        $sheet->getColumnDimension('E')->setWidth(12);
//
//        $sheet->getStyle('A:Z')->applyFromArray([
//            'alignment' => [
//                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
//                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
//            ],
//        ]);
//        $sheet->getStyle('A1')->applyFromArray([
//            'alignment' => [
//                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
//            ],
//        ]);
//
//        $sheet->getStyle('A')->getNumberFormat()->setFormatCode( \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
//        $sheet->getStyle('C:D')->getNumberFormat()->setFormatCode( \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
//
//        $sheet->mergeCells('A1:E1');
//
//        $sheet->setCellValue('A1', '掌控的历史销售统计' . date('Y-m-d H:i:s') . ' 制表人:' . session('username'));
//        $sheet->setCellValue('A2', '设备名');
//        $sheet->setCellValue('B2', '设备号');
//        $sheet->setCellValue('C2', '销售额(元)');
//        $sheet->setCellValue('D2', '单价(元)');
//        $sheet->setCellValue('E2', '余量(包)');
//        $sheet->getStyle('A2:E2')->getFont()->setBold(true);
//
//        $index = 3;
//        foreach ($list as $v) {
//            $sheet->setCellValue('A'.$index, $v['name']);
//            $sheet->setCellValue('B'.$index, $v['device_num']);
//            $sheet->setCellValue('C'.$index, $v['total_price']);
//            $sheet->setCellValue('D'.$index, $v['unit_price']);
//            $sheet->setCellValue('E'.$index, $v['stock']);
//            $index++;
//        }
//
//        $sheet->setCellValue('C'.$index, $total_sum);
//
//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件
////header(‘Content-Type:application/vnd.ms-excel‘);//告诉浏览器将要输出Excel03版本文件
//        header('Content-Disposition: attachment;filename="tissue'.date('Y-m-d').'.xlsx"');//告诉浏览器输出浏览器名称
//        header('Cache-Control: max-age=0');//禁止缓存
//
//        $writer = new Xlsx($spreadsheet);
//        $writer->save('php://output');
//
//    }



}