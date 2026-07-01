<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Services\RajaOngkirService;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class TransaksiController extends BaseController
{
    protected $cart;
    protected $transactionModel;
    protected $transactionDetailModel;

    public function __construct()
    {
        helper(['number', 'form']);
        $this->cart = service('cart');
        $this->transactionModel = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel(); 
    }
    
    public function index()
    {  
        $data = [
            'items' => $this->cart->contents(),
            'total' => $this->cart->total()
        ];

        return view('v_keranjang', $data);
    }

    public function cart_add()
    {
	    $this->cart->insert([
	        'id'      => $this->request->getPost('id'),
	        'qty'     => 1,
	        'price'   => $this->request->getPost('harga'),
	        'name'    => $this->request->getPost('nama'),
	        'options' => [
	        'foto' => $this->request->getPost('foto')
	        ]
	]);
	
	session()->setFlashdata(
	    'success',
	    'Produk berhasil ditambahkan ke keranjang. 
	    <a href="' . base_url('keranjang') . '">Lihat</a>'
	);
	
	return redirect()->to(base_url('/'));
    } 


    public function cart_edit()
{
    $i = 1;
    foreach ($this->cart->contents() as $item) {
        $qty = $this->request->getPost('qty' . $i++);

        $this->cart->update([
            'rowid' => $item['rowid'],
            'qty'   => $qty
        ]);
    }

    session()->setFlashdata(
        'success',
        'Keranjang berhasil diperbarui'
    );

    return redirect()->to(base_url('keranjang'));
}


public function cart_delete($rowid)
{
    $this->cart->remove($rowid);

    session()->setFlashdata(
        'success',
        'Produk berhasil dihapus dari keranjang'
    );

    return redirect()->to(base_url('keranjang'));
}

public function cart_clear()
{
    $this->cart->destroy();

    session()->setFlashdata(
        'success',
        'Keranjang berhasil dikosongkan'
    );

    return redirect()->to(base_url('keranjang'));
}

public function checkout()
{  
    $service = new RajaOngkirService();
    $response = $service->getDestination('semarang');
    $response2 = $service->getCost('64999','65042','1000','jne');

    helper('diskon'); 

    $cartItems = $this->cart->contents();

    // hitung total unit
    $total_unit = 0;
    foreach ($cartItems as $item) {
        $total_unit += $item['qty'];
    }

    // subtotal
    $subtotal = $this->cart->total();

    // hitung diskon
    $diskon = hitung_diskon($total_unit, $subtotal);

    $data = [
        'items'  => $cartItems,
        'total'  => $subtotal,
        'diskon' => $diskon
    ];

    return view('v_checkout', $data);
}
public function destinations()
{
    $search = $this->request->getGet('q'); 

    if (empty($search)) {
        return $this->response->setJSON([
            'results' => []
        ]);
    }

    $service = new RajaOngkirService();
    $response = $service->getDestination($search);

    $results = [];
    $data = $response['data'] ?? [];

    foreach ($data as $item) {
    $results[] = [
        'id'   => $item['id'],
        'text' => $item['label']
    ];
}

    return $this->response->setJSON([
        'results' => $results
    ]);
}

public function costs()
{
    $origin = '64999';
    $destination = $this->request->getGet('destination');
    $weight = '1000';
    $courier = 'jne'; 

    $service = new RajaOngkirService();
    $response = $service->getCost($origin, $destination, $weight, $courier);

    $results = [];
    $data = $response['data'] ?? [];

    foreach ($data as $item) {
        $results[] = [
            'service'     => $item['service'],
            'description' => $item['description'],
            'cost'        => $item['cost'],
            'etd'         => $item['etd']
        ];
    }

    return $this->response->setJSON($results);
}

public function buy()
{ 
    helper('diskon');

    $cartItems = $this->cart->contents();

    if (empty($cartItems)) {
        return redirect()->back();
    }

    $db = \Config\Database::connect();
    $db->transStart(); 

    $subtotal = 0;
    $total_unit = 0;

    foreach ($cartItems as $item) {
        $subtotal += $item['qty'] * $item['price'];
        $total_unit += $item['qty'];
    }

    $diskon = round(hitung_diskon($total_unit, $subtotal));

    $ongkir = (int) $this->request->getPost('ongkir');

    $grand_total = $subtotal - $diskon + $ongkir;

    $transaction = [
        'username'    => $this->request->getPost('username'),
        'alamat'      => $this->request->getPost('alamat'),
        'ongkir'      => $ongkir,
        'total_harga' => $subtotal,
        'diskon'      => $diskon, // ✅ SIMPAN DI SINI
        'grand_total' => $grand_total, // ✅ OPTIONAL tapi bagus
        'status'      => 0, 
    ];

    if (!$this->transactionModel->insert($transaction)) {
        $db->transRollback();
        return redirect()->back()->with('error', 'Gagal membuat transaksi');
    }

    $transactionId = $this->transactionModel->getInsertID();

    foreach ($cartItems as $item) {
        $this->transactionDetailModel->insert([
            'transaction_id' => $transactionId,
            'product_id'     => $item['id'],
            'jumlah'         => $item['qty'],
            'subtotal_harga' => $item['qty'] * $item['price'] 
        ]);
    }

    $db->transComplete();

    if (!$db->transStatus()) {
        return redirect()->back()->with('error', 'Gagal membuat transaksi');
    }

    $this->cart->destroy();
    return redirect()->to(base_url());
}

public function history()
{
    $username = session()->get('username'); 
 
    $transactions = $this->transactionModel->where('username', $username)->findAll();
    $transactionIds = array_column($transactions, 'id');

    $products = $this->transactionDetailModel->getProductsByTransactionIds($transactionIds);

    $data = [
        'username'      => $username,
        'transactions'  => $transactions,
        'products'      => $products
    ]; 

    return view('v_history', $data);
}

}
