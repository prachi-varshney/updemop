<?php
session_start();
if (!isset($_SESSION['name']) && !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;

}


?>
<!DOCTYPE html>
<html lang="en">

<head>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords"
        content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <title>Project</title>

    <link href="assets/css/app.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">

</head>

<body>
    <div class="wrapper">
        <!-- sidebar -->
        <?php include 'layouts/sidebar.php' ?>
        <!-- header -->
        <?php include 'layouts/header.php' ?>

        <main class="content">
            <div class="container-fluid p-0">

                <ul class="nav nav-pills mb-0" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                            aria-selected="true" onclick="resetValidation()">List</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
                            aria-selected="false">Add User</button>
                    </li>

                </ul>
                <input type="hidden" id="url" value="user_handler">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                        aria-labelledby="pills-home-tab">
                        <div class="card">

                            <div class="p-3 shadow mt-2" style="border:1px solid lightgray">
                                <form id="search">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <input type="text" name="name" class="form-control form-control-sm"
                                                id="nname" placeholder="Enter Name">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <input type="text" name="email" class="form-control form-control-sm"
                                                id="eemail" placeholder="Enter Email">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <input type="text" name="phone" class="form-control form-control-sm"
                                                id="pphone" placeholder="Enter Phone No">
                                        </div>


                                        <div class="col-md-3">
                                            <!-- <button type="reset" class="btn btn btn-secondary me-2">Reset</button> -->
                                            <button type="reset" class="btn btn btn-secondary me-2"
                                                onclick="resetValidation()">Reset</button>
                                            <button type="button" class="btn btn btn-dark"
                                                onclick="loadTable('list')">Search</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- <table class="table table-bordered mt-2">
                                <thead style="background-color: #f0f0f0">
                                    <tr>
                                        <th>Sno</th>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone No</th>
                                        <th align="center" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="table_record">

                                </tbody>
                            </table> -->
                            <!-- <select id="limit" onchange="loadTable('list', this.value)" class="mb-1 col-md-1">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                            </select> -->

                            <table class="table table-bordered mt-2">
                                <thead style="background-color: #f0f0f0">
                                    <tr>
                                        <th>Sno</th>
                                        <th>Id
                                            <i class="fas fa-arrow-up" onclick="sortTable(1, 'asc')"></i>
                                            <i class="fas fa-arrow-down" onclick="sortTable(1, 'desc')"></i>
                                        </th>
                                        <th>Name
                                            <i class="fas fa-arrow-up" onclick="sortTable(2, 'asc')"></i>
                                            <i class="fas fa-arrow-down" onclick="sortTable(2, 'desc')"></i>
                                        </th>
                                        <th>Email
                                            <i class="fas fa-arrow-up" onclick="sortTable(3, 'asc')"></i>
                                            <i class="fas fa-arrow-down" onclick="sortTable(3, 'desc')"></i>
                                        </th>
                                        <th>Phone No
                                            <i class="fas fa-arrow-up" onclick="sortTable(4, 'asc')"></i>
                                            <i class="fas fa-arrow-down" onclick="sortTable(4, 'desc')"></i>
                                        </th>
                                        <th align="center" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="table_record">

                                </tbody>
                            </table>
                            <div id="pagination">
  <ul class="pagination">
    <li>
      <a href="#" onclick="loadTable('list', 10, 0)">Prev</a>
    </li>
    <li>
      <a href="#" onclick="loadTable('list', 5, 0)">5</a>
    </li>
    <li class="active">
      <a href="#" onclick="loadTable('list', 10, 0)">10</a>
    </li>
    <li>
      <a href="#" onclick="loadTable('list', 20, 0)">20</a>
    </li>
    <li>
      <a href="#" onclick="loadTable('list', 50, 0)">50</a>
    </li>
    <li>
      <a href="#" onclick="loadTable('list', 100, 0)">100</a>
    </li>
    <li>
      <a href="#" onclick="loadTable('list', 10, 10)">Next</a>
    </li>
  </ul>
