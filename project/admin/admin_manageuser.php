<?php
require 'config.php'; 

if (!isset($_SESSION["admin_login"])) {
    header("Location: adminlogin.php");
    exit();
}

$adminId = $_SESSION["admin_id"];
$result = mysqli_query($conn, "SELECT * FROM admins WHERE id='$adminId'");
$admin = mysqli_fetch_assoc($result);
$sql="Select * from users";
$result=$conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Styles/user_dashboard.css">
</head>
<body>
<div class="sidebar" id="sidebar" style="display: block; width:18%; height: 100%; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #888 #f0f0f0; -webkit-scrollbar-width: thin; -webkit-scrollbar-color: #888 #f0f0f0;">
        <h2>Hello admin</h2>
        <br>
        <ul><li><a href="home.html"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> Manage Admin</a></li>
            <li><a class="nav-link active" href="admin_manageuser.php"><i class="fas fa-user"></i> Manage User</a></li>
            <li><a href="foodorder.php"><i class="fas fa-cog"></i>Order Food</a></li>
            <li><a href="monthpayment.php"><i class="fas fa-cog"></i>Monthly Payment details</a></li>
            <li><a href="transaction.php"><i class="fas fa-cog"></i>Transaction details</a></li>
            <li><a href="bookings.php"><i class="fas fa-envelope"></i>Edit Booking</a></li>
            <li><a href="userfiles.php"><i class="fas fa-cog"></i> User Files</a></li>
            <li><a href="roomdetails.php"><i class="fas fa-cog"></i> Room Details</a></li>
            <li><a href="addrooms.php"><i class="fas fa-cog"></i> Add and Edit rooms</a></li>
            <li><a href="userreport.php"><i class="fas fa-cog"></i>User Reports</a></li>
            <li><a href="leaveform.php"><i class="fas fa-cog"></i>Leave Form Details</a></li>
            <li><a href="feedbackreply.php"><i class="fas fa-cog"></i>Query/Feedback</a></li>
            <li><a href="oldusers.php"><i class="fas fa-cog"></i> Old Users</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <div class="sidebar-toggle" onclick="toggleSidebar()">&#9776;</div>
    </div>
    <div id="helloAdminSection">
    <h5 style="display: inline-block; margin-right: 10px;">Hello admin</h5>
    <div class="show-sidebar-btn" onclick="toggleSidebar()"  style="display: inline-block;font-size: 24px; color: blue; cursor: pointer;">&#9776;</div>
    </div>
    <div class="main-content"id="mainContent">
        <h1>Manage Users:</h1>
        <form class="form-inline mb-3">
            <input class="form-control mr-sm-2" type="text" placeholder="Search by Name or ID" id="searchInput">
            <button class="btn btn-outline-primary my-2 my-sm-0" type="button" onclick="searchUser()">Search</button>
            <button class="btn btn-outline-secondary my-2 my-sm-0" type="button" onclick="showAll()">Show All</button>
        </form>
        <table>
            <tr>
                <th>Id: </th>
                <th>Name: </th>
                <th>Father Name: </th>
                <th>Date of Birth: </th>
                <th>Address: </th>
                <th>Phone Number: </th>
                <th>Email: </th>
                <th>Password: </th>
                <th>Edit</th>
                <th>Remove</th>
            </tr>
            <?php
            if($result-> num_rows>0){
                while($row=$result->fetch_assoc()){
                    echo "<tr><td>".$row["id"]."</td><td>".$row["name"]."</td><td>".$row["father_name"]."</td><td>".$row["dob"]."</td><td>"
                    .$row["address"]."</td><td>".$row["phone_number"]."</td><td>".$row["email"]."</td><td>".$row["password"]."</td>
                    <td><a href='edit-user.php?id={$row['id']}'class='btn btn-primary'>Edit</a></td>
                    <td><a href='delete-user.php?id={$row['id']}'class='btn btn-danger' onclick='return confirm(\"Are you sure?\");'>Remove</a></td></tr>";
                }
                echo"</table>";
            }
            else{
                echo"0 result";
            }
            $conn->close();
            ?>

        </table>
    </div>
    <script>
    function toggleSidebar() {
        var sidebar = document.getElementById("sidebar");
        var mainContent = document.getElementById("mainContent");

        if (sidebar.style.display === "none" || sidebar.style.display === "") {
            sidebar.style.display = "block";
            mainContent.style.marginLeft = "250px"; // Adjust the width as needed
        } else {
            sidebar.style.display = "none";
            mainContent.style.marginLeft = "0";
        }
    }
    function searchUser() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const tableRows = document.querySelectorAll("#mainContent table tr");

            tableRows.forEach(row => {
                if (row.getElementsByTagName('td').length > 0) {
                    const name = row.getElementsByTagName('td')[1].innerText.toLowerCase();
                    const id = row.getElementsByTagName('td')[0].innerText.toLowerCase();
                    row.style.display = (name.includes(searchValue) || id.includes(searchValue)) ? 'table-row' : 'none';
                }
            });
        }

        function showAll() {
            const tableRows = document.querySelectorAll("#mainContent table tr");

            tableRows.forEach(row => {
                row.style.display = 'table-row';
            });
        }
</script>
</body>
</html>