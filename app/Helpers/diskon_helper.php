<?php

function hitung_ppn($total_harga)
{
    return round($total_harga * 0.12);
}

function hitung_biaya_admin($total_harga)
{
    if ($total_harga <= 15000000) {
        $persen = 0.5;
    } elseif ($total_harga <= 35000000) {
        $persen = 0.7;
    } else {
        $persen = 0.9;
    }

    return ($persen / 100) * $total_harga;
}

function hitung_kupon($kode_kupon, $total_harga)
{
    $kode = strtoupper(trim($kode_kupon));

    $persen = 0;

    switch ($kode) {
        case 'HEMAT20':
            $persen = 20;
            break;

        case 'HEMAT30':
            $persen = 30;
            break;

        case 'MEMBER25':
            $persen = 25;
            break;

        default:
            $persen = 0;
            break;
    }

    return ($persen / 100) * $total_harga;
}