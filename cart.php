<?php
    include("header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="css/footer.css">
        <link href="https://fonts.cdnfonts.com/css/dec-terminal-modern" rel="stylesheet">
        <link rel="stylesheet" href="css/index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php
        // Kết nối database
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "gamers_alliance";

        // Tạo kết nối
        $conn = mysqli_connect($servername, $username, $password, $database);

        // Kiểm tra kết nối
        if (!$conn) {
            die("Kết nối thất bại: " . mysqli_connect_error());
        }
        $khach_hang_id = $_SESSION['logged_in'];

        // Lấy danh sách mặt hàng dựa trên mat_hang_id đã xuất hiện trên bảng ctdh
        $sql = "SELECT
        dh.don_hang_id,
        mh.ten_mat_hang,
        mh.don_gia,
        mh.the_loai,
        mh.mo_ta,
        mh.anh,
        dt.dev_name,
        tl.ten_the_loai
      FROM
        don_hang AS dh
      INNER JOIN
        ctdh AS c ON dh.don_hang_id = c.don_hang_id
      INNER JOIN
        mat_hang AS mh ON c.mat_hang_id = mh.mat_hang_id
      LEFT JOIN
        dev_team AS dt ON mh.dev_team_id = dt.dev_id
      LEFT JOIN
        the_loai AS tl ON mh.the_loai = tl.the_loai_id
      WHERE
        dh.khach_hang_id = $khach_hang_id;";

        $result = $conn->query($sql);

        // Kiểm tra kết quả
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<form method='post' action='#'>";
                echo "<div style='display: flex; width:800px; margin-bottom: 10px; margin-left: 300px'>";
                echo "<div style='width: 20%; margin-top: 20px;'>";
                echo "<img src='game_img/{$row['anh']}' width='100px'>";
                echo "</div>";
                echo "<div style='width: 80%;'>";
                echo "<div style='text-align: center;'>" . $row['ten_mat_hang'] . "</div><br>";
                echo substr($row['mo_ta'], 0, 100). "...";
                echo "</div>";
                echo "</div>";
                echo "<div style='display: flex;width:800px; margin-left: 300px'>";
                echo "<div style='width: 50%; margin-left: 160px; color: green;'>".$row['dev_name']."</div>";
                echo "<div style='width: 25%;text-align: right;margin-right: 10px; color: red;'>".$row['don_gia']."VNĐ"."</div>";
                echo "<div style='width: 25%;text-align: left;'>"."<a href='' style='color: green;'> Xem sản phẩm</a>"."</div>";
                echo "<input type='submit' name='addcart' value='thêm vào giỏ'>";
                echo "</div>";
                echo "<br>";
                echo "</form>";
            }
        } else {
            echo "Không có mặt hàng nào";
        }

        // Ngắt kết nối
        mysqli_close($conn);
    ?>
    <?php require("footer.php"); ?>
</body>
</html>