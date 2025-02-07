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
                        <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true" onclick="resetValidation()">List</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Add Client</button>
                    </li>

                </ul>
                <input type="hidden" id="url" value="client_handler">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <div class="card">

                            <div class="p-3 shadow mt-2" style="border:1px solid lightgray">
                                <form id="search">
                                    <div class="row">
                                        <div class="col-md-2 mb-3">
                                            <input type="text" name="name" class="form-control form-control-sm" id="name" placeholder="Enter Name">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <input type="text" name="email" class="form-control form-control-sm" id="email" placeholder="Enter Email">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <input type="text" name="phone" class="form-control form-control-sm" id="phone" placeholder="Enter Phone No">
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <input type="text" name="address" class="form-control form-control-sm" id="address" placeholder="Enter Address">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <!-- <button type="reset" class="btn btn btn-secondary me-2">Reset</button> -->
                                            <button type="reset" class="btn btn btn-secondary me-2" onclick="resetValidation();loadTable(1,'list')">Reset</button>
                                            <button type="button" class="btn btn btn-dark" onclick="loadTable(1,'list')">Search</button>
                                        </div>
                                    </div>
                                </form>
                            </div>



                            <div class="row px-2 mt-2">
                                <div class="col-md-2 text-start">
                                    <div class="d-flex" style="width:8rem">
                                        <label class="form-label pt-2 pe-1">Limit </label>
                                        <select name="records_per_page" class="form-select form-select-sm" id="records_per_page" onchange="loadTable(1,'list')">
                                            <option>select</option>
                                            <option value="5" selected>5</option>
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6"></div>
                                <div class="col-md-4 text-end">
                                    <div class="pagination" id="pagination">

                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered ">
                                <thead class="" style="background-color: #f0f0f0">
                                    <tr>
                                        <th>Sno</th>

                                        <th data-column="id">Id
                                            <i class="fas fa-sort" onclick=""></i>
                                        </th>
                                        <th data-column="name">Name
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th data-column="email">Email
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th data-column="phone">Phone No
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th data-column="address">Address
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th align="center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="table_record">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <div class="card">
                            <div class="card-header mb-0 pb-1">
                                <h2 class="card-title mb-0">Client Info.</h2>
                            </div>
                            <div class="card-body">
                                <form id="dataFrom" enctype="multipart/form-data">
                                    <input type="hidden" name="type" value="ADD_EDIT">
                                    <div class="row">
                                        <input type="hidden" class="" name="id" id="id" />
                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">Name <span class="req-star" style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-sm" name="name" id="name" required maxlength="50" />
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">Email <span class="req-star" style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-sm" name="email" id="email" required maxlength="50" />
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">Phone No <span class="req-star" style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-sm" name="phone" id="phone" required maxlength="10" />
                                        </div>
                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">PinCode <span class="req-star" style="color: red;">*</span></label>
                                            <input type="text" class="form-control form-control-sm" name="pincode" id="pincode" required maxlength="6" minlength="6" required />
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">State <span class="req-star"></span></label>
                                            <select name="state" class="form-select form-select-sm" id="state" onclick="getStateCity()">
                                                <option value="0">Select</option>

                                            </select>
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">City <span class="req-star"></span></label>
                                            <select name="city" id="city" class="form-select form-select-sm">
                                                <option value="0">Select</option>
                                            </select>
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <label class="form-label">Address <span class="req-star"></span></label>
                                            <textarea name="address" class="form-control form-control-sm" id="address"></textarea>
                                        </div>

                                    </div>

                                    <div class="row mt-2 float-end">
                                        <div class="col-md-12 ">
                                            <button type="reset" class="btn btn btn-secondary me-2" onclick="resetValidation()">Reset</button>

                                            <button type="button" class="btn btn btn-dark" onclick="addUpdate('ADD_EDIT')">Submit</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>

    <script>
        // let sortOrder = 'asc';
        // $('th').click(function() {
        //     let columnName = $(this).data('column');
        //     sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        //     loadTable('list', sortOrder, columnName);

        // });
        let sortOrder = 'asc';
        let columnName = '';

        $('th').click(function() {
            columnName = $(this).data('column');
            sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
            loadTable(1, 'list', sortOrder, columnName);
        });



        $(document).ready(function() {
            stateList();
            loadTable(1, 'list');
        });

        function createErrorMessage(inputId, message) {
            let errorSpan = document.getElementById(inputId + 'Error');

            if (!message) {
                if (errorSpan) {
                    errorSpan.remove();
                }
            } else {
                if (!errorSpan) {
                    errorSpan = document.createElement('span');
                    errorSpan.id = inputId + 'Error';
                    errorSpan.classList.add('error');
                    document.getElementById(inputId).insertAdjacentElement('afterend', errorSpan);
                }
                errorSpan.textContent = message;
            }
        }

        function validateEmail() {
            const email = document.getElementById('email').value;
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (email === '') {
                createErrorMessage('email', ''); // Remove error if field is empty
            } else if (!email.match(emailPattern)) {
                createErrorMessage('email', 'Please enter a valid email address.');
            } else {
                createErrorMessage('email', ''); // Remove error if valid
            }
        }

        function validatePassword() {
            const password = document.getElementById('password').value;
            let errorMessage = '';
            if (password === '') {
                createErrorMessage('password', '');
            } else {
                if (password.length < 8) {
                    errorMessage += 'Password must be at least 8 characters long. ';
                }
                if (!/[A-Z]/.test(password)) {
                    errorMessage += 'Password must contain at least one uppercase letter. ';
                }
                if (!/[0-9]/.test(password)) {
                    errorMessage += 'Password must contain at least one number. ';
                }
                createErrorMessage('password', errorMessage.trim());
            }
        }

        function validatePhone() {
            const phone = document.getElementById('phone').value;
            const phonePattern = /^\d{10}$/;
            if (phone === '') {
                createErrorMessage('phone', '');
            } else if (!phone.match(phonePattern)) {
                createErrorMessage('phone', 'Phone number must be 10 digits.');
            } else {
                createErrorMessage('phone', '');
            }
        }

        document.getElementById('email').addEventListener('input', validateEmail);

        document.getElementById('phone').addEventListener('input', validatePhone);


        const numberInput = document.querySelector('.numeric');

        if (numberInput) {
            numberInput.addEventListener('keydown', function(event) {
                if (!/[0-9]/.test(event.key) && event.key !== 'Backspace' && event.key !== 'Delete') {
                    event.preventDefault();
                }
            });
        }
    </script>

    <script src="assets/js/app.js"> </script>




</html>