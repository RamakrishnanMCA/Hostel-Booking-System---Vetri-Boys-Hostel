<?php
require 'config.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php"); 
    exit();
}
$input_user_id= $_SESSION['id'];
$userNameQuery = "SELECT name FROM users WHERE id = '$input_user_id'";
$userNameResult = mysqli_query($conn, $userNameQuery);

if ($userNameResult) {
    $userNameRow = mysqli_fetch_assoc($userNameResult);
    $userName = $userNameRow['name'];
}else {
    // Handle the error if necessary
    $userName = 'Guest'; // Default value if the name is not found
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_FILES['photo']['name']) || empty($_FILES['id_card']['name'])) {
        echo "<script>alert('Please select both photo and ID proof files.')</script>";
        echo "<script>window.location.href = 'uploadfiles.php';</script>";
        return; // Stop further processing and stay on the same page
    }


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $check_user_query = "SELECT * FROM users WHERE id = '$input_user_id'";
    $result = $conn->query($check_user_query);
    $check_existing_files_query = "SELECT * FROM user_files WHERE user_id = '$input_user_id'";
    $result_existing_files = $conn->query($check_existing_files_query);

    if ($result_existing_files->num_rows > 0) {
        echo "<script>alert('Files have already been uploaded for this user.')</script>";
        $conn->close();
        exit();
    }
    if ($result->num_rows > 0) {
        if (isset($_FILES['photo']) && isset($_FILES['id_card'])) {
            $photo_filename = $_FILES['photo']['name'];
            $photo_tmp_path = $_FILES['photo']['tmp_name'];

            $id_card_filename = $_FILES['id_card']['name'];
            $id_card_tmp_path = $_FILES['id_card']['tmp_name'];

            $photo_unique_filename = generateUniqueFilename($photo_filename);
            $id_card_unique_filename = generateUniqueFilename($id_card_filename);

            $uploadDir = 'uploads/';
            $photo_destination = $uploadDir . $photo_unique_filename;
            $id_card_destination = $uploadDir . $id_card_unique_filename;

            move_uploaded_file($photo_tmp_path, $photo_destination);
            move_uploaded_file($id_card_tmp_path, $id_card_destination);

            $insert_files_query = "INSERT INTO user_files (user_id, photo_filename, id_proof_filename)
                                   VALUES (?, ?, ?)";

            $stmt_insert_files = $conn->prepare($insert_files_query);
            $stmt_insert_files->bind_param("iss", $input_user_id, $photo_unique_filename, $id_card_unique_filename);

            if ($stmt_insert_files->execute()) {
                echo "<script>alert('Files uploaded and stored successfully!')</script>";
            } else {
                echo "Error: " . $stmt_insert_files->error;
            }

            $stmt_insert_files->close();
        } else {
            echo "<script>alert('Please select both photo and ID proof files.')</script>";
        }
    } else {
        echo "<script>alert('Invalid User ID. Please enter a valid User ID.')</script>";
    }

    $conn->close(); 
}

function generateUniqueFilename($filename) {
    $timestamp = time();
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    return $timestamp . '_' . uniqid() . '.' . $extension;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Styles/user_dashboard.css">
    <style>
        #preview-container {
            display: none;
        }
        #preview-photo {
            margin-top: 10px;
            width: 48%;
            float: left;
        }
        #photo-preview {
            width: 45%;
            height: auto;
        }
        #preview-id-card {
            margin-top: 10px;
            width: 48%;
            float: left;
        }
        #id-card-preview {
            width: 100%;
            height: 600px;
        }
    </style>
