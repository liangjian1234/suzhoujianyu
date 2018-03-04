<?php

namespace App\Http\Controllers\Home;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    //
    public function index(Request $request)
    {
        dd(count(session('order_listsddd')));
        dd(session('order_lists'));
        return view('home.index');
    }

    public function search(Request $request)
    {
        $search = $request->get('s');
        $order_lists = $request->session()->get('order_lists') ?? [];
        if ($search) {
            $books = Book::whereRaw("(status='A' or status='C') and (locate('$search',number) > 0 or locate('$search',name) > 0 or locate('$search',author) > 0)")->simplePaginate(20);
        } else {
            $books = Book::where('status', 'A')->orWhere('status', 'C')->orderBy('created_at', 'desc')->simplePaginate(20);;
        }
        if (!empty($books)) {
            foreach ($books as $book) {
                if (in_array($book->id, $order_lists)) {
                    $book->add = 1;
                } else {
                    $book->add = 0;
                }
                !empty($book->image) ?: $book->image = 'noimage.jpg';
            }
        }
//        dd($books);
        return view('home.search', compact('books', 'search'));
    }

    public function order(Request $request)
    {
        $order_lists = $request->session()->get('order_lists');
        if($order_lists){
            $orders = Book::where('status', '!=', 'D')->whereIn('id', $order_lists)->get();
        }else{
            $orders = collect([]);
        }
        $search = null;
        return view('home.order', compact('search', 'orders'));
    }

    public function record(Request $request)
    {
        $search = $request->get('s');
        $orders = collect([]);
        if($search){
            $orders = Order::selectRaw("book_order.id,book_order.person_id,book_order.person_name,book_order.created_at,group_concat(order_item.book_id) as book_ids")
                ->leftJoin("order_item","book_order.id","=","order_item.order_id")
                ->whereRaw("book_order.person_id='$search' and order_item.status='A'")
                ->groupBy("book_order.id")
                ->orderBy("book_order.created_at","desc")
                ->paginate(10);
            foreach($orders as $order){
                $book_ids = explode(',',$order->book_ids);
                $order->books=Book::whereIn('id',$book_ids)->get();
            }
        }
        return view('home.record', compact('search','orders'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'person_id' => 'required|min:1',
            'person_name' => 'required|min:1'
        ]);

        $order = new Order();
        $orderId = date('YmdHis') . rand(12345, 98765);
        $order->id = $orderId;
        $order->person_id = $request->post('person_id');
        $order->person_name = $request->post('person_name');
        $order->save();

        $order_lists = $request->session()->get('order_lists');
        $lists = [];
        foreach($order_lists as $list){
            $lists[] = [
                'order_id' => $orderId,
                'book_id'  => $list
                ];
        }
        DB::table('order_item')->insert($lists);
        $request->session()->forget('order_lists');
        return redirect()->route('myrecord',['s'=>$request->post('person_id')]);
    }

    public function add(Request $request, $id)
    {
        if ($request->session()->exists('order_lists')) {
            $orders = $request->session()->get('order_lists');
            if (!in_array($id, $orders)) {
                $request->session()->push('order_lists', $id);
            }
        } else {
            $request->session()->put('order_lists', [$id]);
        }
        return $this->responseJson('10000', count($request->session()->get('order_lists')));
    }

    public function delete(Request $request, $id)
    {
        $orders = $request->session()->get('order_lists');
        foreach ($orders as $key => $value) {
            if ($value === $id) {
                unset($orders[$key]);
            }
        }
        $request->session()->put('order_lists', $orders);
        return $this->responseJson('10000', count($request->session()->get('order_lists')));
    }
}
