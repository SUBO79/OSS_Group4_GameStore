<?php
    include("header.php");
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
    <title>Catalog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="css/footer.css">
        <link href="https://fonts.cdnfonts.com/css/dec-terminal-modern" rel="stylesheet">
        <link rel="stylesheet" href="css/index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <main class="container" style="width: 55%;margin: 0 auto">
    <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "Gamers_Alliance";
        $conn = new mysqli($servername, $username, $password, $database);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if(isset($_GET["search"]))
        {
            $search = $_GET["search"];
        }
        else
        {
            $search = "";
        }

        // Lấy tổng số hàng trong bảng mat_hang
        $sql = "SELECT COUNT(*) AS total_rows FROM mat_hang WHERE ten_mat_hang LIKE '%$search%'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $total_rows = $row['total_rows'];

        // Lấy số trang
        $pages = $total_rows / 2;
        $pages = $pages + $total_rows % 2;

        // Lấy trang hiện tại
        $current_page = (isset($_GET['page'])) ? $_GET['page'] : 1;

        // Lấy offset
        $offset = ($current_page - 1) * 2;

        // Lấy dữ liệu phân trang
        $sql = "SELECT mat_hang.mat_hang_id, mat_hang.mo_ta,mat_hang.ten_mat_hang, mat_hang.don_gia, the_loai.ten_the_loai, dev_team.dev_name, mat_hang.anh
        FROM mat_hang
        JOIN dev_team ON mat_hang.dev_team_id = dev_team.dev_id
        JOIN the_loai ON mat_hang.the_loai = the_loai.the_loai_id WHERE ten_mat_hang LIKE '%$search%' LIMIT $offset, 2";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<form method='post' action='#'>";
                echo "<div style='display: flex; width:800px; margin-bottom: 10px;'>";
                echo "<div style='width: 20%; margin-top: 20px;'>";
                echo "<img src='game_img/{$row['anh']}' width='100px'>";
                echo "</div>";
                echo "<div style='width: 80%;'>";
                echo "<div style='text-align: center;'>" . $row['ten_mat_hang'] . "</div><br>";
                echo substr($row['mo_ta'], 0, 100). "...";
                echo "</div>";
                echo "</div>";
                echo "<div style='display: flex;width:800px;'>";
                echo "<div style='width: 50%; margin-left: 160px; color: green;'>".$row['dev_name']."</div>";
                echo "<div style='width: 25%;text-align: right;margin-right: 10px; color: red;'>".$row['don_gia']."VNĐ"."</div>";
                echo "<div style='width: 25%;text-align: left;'>"."<a href='' style='color: green;'> Xem sản phẩm</a>"."</div>";
                echo "<input type='submit' name='addcart' value='thêm vào giỏ'>";
                echo "</div>";
                echo "<br>";
                echo "</form>";
                
                if(isset($_POST['addcart'])) {
                    $mat_hang_id = $row['mat_hang_id'];
                    $khach_hang_id = $_SESSION['logged_in']; 
                    $sql = "SELECT COUNT(*) AS product_count FROM ctdh WHERE mat_hang_id = $mat_hang_id AND don_hang_id IN (SELECT don_hang_id FROM don_hang WHERE khach_hang_id = $khach_hang_id)";
                    $result_check = $conn->query($sql);
                    $row_check = $result_check->fetch_assoc();

                    if ($row_check['product_count'] == 0) {
                        // Product is not in the cart, so add it
                        $sql = "SELECT * FROM don_hang WHERE khach_hang_id = $khach_hang_id";
                        $result = $conn->query($sql);
                        if ($result->num_rows>0) {
                            $row = $result->fetch_assoc();
                            $don_hang_id = $row['don_hang_id'];
                        } else {
                            $sql = "INSERT INTO don_hang (khach_hang_id) VALUES ('$khach_hang_id')";
                            if ($conn->query($sql) === TRUE) {
                                $don_hang_id = $conn->insert_id;
                                echo "<script>alert('thêm đơn hàng thành công','Thông báo từ hệ thống');</script>";
                            } else {
                                echo "<script>alert('lỗi','Thông báo từ hệ thống');</script>" . $conn->error;
                            }
                        }
                        $sql = "INSERT INTO ctdh (don_hang_id, mat_hang_id) VALUES ('$don_hang_id', '$mat_hang_id')";
                        if ($conn->query($sql) === TRUE) {
                            echo "<script>alert('thêm đơn hàng thành công','Thông báo từ hệ thống');</script>";
                        } else {
                            echo "<script>alert('lỗi','Thông báo từ hệ thống');</script>" . $conn->error;
                        }
                    } else {
                        echo "<script>alert('mặt hàng đã tồn tại','Thông báo từ hệ thống');</script>";
                    }
                    
                }
            }
        }
        echo "<div class='pagination'>";
        for ($i = 1; $i <= $pages; $i++) {
            echo "<a href='?page=$i' class='page-item'>";
            echo "<span class='page-link'>$i</span>";
            echo "</a>";
        }
        echo "</div>";
        
        $conn->close();
    ?>
    </main>
    <div>
    <?php require("footer.php"); ?>
    </div>
    
</body>
</html>