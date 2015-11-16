<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Controllers\Controller;

use App\Product;

use Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('product/index', [
            'products'      => Product::latest()->paginate(16),
            'recommended'   => Product::take(8)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product/create', ['product' => new Product]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $data = $request->all();

        if ($request->hasFile('img')) {
            
            $file = $request->file('img');
            
            $fileName = time().'-'.$file->getClientOriginalName();
            $file->move('uploads', $fileName);

            $data['image'] = $fileName;


        }

        $p = Product::create($data);


        return redirect('product/manage');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('product/show', [
            'product'       => $product,
            'related'       => Product::take(16)->get(),
            'menjual'       => Product::take(4)->get(),
            'recommended'   => Product::take(8)->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('product/edit', ['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->all());

        return redirect('product/manage');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect('product/manage');
    }

    public function manage()
    {
        return view('product/manage', [
            'products' => Product::all()
        ]);
    }

    public function getList()
    {
        $r              = Request::all();
        
        $sortColumn     = $r['columns'][$r['order'][0]['column']]['data'];
        $sortOrder      = $r['order'][0]['dir'];
        $searchValue    = '%'.$r['search']['value'].'%';

        $products = Product::all();

        $filtered = Product::where('name', 'LIKE', $searchValue)
            ->orWhere('description', 'LIKE', $searchValue)
            ->orWhere('price', 'LIKE', $searchValue)
            ->orderBy($sortColumn, $sortOrder);

        return [
            'draw'              => $r['draw'],
            'recordsTotal'      => count($filtered->get()),
            'recordsFiltered'   => count($filtered->get()),
            'data'              => $filtered->limit($r['length'])
                                        ->offset($r['start'])
                                        ->get()
        ];
    }
}