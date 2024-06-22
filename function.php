<?php

// membuat koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "sistokbarang");


function register($data)
{
	global $conn;

	$email = strtolower(stripslashes($data["email"]));
	$password = mysqli_real_escape_string($conn, $data["password"]);
	$konfirmasipassword = mysqli_real_escape_string($conn, $data["konfirmasipassword"]);

	//cek konfirmasi password
	if ($password !== $konfirmasipassword) {
		echo "<script>
		alert('konfirmasi password tidak sesuai!');
		</script>";
		return false;
	}

	// cek user name sudah ada atau belum
	$result = mysqli_query($conn, "SELECT email FROM login WHERE email = '$email'");

	if (mysqli_fetch_assoc($result)) {
		echo "<script>
		alert('username sudah terdaftar');
		</script>";
		return false;
	}

	// enkripsi password
	$password = password_hash($password, PASSWORD_DEFAULT);

	// tambahkan user baru ke database
	mysqli_query($conn, "INSERT INTO login (email,password) VALUES('$email', '$password')");

	return mysqli_affected_rows($conn);
}


if (isset($_POST["submit"])) {

	$namabarang = htmlspecialchars($_POST["namabarang"]);
	$deskripsi = htmlspecialchars($_POST["deskripsi"]);
	$stock = htmlspecialchars($_POST["stock"]);

	// menambah gambar
	$allowed_extension = array('png', 'jpg', 'jpeg');
	$nama = $_FILES['file']['name']; // ngambil nama barang
	$dot = explode('.', $nama);
	$ekstensi = strtolower(end($dot)); // mengambi ekstensinya
	$ukuran = $_FILES['file']['size']; // ukuran gambar
	$file_tmp = $_FILES['file']['tmp_name'];  /// mengambil lokasi filenya

	// penamaan filenya
	$image = md5(uniqid($nama, true) . time()) . '.' . $ekstensi; // menggabungkan nama file yang di enkripsi dengan ekstensinya
	// validasi sudah ada atau belum 
	$cek = mysqli_query($conn, "SELECT * FROM stock WHERE namabarang = '$namabarang'");
	$hitung = mysqli_num_rows($cek);
	if ($hitung < 1) {
		// jika belum ada

		if (in_array($ekstensi, $allowed_extension) === true) {
			// validasi ukurannya
			if ($ukuran < 15000000) {
				move_uploaded_file($file_tmp, 'img/' . $image);

				$result = mysqli_query($conn, "INSERT INTO stock (namabarang, deskripsi, stock, image) VALUES ('$namabarang', '$deskripsi','$stock', '$image')");
				if ($result) {
					echo "<script>
        alert('Barang berhasil di tambahkan');
        document.location.href = 'index.php';
        </script>
        ";
				} else {
					echo "<script>
        alert('Barang gagal di tambahkan');
        document.location.href = 'index.php';
        </script>
        ";
				}
			} else {
				// kalau filenya lebih dari 15 mb
				echo "<script>
        alert('Ukuran Foto Terlalu Besar');
        document.location.href = 'index.php';
        </script>
        ";
			}
		} else {
			// kalau filenya tidak png/jpg
			echo "<script>
        alert('File Harus Png/jpg');
        document.location.href = 'index.php';
        </script>
        ";
		}
	} else {
		// jika sudah ada
		echo "<script>
        alert('Barang Sudah Terdaftar');
        document.location.href = 'index.php';
        </script>
        ";
	}
}

// menambah barang masuk

if (isset($_POST["barangmasuk"])) {
	$namabarang = $_POST["namabarang"];
	$penerima = $_POST["penerima"];
	$qty = $_POST["qty"];

	$cekstoksekarang = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang= '$namabarang'");
	$ambildata = mysqli_fetch_array($cekstoksekarang);

	$stoksekarang = $ambildata["stock"];
	$tambahstocksekarangdenganqty = $stoksekarang + $qty;

	$result1 = mysqli_query($conn, "INSERT INTO masuk (idbarang, keterangan, qty) VALUES('$namabarang', '$penerima', '$qty')");
	$result2 = mysqli_query($conn, "UPDATE stock SET stock= '$tambahstocksekarangdenganqty' WHERE idbarang= '$namabarang'");
	if ($result1 && $result2) {
		echo "<script>
        alert('Barang Masuk berhasil di tambahkan');
        document.location.href = 'masuk.php';
        </script>";
	} else {
		echo "<script>
        alert('Barang Masuk gagal di tambahkan');
        document.location.href = 'masuk.php';
        </script>";
	}
}