</div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <div class="card">
                            <div class="card-header mb-0 pb-1">
                                <h2 class="card-title mb-0">User Info.</h2>
                            </div>
                            <div class="card-body">
                                <form id="dataFrom" enctype="multipart/form-data">


                                    <input type="hidden" name="type" value="ADD_EDIT">


                                    <div class="row">
                                        <input type="hidden" class="" name="id" id="id" />
                                        <!-- <div class="mb-3 col-md-3">
                                            <label class="form-label">Name <span class="req-star"
                                                    style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-sm" name="name"
                                                id="name" required maxlength="50" />
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">Email <span class="req-star"
                                                    style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-sm" name="email"
                                                id="email" required maxlength="50" />
                                        </div> -->




                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">Name <span class="req-star"
                                                    style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-sm" name="name"
                                                id="name" required maxlength="50" />
                                            <span id="name-error" style="color: red; display: none;"></span>
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">Email <span class="req-star"
                                                    style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-sm" name="email"
                                                id="email" required maxlength="50" />
                                            <span id="email-error" style="color: red; display: none;"></span>
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">Phone No <span class="req-star"
                                                    style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-sm" name="phone"
                                                id="phone" required maxlength="10" />
                                            <span id="phone-error" style="color: red; display: none;"></span>
                                        </div>

                                        <!-- <div class="mb-3 col-md-3">
                                            <label class="form-label">Password <span class="req-star"
                                                    style="color: red;">*</label>
                                            <input type="password" class="form-control form-control-sm" name="password"
                                                id="password" />
                                            <span id="password-error" style="color: red; display: none;"></span>
                                        </div> -->





                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">Password <span class="req-star"
                                                    style="color: red;">*</label>
                                            <input type="password" class="form-control form-control-sm" name="password"
                                                id="password" required maxlength="20" />
                                            <span id="password-error" style="color: red; display: none;"></span>
                                        </div>
                                    </div>

                                    <div class="row mt-2 float-end">
                                        <div class="col-md-12 ">
                                            <button type="reset" class="btn btn btn-secondary me-2"
                                                onclick="resetValidation()">Reset</button>

                                            <button type="button" class="btn btn btn-dark"
                                                onclick="addUpdate('ADD_EDIT')">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </main>
    </div>
    </div>

    <script src="assets/js/script.js">

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
        </script>

    <script>
        // $(document).ready(function () {
        //     // alert()
        //     loadTable('list');
        // });


        $(document).ready(function () {
    loadTable('list', 10, 0);
});
        $(document).ready(function () {
            $('#name').on('input', function () {
                var name = $(this).val();
                if (!validateName(name)) {
                    $('#name-error').html('Name can only contain letters and spaces.').show();
                } else {
                    $('#name-error').hide();
                }
            });

            $('#email').on('input', function () {
                var email = $(this).val();
                if (!validateEmail(email)) {
                    $('#email-error').html('Invalid email format.').show();
                } else {
                    $('#email-error').hide();
                }
            });

            $('#phone').on('input', function () {
                var phone = $(this).val();
                if (!validatePhone(phone)) {
                    $('#phone-error').html('Phone number must be 10 digits long.').show();
                } else {
                    $('#phone-error').hide();
                }
            });

            $('#password').on('input', function () {
                var password = $(this).val();
                if (!validatePassword(password)) {
                    $('#password-error').html('Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, one digit, and one special character.').show();
                } else {
                    $('#password-error').hide();
                }
            });

            $('#dataFrom').submit(function (e) {
                var name = $('#name').val();
                var email = $('#email').val();
                var phone = $('#phone').val();
                var password = $('#password').val();
                var id = $('#id').val();
                var errorMessage = '';

                if (!validateName(name)) {
                    errorMessage += 'Name can only contain letters and spaces.<br>';
                }

                if (!validateEmail(email)) {
                    errorMessage += 'Invalid email format.<br>';
                }

                if (!validatePhone(phone)) {
                    errorMessage += 'Phone number must be 10 digits long.<br>';
                }

                if (id == 0 && password == '') {
                    errorMessage += 'Password is required.<br>';
                } else if (id != 0 && password != '') {
                    if (!validatePassword(password)) {
                        errorMessage += 'Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, one digit, and one special character.<br>';
                    }
                }

                if (errorMessage !== '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });

        });

        function validateName(name) {
            var regex = /^[a-zA-Z ]+$/;
            if (regex.test(name)) {
                return true;
            } else {
                return false;
            }
        }

        function validateEmail(email) {
            var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (regex.test(email)) {
                return true;
            } else {
                return false;
            }
        }

        function validatePhone(phone) {
            var regex = /^[0-9]{10}$/;
            if (regex.test(phone)) {
                return true;
            } else {
                return false;
            }
        }

        function validatePassword(password) {
            var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (regex.test(password)) {
                return true;
            } else {
                return false;
            }
        }





    </script>


    <script src="assets/js/app.js"> </script>


</html>