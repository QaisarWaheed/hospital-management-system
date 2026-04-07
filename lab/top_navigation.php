<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">YCDO</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Dashboard <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="patient_lab_by_token.php">1st Turn</a>
            </li>
            <li>
                <div class="dropdown show">
                    <a class="dropdown-toggle nav-link" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Test Records</a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item"  href = "lab_test_received_samples.php" >Received Samples</a>
                        <a class="dropdown-item"  href = "lab_test_in_process.php" >Test In Process</a>
                        <a class="dropdown-item"  href = "lab_test_conducted.php" >Conducted</a>
                        <a class="dropdown-item"  href = "lab_test_approved_report.php" >Approved Report</a>
                        <a class="dropdown-item"  href = "lab_test_print_report.php" >Print Report</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lab_test_sample_transfer.php">Sample Transfer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="lab_test_all_reocrds.php">All Records</a>
            </li>
            <li>
                <div class="dropdown show">
                    <a class="dropdown-toggle nav-link" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Progress</a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item"  href = "progress_report_daily.php" >Daily</a>
                        <a class="dropdown-item"  href = "progress_report_monthly.php" >Monthly</a>
                    </div>
                </div>
            </li>
            <li>
                <div class="dropdown show">
                    <a class="dropdown-toggle nav-link" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Referral</a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item"  href = "referral_patients.php" >Patients Record</a>
                        <a class="dropdown-item"  href = "referal_tests.php" >Tests Record</a>
                    </div>
                </div>
            </li>

<?php if($lab_login_branch_id == 9) { ?>
            <li class="nav-item">
                <a class="nav-link" href="lab_tests.php">Lab Tests</a>
            </li>
<?php } ?>
<?php if($lab_user_id == 1) { ?>
            <li class="nav-item">
                <a class="nav-link" href="lab_test_link.php">Lab Test Link </a>
            </li>
<?php } ?>
            <li class="nav-item">
                <a class="nav-link" href="item_receive_lab.php">Item Receive</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="ycdo_phone_book.php">Phone Book</a>
            </li>
        </ul>
        <form action = "logout.php" method = "POST" class="form-inline my-2 my-lg-0">
            <div><?php echo $_SESSION['lab_user_name']; ?></div> 
            <input type = "submit" value = "LOGOUT" name = "logout" class = "btn btn-warning btn-sm" />
        </form>
    </div>
</nav>