</head>
<body>
<div class="sidebar" id="sidebar" style="display: block; width:18%; height: 100%; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #888 #f0f0f0; -webkit-scrollbar-width: thin; -webkit-scrollbar-color: #888 #f0f0f0;">
<h2>Welcome <span style="color:blue;"><?php echo $userName; ?> </span></h2>
        <br>
        <ul>
            <li><a href="home.html"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="user_dashboard.php"><i class="fas fa-home"></i>My details</a></li>
            <li><a href="payment.php"><i class="fas fa-cog"></i> Payment</a></li>
            <li><a href="bookroom.php"><i class="fas fa-envelope"></i> Book a Room</a></li>
            <li><a class="nav-link active" href="uploadfiles.php"><i class="fas fa-cog"></i>Upload files</a></li>
            <li><a href="report.php"><i class="fas fa-cog"></i> Download PDF</a></li>
            <li><a href="foodorder.php"><i class="fas fa-cog"></i> Food Details</a></li>
            <li><a href="monthlypayment.php"><i class="fas fa-cog"></i>Monthly Payment</a></li>
            <li><a href="leaveform.php"><i class="fas fa-cog"></i> Leave Form</a></li>
            <li><a href="feedback.php"><i class="fas fa-cog"></i> Query/Feedback</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <div class="sidebar-toggle" onclick="toggleSidebar()">&#9776;</div>
    </div>
    <div id="helloAdminSection">
    <h5 style="display: inline-block; margin-right: 10px;">Hello <?php echo $userName; ?></h5>
    <div class="show-sidebar-btn" onclick="toggleSidebar()"  style="display: inline-block;font-size: 24px; color: blue; cursor: pointer;">&#9776;</div>
    </div>
    <div class="main-content" id="mainContent">
    <h3>Upload Files</h3>
    <form action="uploadfiles.php" method="POST" enctype="multipart/form-data">
    <h3 class="card-title mt-5"></h3>
    <div class="row">
    <div class="col-sm-12 col-md-6 col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">User ID</h6>
                                            <div class="form-group">
                                                <input type="number" name="userid" id="userid" required class="form-control" value="<?php echo $input_user_id; ?>" readonly>
                                            </div>
                                    </div>
                                </div>
                            </div>
        </div>
    <table>
    <th><label for="photo">Photo:</label></th>
    <td><input type="file" name="photo" accept=".jpg,.png" onchange="previewImage(event, 'photo')"></td>
    <th><label for="id_proof">ID Proof:</label> </th>
    
    <td><input type="file" name="id_card" accept=".pdf" onchange="previewPdf(event, 'id_card')"></td> </table>
    <div id="preview-container">
        <div id="preview-photo">
            <img id="photo-preview" src="" alt="Passport size photo preview">
        </div>
        <div id="preview-id-card">
            <embed id="id-card-preview" type="application/pdf" width="100%" height="600px">
        </div>   
    </div>
    <button type="submit" name="submit">Upload Files</button>  
    </form>
    <script>
        function previewImage(event, id) {
            var previewContainer = document.getElementById("preview-container");
            previewContainer.style.display = "block";
            var previewImage = document.getElementById("photo-preview");
            var file = event.target.files[0];
            var reader = new FileReader();
            
            if (file.type.startsWith("image/")) {
                if (file.size >= 50000 && file.size <= 150000) {
                    reader.onload = function (event) {
                        previewImage.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert("Image file size should be between 50KB and 150KB.");
                    previewContainer.style.display = "none";
                }
            } else {
                alert("Invalid file type. Please select an image file for the passport size photo.");
                previewContainer.style.display = "none";
            }
        }
        
        function previewPdf(event, id) {
            var previewContainer = document.getElementById("preview-container");
            previewContainer.style.display = "block";
            var previewPdf = document.getElementById("id-card-preview");
            var file = event.target.files[0];
            var reader = new FileReader();
            
            if (file.type === "application/pdf") {
                if (file.size >= 50000 && file.size <= 2000000) {
                    reader.onload = function (event) {
                        previewPdf.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert("PDF file size should be between 50KB and 2MB.");
                    previewContainer.style.display = "none";
                }
            } else {
                alert("Invalid file type. Please select a PDF file for the college ID card.");
                previewContainer.style.display = "none";
            }
        }

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
    </script>
</body>
</html>