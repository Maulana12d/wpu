<?php

$conn = mysqli_connect("localhost", "root", "", "latihanphpfirman");

function query($query) {
    global $conn;

    $result = mysqli_query($conn, $query);

    $datas = [];
    while($data = mysqli_fetch_assoc($result)) {
        $datas[] = $data;
    }
    return $datas;
}

function tambah($data) {
    global $conn;

    $nama = htmlspecialchars($data["nama"]);
    $harga = htmlspecialchars($data["harga"]);

    $gambar = upload();
    // cek apakah ada gambar yang di Upload
    if($gambar === false) {
        return false;
    }

    mysqli_query($conn, "INSERT INTO contoh VALUES('', '$nama','$harga', '$gambar')");

    return mysqli_affected_rows($conn);
}

function upload(){
    $fileName = $_FILES["gambar"]["name"];
    $fileSize = $_FILES["gambar"]["size"];
    $fileError = $_FILES["gambar"]["erro"];
    $tmp_name = $_FILES["gambar"]["tmp_name"];

    // cek apakah ada gambar yang diupload
    if($fileError === 4) {
        echo "<script>
        alert('gambar belum diUpload');
        </script>";
        return false;
    }

    // cek format gambar apakah valid
    $formatGambarValid = ["png", "jpg", "jpeg"];
    $formatGambar = explode(".", $fileName);
    $formatGambar = strtolower(end($formatGambar));

    // cek apakah gambar sudah valid
    if(!in_array($formatGambar, $formatGambarValid)) {
        echo "<script>
        alert('gambar tidak valid, pastikan gambar formatNya [png, jpg, jpeg]');
        </script>";
        return false;
    }

    // cek apakah ukuran gambar kurang dari 2mb
    if($fileSize > 200000) {
        echo "<script>
        alert('ukuran gambar besar, pastikan kurang dari 2mb');
        </script>";
        return false;
    }

    // buat variabel baru untuk penamaan gambar dan jadikan unik
    $fileNameNew = uniqid();
    // gabungkan dengan . untuk format ektensi gambar
    $fileNameNew .= ".";
    // gabungkan yang sudah ada . dengan format gambar yang sesuai / sudah valid
    $fileNameNew .= $formatGambar;
    // pindahkan dari tmp_name ke $fileNameNew
    move_uploaded_file($tmp_name, "img/".$fileNameNew);

    return $fileNameNew;
}


function cari($userinput){
    $query = "SELECT * FROM contoh WHERE nama= '$userinput'";

    return query($query);
}

function hapus($id) {
    global $conn;
    mysqli_query($conn, "DELETE FROM contoh WHERE id = '$id'");
    return mysqli_affected_rows($conn);
}

function ubah($data) {
    global $conn;

    $id = $data["id"];
    $nama = htmlspecialchars($data["nama"]);
    $harga = htmlspecialchars($data["harga"]);
    $gambarLama = htmlspecialchars($data["gambarlama"]);

    // cek apakah ada gambar yang di ganti
    if($_FILES["gambar"]["error"] === 4) {
        $gambar = $gambarLama;
    } else {
        $gambar = upload();
    }

    $query = "UPDATE contoh SET nama = '$nama', harga = '$harga', gambar = '$gambar' WHERE id = $id";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
    
}
function register($data){
    global $conn;
    $username = strtolower(stripcslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password"]);
    $password_confirm =  mysqli_real_escape_string($conn, $data["passwordconfirm"]);
    
    $result = mysqli_query($conn,"SELECT username FROM user WHERE username = '$username'");

    // cek username apakah sudah ada atu belum di database
    
    if (mysqli_fetch_assoc($result)){
        echo "<script>
        alert('username sudah ada silakan buat username baru ');
        </script>";
        return false;

    }

    // cek konfirmasi password 
    if ($password !== $password_confirm){
        echo "<script>
        alert('password tidak sama');
        </script>";
        return false;
    }
    // has password

    $passwordEncrypt= password_hash($password, PASSWORD_DEFAULT);

    // tambahkan ke data base 
    mysqli_query($conn, "INSERT INTO user VALUES ('', '$username', '$passwordEncrypt')");
    return mysqli_affected_rows($conn);
}
function logout ($data){
    global $conn;
    
}