// menambah barang keluar

if (isset($_POST["barangkeluar"])) {
	$namabarang = $_POST["namabarang"];
	$penerima = $_POST["penerima"];
	$qty = $_POST["qty"];

	$cekstoksekarang = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang= '$namabarang'");
	$ambildata = mysqli_fetch_array($cekstoksekarang);

	$stoksekarang = $ambildata["stock"];

	if ($stoksekarang >= $qty) {
		// kalau barangnya cukup 
		$tambahstocksekarangdenganqty = $stoksekarang - $qty;

		$rslt1 = mysqli_query($conn, "INSERT INTO `keluar` (idbarang, penerima, qty) VALUES('$namabarang', '$penerima', '$qty')");
		$rslt2 = mysqli_query($conn, "UPDATE stock SET stock= '$tambahstocksekarangdenganqty' WHERE idbarang= '$namabarang'");
		if ($rslt1 && $rslt2) {
			echo "<script>
        alert('Barang Keluar berhasil di tambahkan');
        document.location.href = 'keluar.php';
        </script>";
		} else {
			echo "<script>
        alert('Barang Keluar gagal di tambahkan');
        document.location.href = 'keluar.php';
        </script>";
		}
		// kalau baragnya tidak cukup
	} else {
		echo "<script>
        alert('Stock Saat Ini Tidak Mencukupi');;
        document.location.href = 'keluar.php';
        </script>";
	}
}



// update info barang
if (isset($_POST["updatebarang"])) {
	$idbarang = $_POST["idbarang"];
	$namabarang = $_POST["namabarang"];
	$deskripsi = $_POST["deskripsi"];

	// menambah gambar
	$allowed_extension = array('png', 'jpg', 'jpeg');
	$nama = $_FILES['file']['name']; // ngambil nama barang
	$dot = explode('.', $nama);
	$ekstensi = strtolower(end($dot)); // mengambi ekstensinya
	$ukuran = $_FILES['file']['size']; // ukuran gambar
	$file_tmp = $_FILES['file']['tmp_name']; /// mengambil lokasi filenya

	// penamaan filenya
	$image = md5(uniqid($nama, true) . time()) . '.' . $ekstensi; // menggabungkan nama file yang di enkripsi dengan ekstensinya

	if ($ukuran == 0) {
		// jika tidak ingin upload

		$result = "UPDATE stock set namabarang= '$namabarang', deskripsi= '$deskripsi' WHERE idbarang = '$idbarang'";
		$update = mysqli_query($conn, $result);
		if ($update) {
			echo "<script>
        alert('Barang berhasil di edit');
        document.location.href = 'index.php';
        </script>";
		} else {
			echo "<script>
        alert('Barang gagal di edit');
        document.location.href = 'index.php';
        </script>";
		}
	} else {
		// jika ingin
		move_uploaded_file($file_tmp, 'img/' . $image);
		$result = "UPDATE stock set namabarang= '$namabarang', deskripsi= '$deskripsi', image= '$image' WHERE idbarang = '$idbarang'";
		$update = mysqli_query($conn, $result);
		if ($update) {
			echo "<script>
        alert('Barang berhasil di edit');
        document.location.href = 'index.php';
        </script>";
		} else {
			echo "<script>
        alert('Barang gagal di edit');
        document.location.href = 'index.php';
        </script>";
		}
	}
}


// menghapus barang
if (isset($_POST["deletebarang"])) {
	$idbarang = $_POST["idbarang"];

	$gambar = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idbarang'");
	$get = mysqli_fetch_array($gambar);
	$img = 'img/' . $get["image"];
	unlink($img);

	$result = "DELETE FROM stock WHERE idbarang = '$idbarang'";
	$hapus = mysqli_query($conn, $result);

	if ($hapus) {
		echo "<script>
        alert('Barang berhasil di hapus');
        document.location.href = 'index.php';
        </script>";
	} else {
		echo "<script>
        alert('Barang gagal di hapus');
        document.location.href = 'index.php';
        </script>";
	}
}

