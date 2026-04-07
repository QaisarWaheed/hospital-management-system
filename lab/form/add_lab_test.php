<form action = "lab_tests.php" method = "POST" class = "d-print-none">
    <div class = "p-4 g-4">
    <div class = "row">
        <div class = "col-md-12">
            <div class = "h3 text-center">ADD NEW TEST DETAILS</div>
        </div>
        <div class = "col-md-3">
            <label>PARAMETER NAME</label>
            <input type ="text" name="parameter_name" class = "form-control text-danger" required>
        </div>
        <div class = "col-md-3">
            <label>TEST NAME</label>
            <input list="items" name="item_id" id="item_id" class = "form-control text-danger" required>
            <datalist id="items">
            <?php
            $select = "SELECT id, name FROM items WHERE category_id = '2' AND status = '1' ";
            $run = mysqli_query($con, $select);
            if(mysqli_num_rows($run) > 0)
            {
                while($row = mysqli_fetch_array($run))
                {
                    echo '<option value = "'.$row['id'].'">'.$row['name'].'</option>';
                }
            }
            else
                echo '<option value = ""> NO DATA FOUND</option>';
            ?>
            </datalist>
        </div>
        <div class = "col-md-3">
            <label>TEST CATEGORY</label>
            <select name = "lab_reporting_test_type" class = "form-control">
            <?php
            $select = "SELECT * FROM `test_categories` WHERE `test_category_status` = '1' ";
            $run = mysqli_query($con, $select);
            if(mysqli_num_rows($run) > 0)
            {
                while($row = mysqli_fetch_array($run))
                {
                    echo '<option value = "'.$row['test_category_id'].'">'.$row['test_category_title'].'</option>';
                }
            }
            ?>
            </select>
        </div>
        <div class = "col-md-3">
            <label>RESULT UNIT</label>
            <select name = "lab_reporting_test_unit" class = "form-control">
            <?php
            $select_test_categories = "SELECT * FROM `lab_test_units` WHERE `lab_test_unit_status` = '1' ";
            $run_test_categories = mysqli_query($con, $select_test_categories);
            if(mysqli_num_rows($run_test_categories) > 0)
            {
                while($row_test_categories = mysqli_fetch_array($run_test_categories))
                {
                    echo '<option value = "'.$row_test_categories['lab_test_unit_id'].'">'.$row_test_categories['lab_test_unit_value'].'</option>';
                }
            } ?>
            </select>
        </div>
        <div class = "col-md-3">
            <label>TEST TIME</label>
            <input type = "number" placeholder = "ENTER TEST TIME IN MINUTIES" min = "0" class = "form-control" name = "lab_reporting_test_time_minutes" id = "lab_reporting_test_time_minutes" />
        </div>
        <div class = "col-md-3">
            <label>RESULT NORMAL VALUE(GENRAL)</label>
            <textarea rows = "1" name = "lab_reporting_test_normal_value" class = "form-control"></textarea>
        </div>
        <div class = "col-md-3">
            <label>RESULT NORMAL VALUE(MALE)</label>
            <textarea rows = "1" name = "lab_reporting_test_normal_male" class = "form-control"></textarea>
        </div>
        <div class = "col-md-3">
            <label>RESULT NORMAL VALUE(FEMALE)</label>
            <textarea rows = "1" name = "lab_reporting_test_normal_female" class = "form-control"></textarea>
        </div>
        <div class = "col-md-3">
            <label>RESULT NORMAL VALUE(CHILDERN)</label>
            <textarea rows = "1" name = "lab_reporting_test_normal_childern" class = "form-control"></textarea>
        </div>
        <div class = "col-md-3">
            <label>MSG - NORMAL</label>
            <input type = "text" name = "lab_reporting_test_msg_if_normal" class = "form-control" />
        </div>
        <div class = "col-md-3">
            <label>MSG - LOW</label>
            <input type = "text" name = "lab_reporting_test_msg_if_low" class = "form-control" />
        </div>
        <div class = "col-md-3">
            <label>MSG - HIGH</label>
            <input type = "text" name = "lab_reporting_test_msg_if_high" class = "form-control" />
        </div>
        <div class = "col-md-3">
            <label></label></br>
            <input type = "submit" name = "save_form_data" value = "SAVE DATA" class = "btn btn-sm btn-primary" />
            <input type = "reset" name = "reset_form" value = "CLEAR DATA" class = "btn btn-sm btn-warning" />
        </div>
    </div>
    </div>
</form>