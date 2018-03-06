<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\Export;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function import(Request $request)
    {
        if ($request->isMethod('post')) {
            $file = $request->file('import');
            if ($file) {
                $extension = $file->extension();
                $name = date('YmdHis', time()) . rand(12345, 98765);
                $newName = $name . "." . $extension;
                $file->storeAs('/imports', $newName);
                return $this->responseJson(10000, compact('name', 'extension'));
            } else {
                $idCardFrontImg = '';
                return json_encode($idCardFrontImg);
            }
        }
    }

    public function store(Request $request)
    {
        ini_set('max_execution_time', '0');
        $name = $request->get('name');
        $extension = $request->get('extension');
        $type = $request->get('type');

        $path = public_path("/storage/imports" . "/$name.$extension");
        $data = [];
        Excel::load($path, function ($reader) use (&$data) {
            $reader->ignoreEmpty();
            $reader->formatDates(true, 'Y-m');
            $data = $reader->toArray();
        });

        DB::beginTransaction();
        try {
            if(count($data[0])!=7){
                throw new \Exception('Excel数据条目有误！');
            }
            unset($data[0]);
            if(empty($data)){
                throw new \Exception('Excel数据为空！');
            }
            $kname = array('number', 'name', 'author', 'price', 'publish_year', 'publish_type', 'image', 'created_at', 'updated_at');
            array_walk($data, 'combine_books', $kname);

            if($type==1){
                foreach($data as $v){
                    Book::firstOrCreate(['number'=>$v['number']],[
                        'name'=>$v['name'],
                        'author'=>$v['author'],
                        'price'=>$v['price'],
                        'publish_year'=>$v['publish_year'],
                        'publish_type'=>$v['publish_type'],
                        'image'=>$v['image'],
                    ]);
                }
            }else if($type==2){
                Book::where('status','!=','D')->update(['status'=>'D']);
                DB::table('book_book')->insert($data);
            }else{
                throw new \Exception('入库方式有误！');
            }
            DB::commit();
            return $this->responseJson();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseJson(20000, $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'export_type' => 'required',
                'start_date' => 'required|date|before_or_equal:end_date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);
            $type = $request->post('export_type');
            $s_date = $request->post('start_date');
            $e_date = $request->post('end_date');

            $name = date('YmdHis', time()) . rand(12334, 98765);
            if ($type == 1) {
                $order_lists = Order::select('book_order.*', 'book_book.number', 'book_book.name', 'book_book.author', 'book_book.price', 'book_book.publish_year', 'book_book.publish_type')
                    ->leftJoin('order_item', 'book_order.id', '=', 'order_item.order_id')
                    ->leftJoin('book_book', 'book_book.id', '=', 'order_item.book_id')
                    ->whereDate('book_order.created_at', '>=', $s_date)
                    ->whereDate('book_order.created_at', '<=', $e_date)
                    ->orderBy('book_order.created_at', 'asc')
                    ->get();
                if ($order_lists->isEmpty()) {
                    return $this->responseJson(20000, '该时间段内没有订单！');
                }
                $orders = $order_lists->toArray();

                Excel::create($name, function ($excel) use ($s_date, $e_date, $orders) {
                    $excel->setTitle('订单 - 按预定记录排序');
                    $excel->setCreator('Sylar丶')
                        ->setCompany('Advancina');
                    $excel->setDescription(date('Y-m-d H:i:s', time()), '导出，自预定开始日期：' . $s_date . ' 到预定结束日期:' . $e_date . ' 间的所有预定记录');
                    $excel->sheet('sheet1', function ($sheet) use ($orders) {
                        $sheet->setWidth(array(
                            'A' => 10,
                            'B' => 20,
                            'C' => 20,
                            'D' => 20,
                            'E' => 20,
                            'F' => 20,
                            'G' => 20,
                            'H' => 20,
                            'I' => 20,
                            'J' => 20,
                        ));
                        $ods = [];
                        foreach ($orders as $k => $order) {
                            $ods[] = [
                                '编号' => $k + 1,
                                '书号' => $order['number'],
                                '书名' => $order['name'],
                                '作者' => $order['author'],
                                '单价' => $order['price'],
                                '出版年月' => $order['publish_year'],
                                '版别' => $order['publish_type'],
                                '预定人编号' => $order['person_id'],
                                '预定人姓名' => $order['person_name'],
                                '预定时间' => $order['created_at']
                            ];
                        }
                        $sheet->fromArray($ods);
                    });
                })->store('xlsx', storage_path('app/public/exports'));

                $export = new Export();
                $export->number = $name;
                $export->start_date = $s_date;
                $export->end_date = $e_date;
                $export->save();
                $export_id = $export->id;

                $items = [];
                $order_ids = [];
                foreach ($orders as $v) {
                    if (!in_array($v['id'], $order_ids)) {
                        array_push($order_ids, $v['id']);
                        $items[] = [
                            'export_id' => $export_id,
                            'order_id' => $v['id'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                }
                DB::table('export_item')->insert($items);

                Order::whereIn('id', $order_ids)->increment('status');
            } else if ($type == 2) {
                $order_lists = Order::select('book_order.*', 'book_book.id as bid', 'book_book.number', 'book_book.name', 'book_book.author', 'book_book.price', 'book_book.publish_year', 'book_book.publish_type')
                    ->leftJoin('order_item', 'book_order.id', '=', 'order_item.order_id')
                    ->leftJoin('book_book', 'book_book.id', '=', 'order_item.book_id')
                    ->whereDate('book_order.created_at', '>=', $s_date)
                    ->whereDate('book_order.created_at', '<=', $e_date)
                    ->orderBy('book_book.id', 'asc')
                    ->get();
                if ($order_lists->isEmpty()) {
                    return $this->responseJson(20000, '该时间段内没有订单！');
                }
                $orders = $order_lists->toArray();

                Excel::create($name, function ($excel) use ($s_date, $e_date, $orders) {
                    $excel->setTitle('订单 - 按明细排序');
                    $excel->setCreator('Sylar丶')
                        ->setCompany('Advancina');
                    $excel->setDescription(date('Y-m-d H:i:s', time()) . '导出，自预定开始日期：' . $s_date . ' 到预定结束日期:' . $e_date . ' 间的所有预定记录');
                    $excel->sheet('sheet1', function ($sheet) use ($orders) {
                        $sheet->setWidth(array(
                            'A' => 10,
                            'B' => 20,
                            'C' => 20,
                            'D' => 20,
                            'E' => 20,
                            'F' => 20,
                            'G' => 20,
                            'H' => 20,
                            'I' => 20,
                            'J' => 20,
                        ));
                        $ods = [];
                        $seq = 0;
                        foreach ($orders as $k => $order) {
                            $bid = $order['bid'];
                            if (array_key_exists($bid, $ods)) {
                                $ods[$bid]['订购数量'] += 1;
                            } else {
                                $seq +=1;
                                $ods[$bid] = [
                                    '编号' => $seq,
                                    '书号' => $order['number'],
                                    '书名' => $order['name'],
                                    '作者' => $order['author'],
                                    '单价' => $order['price'],
                                    '出版年月' => $order['publish_year'],
                                    '版别' => $order['publish_type'],
                                    '订购数量'=>1
                                ];
                            }
                        }
                        $sheet->fromArray($ods);
                    });
                })->store('xlsx', storage_path('app/public/exports'));

                $export = new Export();
                $export->number = $name;
                $export->start_date = $s_date;
                $export->end_date = $e_date;
                $export->save();
                $export_id = $export->id;

                $items = [];
                $order_ids = [];
                foreach ($orders as $v) {
                    if (!in_array($v['id'], $order_ids)) {
                        array_push($order_ids, $v['id']);
                        $items[] = [
                            'export_id' => $export_id,
                            'order_id' => $v['id'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                }
                DB::table('export_item')->insert($items);

                Order::whereIn('id', $order_ids)->increment('status');

            }
            return $this->responseJson(10000, ['name' => $name, 'ext' => 'xlsx']);
        } else {
            $export_record = Export::orderBy('created_at', 'desc')->take(20)->get();
            return view('admin.order', compact('export_record'));
        }
    }

    public function download_export(Request $request)
    {
        $name = $request->name . '.' . $request->ext;
        $file = public_path("/storage/exports" . "/$name");
        return response()->download($file)->deleteFileAfterSend(true);
    }

    public function record()
    {

    }

}