// mengubah data barang masuk
if (isset($_POST["updatebarangmasuk"])) {
	$idm = $_POST["idmasuk"];
	$idb = $_POST["idbarang"];
	$keterangan = $_POST["keterangan"];
	$qty = $_POST["qty"];

	$lihatstok = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
	$stoknya = mysqli_fetch_array($lihatstok);
	$stoksekarang = $stoknya["stock"];

	$lihatqty = mysqli_query($conn, "SELECT * FROM masuk WHERE idmasuk = '$idm'");
	$qtynya = mysqli_fetch_array($lihatqty);
	$qtysekarang = $qtynya["qty"];

	if ($qty > $qtysekarang) {
		$selisih = $qty - $qtysekarang;
		$kurangin = $stoksekarang + $selisih;
		$kuranginstoknya =  mysqli_query($conn, "UPDATE stock SET stock = '$kurangin' WHERE idbarang = '$idb'");
		$updatenya = mysqli_query($conn, "UPDATE masuk SET qty = '$qty', keterangan = '$keterangan' WHERE idmasuk = '$idm'");
		if ($kuranginstoknya && $updatenya) {
			echo "<script>
        alert('Barang Masuk berhasil di edit');
        document.location.href = 'masuk.php';
        </script>";
		} else {
			echo "<script>
        alert('Barang Masuk gagal di edit');
        document.location.href = 'masuk.php';
        </script>";
		}
	} else {
		$selisih = $qtysekarang - $qty;
		$kurangin = $stoksekarang - $selisih;
		$kuranginstoknya =  mysqli_query($conn, "UPDATE stock SET stock = '$kurangin' WHERE idbarang = '$idb'");
		$updatenya = mysqli_query($conn, "UPDATE masuk SET qty = '$qty', keterangan = '$keterangan' WHERE idmasuk = '$idm'");
		if ($kuranginstoknya && $updatenya) {
			echo "<script>
        alert('Barang Masuk berhasil di edit');
        document.location.href = 'masuk.php';
        </script>";
		} else {
			echo "<script>
        alert('Barang Masuk gagal di edit');
        document.location.href = 'masuk.php';
        </script>";
		}
	}
}


// menghapus barang masuk

if (isset($_POST["deletebarangmasuk"])) {
	$idb = $_POST["idbarang"];
	$qty = $_POST["qty"];
	$idm = $_POST["idmasuk"];

	$getdatastok = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
	$data = mysqli_fetch_array($getdatastok);
	$stok = $data["stock"];

	$selisih = $stok - $qty;

	$update = mysqli_query($conn, "UPDATE stock SET stock = '$selisih' WHERE idbarang = '$idb'");
	$hapus = mysqli_query($conn, "DELETE FROM masuk WHERE idmasuk = '$idm'");

	if ($update && $hapus) {
		echo "<script>
        alert('Barang Masuk berhasil di hapus');
        document.location.href = 'masuk.php';
        </script>";
	} else {
		echo  "<script>
        alert('Barang Masuk gagal di hapus');
        document.location.href = 'masuk.php';
        </script>";
	}
}



// mengubah data barang keluar
if (isset($_POST["updatebarangkeluar"])) {
	$idk = $_POST["idkeluar"];
	$idb = $_POST["idbarang"];
	$penerima = $_POST["penerima"];
	$qty = $_POST["qty"];

	$lihatstok = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
	$stoknya = mysqli_fetch_array($lihatstok);
	$stoksekarang = $stoknya["stock"];

	$lihatqty = mysqli_query($conn, "SELECT * FROM keluar WHERE idkeluar = '$idk'");
	$qtynya = mysqli_fetch_array($lihatqty);
	$qtysekarang = $qtynya["qty"];

	if ($stoksekarang >= $qty) {

		if ($qty > $qtysekarang) {
			$selisih = $qty - $qtysekarang;
			$kurangin = $stoksekarang - $selisih;
			$kuranginstoknya =  mysqli_query($conn, "UPDATE stock SET stock = '$kurangin' WHERE idbarang = '$idb'");
			$updatenya = mysqli_query($conn, "UPDATE keluar SET qty = '$qty', penerima = '$penerima' WHERE idkeluar = '$idk'");
			if ($kuranginstoknya && $updatenya) {
				echo "<script>
        alert('Barang Masuk berhasil di edit');
        document.location.href = 'keluar.php';
        </script>";
			} else {
				echo "<script>
        alert('Barang Masuk gagal di edit');
        document.location.href = 'keluar.php';
        </script>";
			}
		} else {
			$selisih = $qtysekarang - $qty;
			$kurangin = $stoksekarang + $selisih;
			$kuranginstoknya =  mysqli_query($conn, "UPDATE stock SET stock = '$kurangin' WHERE idbarang = '$idb'");
			$updatenya = mysqli_query($conn, "UPDATE keluar SET qty = '$qty', penerima = '$penerima' WHERE idkeluar = '$idk'");
			if ($kuranginstoknya && $updatenya) {
				echo "<script>
        alert('Barang Masuk berhasil di edit');
        document.location.href = 'keluar.php';
        </script>";
			} else {
				echo "<script>
        alert('Barang Masuk gagal di edit');
        document.location.href = 'keluar.php';
        </script>";
			}
		}
	} else {
		echo "<script>
        alert('Stock Saat ini Tidak Cukup');
        document.location.href = 'keluar.php';
        </script>";
	}
}


