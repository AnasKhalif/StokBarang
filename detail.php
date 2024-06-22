<?php 
session_start();

if( !isset($_SESSION["login"]) ) {
    header("Location: login.php");
    exit;
}

require 'function.php';

// mendapatakan Id
$idbarang = $_GET['id'];

// ambil data dari database
$get = mysqli_query($conn, "SELECT * FROM stock WHERE idbarang = '$idbarang'");
$fetch = mysqli_fetch_array($get);

// set variabel
$namabarang = $fetch["namabarang"];
$deskripsi = $fetch["deskripsi"];
$stock = $fetch["stock"];
$image = $fetch["image"];

// ada gambar atau tidak
$gambar = $fetch["image"]; // ambil gambar
if( $gambar == null ) {
// jika tidak ada gambar
    $img = "No Photo";
} else {
// jika ada gambar
    $img = '<img src="img/'.$gambar.'" class="zoomable">';
}

// generate QR

$url = 'http://localhost/si-stokbarang/view.php?id='.$idbarang;
$qrcode = 'https://chart.googleapis.com/chart?chs=350x350&cht=qr&chl='.$url.'&choe=UTF-8';

 
 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - Detail Barang</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
        <style>
            .zoomable {
                width: 350px;
                height: 350px;
            }
            .zoomable:hover{
                transform: scale(1.5);
                transition: 0.3s ease;
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php">si-stokbarang</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="logout.php" onclick=" return confirm('yakin?')">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Stok Barang
                            </a>
                            <a class="nav-link" href="masuk.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Barang Masuk
                            </a>
                            <a class="nav-link" href="keluar.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Barang Keluar
                            </a>
                            <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Layouts
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html">Static Navigation</a>
                                    <a class="nav-link" href="layout-sidenav-light.html">Light Sidenav</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Pages
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                        Authentication
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="login.php">Login</a>
                                            <a class="nav-link" href="register.php">Register</a>
                                        </nav>
                                    </div>
                                    <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    </div>
                                </nav>
                            </div>
                            <div class="sb-sidenav-menu-heading">Addons</div>
                            <a class="nav-link" href="charts.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Charts
                            </a>
                            <a class="nav-link" href="tables.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Tables
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Start Bootstrap
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Detail Barang</h1>
                        <div class="card mb-4" mt-4>
                            <div class="card-header">
                                <h2><?= $namabarang; ?></h2>
                                <?= $img; ?> 
                                <img src="<?= $qrcode; ?>">
                            </div>
                            <div class="card-body">

                            <div class="row">
                                <div class="col" md-3>Deskripsi</div>
                                <div class="col" md-9>: <?= $deskripsi; ?></div>
                            </div>

                            <div class="row">
                                <div class="col" md-3>Stock</div>
                                <div class="col" md-9>: <?= $stock; ?></div>
                            </div>

                            <br><br><hr>

                            <h3>Barang Masuk</h3>
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tangggal</th>
                                            <th>Keterangan</th>
                                            <th>Jumlah</th>
                
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                    
                                        $ambildatamasuk = mysqli_query($conn, "SELECT * FROM masuk WHERE idbarang = '$idbarang'");
                                        $i = 1;
                                        while($fetch = mysqli_fetch_array($ambildatamasuk) ) { 
                                        $tanggal = $fetch["tanggal"];
                                        $keterangan = $fetch["keterangan"];
                                        $qty = $fetch["qty"]
                            
                                         ?>
                                        <tr>
                                            <td><?= $i++; ?></td>
                                            <td><?= $tanggal; ?></td>
                                            <td><?= $keterangan; ?></td>
                                            <td><?= $qty; ?></td>&nbsp;
                                        </tr>

                                        <?php 
                                         };

                                         ?>
                                    </tbody>
                                </table>

                                <br><br>

                            <h3>Barang Keluar</h3>
                                <table id="datatablesSimple" class="table  table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tangggal</th>
                                            <th>Penerima</th>
                                            <th>Jumlah</th>
                
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                    
                                        $ambildatakeluar = mysqli_query($conn, "SELECT * FROM keluar WHERE idbarang = '$idbarang'");
                                        $i = 1;
                                        while($fetch = mysqli_fetch_array($ambildatakeluar) ) { 
                                        $tanggal = $fetch["tanggal"];
                                        $penerima = $fetch["penerima"];
                                        $qty = $fetch["qty"]
                            
                                         ?>
                                        <tr>
                                            <td><?= $i++; ?></td>
                                            <td><?= $tanggal; ?></td>
                                            <td><?= $penerima; ?></td>
                                            <td><?= $qty; ?></td>&nbsp;
                                        </tr>

                                        <?php 
                                         };

                                         ?>
                                    </tbody>
                                </table>    

                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2022</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script> 

        <!-- Datatables -->
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
       
    </body>    

     <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="" method="post" enctype="multipart/form-data">
      <div class="modal-body">
      <input type="text" name="namabarang" placeholder="Nama Barang" class="form-control" required>
      <br>
      <input type="text" name="deskripsi" placeholder="Deskripsi" class="form-control" required>
      <br>
      <input type="number" name="stock" class="form-control" placeholder="Stok" required>
      <br>
      <input type="file" name="file" class="form-control">
      <br>
      <button type="submit" class="btn btn-primary" name="submit">Submit</button>
      </div>
      </form>

    </div>
  </div>
</div>
</html>
