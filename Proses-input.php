<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Menerima data dari formulir HTML
    $nama = $_POST['nama'];
    
    // Ganti dengan token akses Anda yang valid
    $token = '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';

    // Ganti dengan path file yang benar di repositori Anda
    $file_path = 'Contoh.sql';

    // Koneksi ke GitHub API
    $username = 'Inchayiq12345';
    $repository = 'Barangmurah';
    $api_url = 'https://api.github.com/repos/'.$username.'/'.$repository.'/contents/'.$file_path;

    // Ambil konten file yang ada di repositori
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: token '.$token,
        'User-Agent: PHP'
    ));
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Pastikan permintaan berhasil
    if ($http_code == 200) {
        // Dekode konten file
        $file_content = json_decode($response, true);

        // Dekode isi file yang sudah ada
        $content = base64_decode($file_content['content']);
        
        // Tambahkan data baru ke dalam isi file
        $data_sql = "INSERT INTO `Contoh` (`User`) VALUES ('$nama');";
        $content .= PHP_EOL . $data_sql;

        // Menyiapkan data untuk dikirim
        $data = array(
            'message' => 'Updating data from form',
            'content' => base64_encode($content),
            'sha' => $file_content['sha'],
            'branch' => 'main'
        );

        // Kirim data yang diperbarui kembali ke GitHub
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: token '.$token,
            'User-Agent: PHP',
            'Content-Type: application/json'
        ));
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Tampilkan pesan sesuai dengan kode HTTP
        if ($http_code == 200 || $http_code == 201) {
            echo 'Data updated successfully!';
        } else {
            echo 'Failed to update data. HTTP code: ' . $http_code;
        }
    } else {
        echo 'Failed to fetch file from GitHub. HTTP code: ' . $http_code;
    }
}
?>