// menghapus barang keluar

if (isset($_POST["deletebarangkeluar"])) {
	$idb = $_POST["idbarang"];
	$qty = $_POST["qty"];
	$idk = $_POST["idkeluar"];

	$getdatastok = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idb'");
	$data = mysqli_fetch_array($getdatastok);
	$stok = $data["stock"];

	$selisih = $stok + $qty;

	$update = mysqli_query($conn, "UPDATE stock SET stock = '$selisih' WHERE idbarang = '$idb'");
	$hapus = mysqli_query($conn, "DELETE FROM keluar WHERE idkeluar = '$idk'");

	if ($update && $hapus) {
		echo "<script>
        alert('Barang Keluar berhasil di hapus');
        document.location.href = 'keluar.php';
        </script>";
	} else {
		echo  "<script>
        alert('Barang Keluar gagal di hapus');
        document.location.href = 'keluar.php';
        </script>";
	}
}

// meminjam barang
if (isset($_POST["pinjam"])) {
	$idbarang = $_POST["barang"]; // mengambil id
	$qty = $_POST["qty"];
	$peminjam = $_POST["peminjam"];

	// stok saat ini
	$stocksaatini = mysqli_query($conn, "SELECT *  FROM stock WHERE idbarang = '$idbarang'");
	$stock = mysqli_fetch_assoc($stocksaatini);
	$barang = $stock["stock"];

	if ($barang >= $qty) {

		// kurangin stocknya
		$stocknya = $barang - $qty;

		$result = mysqli_query($conn, "INSERT INTO peminjaman (idbarang, qty, peminjam) VALUES ('$idbarang', '$qty', '$peminjam')");

		$kurangin = mysqli_query($conn, "UPDATE stock SET stock = '$stocknya' WHERE idbarang = '$idbarang'");

		if ($result && $kurangin) {
			echo "<script>
        alert('Berhasil');
        document.location.href = 'peminjaman.php';
        </script>";
		} else {
			echo "<script>
        alert('Gagal');
        document.location.href = 'peminjaman.php';
        </script>";
		}
	} else {
		echo "<script>
        alert('Stock Saat Ini Tidak Mencukupi');
        document.location.href = 'peminjaman.php';
        </script>";
	}
}

// menyelesaikan pinjaman
if (isset($_POST["selesaipinjam"])) {
	$idpeminjam = $_POST["idpeminjam"];
	$idbarang = $_POST["idbarang"];

	$update = mysqli_query($conn, "UPDATE peminjaman SET status = 'Kembali' WHERE idpeminjam = '$idpeminjam'");

	// stok saat ini
	$stocksaatini = mysqli_query($conn, "SELECT *  FROM stock WHERE idbarang = '$idbarang'");
	$stock = mysqli_fetch_assoc($stocksaatini);
	$barang = $stock["stock"];

	// stok qty dari tabel peminjaman
	$stocksaatini1 = mysqli_query($conn, "SELECT *  FROM peminjaman WHERE idpeminjam = '$idpeminjam'");
	$stock1 = mysqli_fetch_assoc($stocksaatini1);
	$barang1 = $stock1["qty"];

	// kurangin stocknya
	$stocknya = $barang + $barang1;

	// kembalikan stocknya
	$kembalikan = mysqli_query($conn, "UPDATE stock SET stock = '$stocknya' WHERE idbarang = '$idbarang'");

	if ($update && $kembalikan) {
		echo "<script>
        alert('Berhasil');
        document.location.href = 'peminjaman.php';
        </script>";
	} else {
		echo "<script>
        alert('Gagal');
        document.location.href = 'peminjaman.php';
        </script>";
	}
}
