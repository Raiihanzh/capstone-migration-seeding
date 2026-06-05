<?php

namespace App\Controllers;

use App\Models\ProductModel; 

class Home extends BaseController
{
    protected $productModel;

    function __construct(){
        helper(['form', 'number']);
        $this->productModel = new ProductModel();
    }

    public function index()
    {

    return view('v_home', [
            'products' => $this->productModel->findAll()
        ]);
    }
}