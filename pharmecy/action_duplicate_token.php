<?php 
include 'includes/config.php'; 
if (isset($_GET['duplicate']) && isset($_GET['token_no']) && $_GET['token_no'] != '') 
{
    $tokan_id = $_GET['token_no'];
    header('location: print_medicine_slip_duplicate.php?tokan_no='.$tokan_id);
    // echo '<script>
    //     window.open("print_medicine_slip_duplicate.php?tokan_no='.$tokan_id.'", "_blank", "toolbar=no,scrollbars=no,resizable=no,top=300,left=300,width=400,height=600,status=no");
    //     location.replace("dashboard.php");
    // </script>';
    mysqli_close($con);
    exit(0);
}
?>