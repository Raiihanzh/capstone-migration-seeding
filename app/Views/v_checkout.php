<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-6">
        <?= form_open('buy', 'class="row g-3"') ?>

<?= form_hidden('username', session()->get('username')) ?>

<?= form_input([
    'type' => 'hidden', 
    'name' => 'total_harga', 
    'id' => 'total_harga']) ?>

<div class="col-12">
    <?= form_label('Nama', 'nama', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'     => 'nama',
        'id'       => 'nama',
        'class'    => 'form-control',
        'value'    => session()->get('username'),
        'readonly' => true]) ?>
</div>
<div class="col-12">
    <?= form_label('Alamat', 'alamat', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'  => 'alamat',
        'id'    => 'alamat',
        'class' => 'form-control']) ?>
</div> 
<div class="col-12"> 
    <?= form_label('Kelurahan', 'kelurahan', ['class' => 'form-label']) ?>
    <?= form_dropdown('kelurahan', [], '', ['id' => 'kelurahan', 'class' => 'form-control']) ?>
</div>
<div class="col-12"> 
    <?= form_label('Layanan', 'layanan', ['class' => 'form-label']) ?> 
    <?= form_dropdown('layanan', [], '', ['id' => 'layanan', 'class' => 'form-control']) ?>
</div>
<div class="col-12">
    <?= form_label('Ongkir', 'ongkir', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'     => 'ongkir',
        'id'       => 'ongkir',
        'class'    => 'form-control',
        'readonly' => true]) ?>
</div>

<div class="col-12">
    <?= form_label('Kode Kupon', 'kode_kupon', ['class'=>'form-label']) ?>

    <?= form_input([
        'name'=>'kode_kupon',
        'id'=>'kode_kupon',
        'class'=>'form-control',
        'placeholder'=>'Masukkan kode kupon'
    ]) ?>

    <small id="status-kupon"></small>

    <div class="mt-2">
        <span class="badge bg-success">HEMAT20 (20%)</span>
        <span class="badge bg-primary">HEMAT30 (30%)</span>
        <span class="badge bg-warning text-dark">MEMBER25 (25%)</span>
    </div>
</div>

<div class="col-12">
    <?= form_submit(
        'submit',
        'Buat Pesanan',
        ['class' => 'btn btn-primary']) ?>
</div>

<?= form_close() ?> 
    </div>

    <div class="col-lg-6">
        <table class="table">
  <thead>
      <tr>
          <th scope="col">Nama</th>
          <th scope="col">Harga</th>
          <th scope="col">Jumlah</th>
          <th scope="col">Sub Total</th>
      </tr>
  </thead>
  <tbody>
      <?php 
      if (!empty($items)) :
          foreach ($items as $index => $item) :
      ?>
              <tr>
                  <td><?= $item['name'] ?></td>
                  <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                  <td><?= $item['qty'] ?></td>
                  <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
              </tr>
      <?php
          endforeach;
      endif;
      ?>

<tr>
    <td colspan="2"></td>
    <td>Subtotal</td>
    <td><?= number_to_currency($total,'IDR') ?></td>
</tr>

<tr>
    <td colspan="2"></td>
    <td style="color:red;">Diskon Kupon</td>
    <td style="color:red;">
        -<span id="diskon-kupon">Rp0</span>
        <br>
        <small id="persen-kupon"></small>
    </td>
</tr>

<tr>
    <td colspan="2"></td>
    <td>PPN (12%)</td>
    <td><?= number_to_currency($ppn,'IDR') ?></td>
</tr>

<tr>
    <td colspan="2"></td>
    <td>Biaya Admin</td>
    <td><?= number_to_currency($biaya_admin,'IDR') ?></td>
</tr>

<tr class="table-success">
    <td colspan="2"></td>
    <td>
        <strong>Subtotal<br>(+PPN+Admin-Kupon)</strong>
    </td>
    <td>
        <strong>
            <span id="subtotal-akhir"></span>
        </strong>
    </td>
</tr>

<tr>
    <td colspan="2"></td>
    <td>Ongkir</td>
    <td>
        <span id="ongkir-text">Rp0</span>
    </td>
</tr>

<tr class="table-dark">
    <td colspan="2"></td>
    <td><strong>Grand Total<br>(incl. Ongkir)</strong></td>
    <td><strong><span id="total"></span></strong></td>
</tr>

  </tbody>
</table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(document).ready(function() {
   let ongkir = 0;

let subtotal = <?= $total ?>;

let ppn = <?= $ppn ?>;

let biaya_admin = <?= $biaya_admin ?>;

let diskonKupon = 0;

    hitungTotal();

    function hitungTotal(){

    let subtotalAkhir =
        subtotal
        - diskonKupon
        + ppn
        + biaya_admin;

    let grandTotal =
        subtotalAkhir
        + ongkir;

    $("#ongkir").val(ongkir);

    $("#ongkir-text").text(
        "IDR "+ongkir.toLocaleString('id-ID')
    );

    $("#diskon-kupon").text(
        "IDR "+diskonKupon.toLocaleString('id-ID')
    );

    $("#subtotal-akhir").text(
        "IDR "+subtotalAkhir.toLocaleString('id-ID')
    );

    $("#total").text(
        "IDR "+grandTotal.toLocaleString('id-ID')
    );

    $("#total_harga").val(grandTotal);

}

    $('#kelurahan').select2({
        placeholder: 'Cari daerah tujuan',
        minimumInputLength: 3, 
        ajax: {
            url: '<?= site_url('ajax/destinations') ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return data;
            },
            cache: true
        }
    });

    $("#kelurahan").on('change', function () {
        let id_kelurahan = $(this).val();

        $("#layanan").empty();
        ongkir = 0;
        hitungTotal(); 

        $.ajax({
            url: "<?= site_url('ajax/costs') ?>", 
            dataType: "json",
            data: {
                destination: id_kelurahan
            },
            success: function (data) { 
                data.forEach(function (item) {
                    $("#layanan").append(
                        $('<option>', {
                            value: item.cost,
                            text: `${item.description} (${item.service}) : estimasi ${item.etd}`
                        })
                    );
                });
            }
        });
    });

    $("#layanan").on('change', function() {
        ongkir = parseInt($(this).val());
        hitungTotal();
    }); 

    $("#kode_kupon").on("keyup",function(){

    let kode=$(this).val().trim().toUpperCase();

    diskonKupon=0;

    $("#persen-kupon").html("");

    if(kode=="HEMAT20")
    {
        diskonKupon=subtotal*0.20;

        $("#status-kupon").html(
            "<span class='text-success'>✔ Kupon HEMAT20 berhasil digunakan.</span>"
        );

        $("#persen-kupon").html("(20%)");
    }

    else if(kode=="HEMAT30")
    {
        diskonKupon=subtotal*0.30;

        $("#status-kupon").html(
            "<span class='text-success'>✔ Kupon HEMAT30 berhasil digunakan.</span>"
        );

        $("#persen-kupon").html("(30%)");
    }

    else if(kode=="MEMBER25")
    {
        diskonKupon=subtotal*0.25;

        $("#status-kupon").html(
            "<span class='text-success'>✔ Kupon MEMBER25 berhasil digunakan.</span>"
        );

        $("#persen-kupon").html("(25%)");
    }

    else if(kode=="")
    {
        $("#status-kupon").html("");
    }

    else
    {
        $("#status-kupon").html(
            "<span class='text-danger'>✖ Kode kupon tidak valid.</span>"
        );
    }

    hitungTotal();

});

});
</script>

<?= $this->endSection() ?>