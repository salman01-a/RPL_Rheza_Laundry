<?php
function kirimPesanWA($nomor, $nama, $role = "Customer") {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.fonnte.com/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'target' => "$nomor|$nama|$role",
            'message' => "Halo {name}, pesanan laundry Anda telah selesai. Terima kasih!",
        ),
        CURLOPT_HTTPHEADER => array(
            'Authorization: TJ1jp5F1bi7rvbs4dub9' // Ganti dengan token asli
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
?>
