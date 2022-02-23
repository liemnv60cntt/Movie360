<?php
    session_start();
?>
<!DOCTYPE html>
<?php
    require_once("../connect.php");
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="../styles/tapphimstyle.css">
    <title>Thêm tập phim</title>

</head>
<body class="container-fluid">
    <?php
        include_once("../../DangNhapLocation2.php");
        include_once("../header.html");
    ?> 
    <?php  
        $maPhim = $_GET['maPhim'];
        $sql = "SELECT * FROM phim WHERE MaPhim = '$maPhim'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $tenPhim = $row['TenPhim'];
        echo "<h3>Bạn đang thêm tập phim cho phim $tenPhim</h3>";
    ?>

    <!-- Xử lý dữ liệu post lên -->
    <?php
        $maTapPhim = $soTap = $fileTapPhim = "";

        $maPhim = $maPhim ?? "";

        $error = array();

        $maTapPhimErr = $soTapErr = $fileTapPhimErr = "";

        if (isset($_POST['themTapMoi'])) {
            $maPhim = $_POST['maPhim'];
            $maTapPhim = $_POST['maTapPhim'];
            $soTap = $_POST['soTap'];
            $fileTapPhim = $_FILES['fileTapPhim'];

            $name = $fileTapPhim['name'];
            $type = $fileTapPhim['type'];
            $tmp = $fileTapPhim['tmp_name'];
            $size = $fileTapPhim['size'];
            
            if (empty($maTapPhim)) {
                $maTapPhimErr = "Vui lòng nhập mã tập phim!";
                $error[] = $maTapPhimErr;
            }

            $sql = "SELECT * FROM tapphim WHERE MaTapPhim = '$maTapPhim'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result)) {
                $maTapPhimErr = "Mã này đã tồn tại!";
                $error[] = $maTapPhimErr;
            }

            if (empty($soTap)) {
                $soTapErr = "Vui lòng nhập số tập phim!";
                $error[] = $soTapErr;
            }

            if (empty($name)) {
                $fileTapPhimErr = "Vui lòng tải file phim lên!";
                $error[] = $fileTapPhimErr;
            }

            if (empty($error)) {
                move_uploaded_file($tmp, "../../../Films/" . $name);

                $sql = "INSERT INTO tapphim
                        (MaTapPhim, TapPhim, SoTap, MaPhim)
                        VALUES
                        ('$maTapPhim', '$name', '$soTap','$maPhim')";

                if (mysqli_query($conn, $sql)) {
                    echo "
                        <div class='alert alert-success' role='alert'>
                            Bạn đã thêm thành công tập phim!
                        </div>                    
                    ";
                } else {
                    echo "
                        <div class='alert alert-danger' role='alert'>
                            Đã có sự cố ngoài ý muốn xảy ra!!!
                        </div>                    
                    ";
                }
            }
        }

        if (isset($_POST['xoa'])) {
            $maTapPhim = $_POST['maTapPhim'];
            $maPhim = $_POST['maPhim'];
            $soTap = $_POST['soTap'];

            $sql1 = "DELETE FROM lichsu WHERE MaTapPhim = '$maTapPhim'";
            $sql2 = "DELETE FROM tapphim WHERE MaTapPhim = '$maTapPhim'";

            if (mysqli_query($conn, $sql1) && mysqli_query($conn, $sql2)) {
                echo '<script language="javascript">';
                echo "alert('Bạn đã xóa thành công $soTap')";
                echo '</script>';
            } else {
                echo "alert('Đã có sự cố ngoài ý muốn!:". mysqli_error($conn). "');";
            }
        }

        $maPhim = $_GET['maPhim'];
    ?>

    <br>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" class="form-control" name="maPhim" value="<?php echo $maPhim; ?>">

        <div class="row mb-1">
            <label for="maPhim" class="col-sm-2 col-form-label">Mã tập phim:</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" name="maTapPhim" value="<?php echo $maTapPhim; ?>">
            </div>
            <?php
                if (empty($maTapPhimErr)) {
                    echo "";
                } else {
                    echo "
                    <div class='alert alert-danger col-sm-3' role='alert'>
                        $maTapPhimErr
                    </div>
                    ";
                }
            ?>
        </div>

        <div class="row mb-1">
            <label for="maPhim" class="col-sm-2 col-form-label">Số tập:</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" name="soTap" value="<?php echo $soTap; ?>">
            </div>
            <?php
                if (empty($soTapErr)) {
                    echo "";
                } else {
                    echo "
                    <div class='alert alert-danger col-sm-3' role='alert'>
                        $soTapErr
                    </div>
                    ";
                }
            ?>
        </div>

        <div class="row mb-1">
            <label for="maPhim" class="col-sm-2 col-form-label">File tập phim:</label>
            <div class="col-sm-3">
                <input type="file" class="form-control" name="fileTapPhim">
            </div>
            <?php
                if (empty($fileTapPhimErr)) {
                    echo "";
                } else {
                    echo "
                    <div class='alert alert-danger col-sm-3' role='alert'>
                        $fileTapPhimErr
                    </div>
                    ";
                }
            ?>
        </div>
        
        <button type="submit" class="btn btn-primary" name="themTapMoi">Thêm tập mới</button>
    </form>
    
    <?php
        $sql = "SELECT * FROM tapphim WHERE MaPhim = '$maPhim'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            echo "<p style='text-align:center;' class='h4'>Danh sách tập phim đã có</p>";

            echo "<table class='table table-hover table-bordered border-warning'>";
            echo "<thead>
                    <tr class='table-dark  '>
                        <th>Mã tập phim</th>
                        <th>Số tập</th>
                        <th>File tập phim</th>
                        <th>Chức năng</th>
                    </tr>
                  </thead>";
            echo "<tbody>";
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr class='table-white'>";
                echo "<td>" . $row['MaTapPhim'] . "</td>";
                echo "<td>" . $row['SoTap'] . "</td>";
                echo "<td>" . $row['TapPhim'] . "</td>";
                echo "
                    <td>
                    <form name='' method='post'>
                        <input type='hidden' name='maPhim' value='".$row['MaPhim']."'>
                        <input type='hidden' name='maTapPhim' value='". $row['MaTapPhim'] ."'>
                        <input type='hidden' name='soTap' value='". $row['SoTap'] ."'>
                        <input class='btn btn-danger' type='submit' name = 'xoa' value='Xóa'' />
                    </form>
                    <a class='btn btn-warning' href='suatapphim.php?maPhim=". $row['MaPhim'] . "&maTapPhim=". $row['MaTapPhim'] ."'>Sửa tập phim</a>
                    </td>
                ";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<h3>Phim hiện tại chưa thêm tập nào</h3>";
        }
    ?>

    <?php
        include_once("../footer.html");
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
</body>
</html>