
	    <table class = "table table-bordered table-hover">
	        <thead>
	            <tr style = "text-align: center;">
	                <form method = "POST" action = "recepits.php">
	                <th>S#</th>
	                <th>YEAR
	                    <input required type = "number" min="2000" max="2099" step="1" value="<?php echo date('Y'); ?>" name = "book_year" id = "book_year" class = "form-control" />
	                </th>
	                <th>BOOK NO
	                    <input required type = "number" name = "book_no" id = "book_no" class = "form-control" />
	                </th>
	                <th>BRANCH 
	                    <input list="browsers" min = "1" required type = "number" name = "br_id" id = "br_id" class = "form-control" />
                        <datalist id="browsers">
                            <?php
                            $run = mysqli_query($GLOBALS['con'], "SELECT id, tag_name FROM `branchs` WHERE `status` = '1' ");
                            if (mysqli_num_rows($run) > 0) 
                            {
                                while ($row = mysqli_fetch_array($run)) 
                                {
                                    echo '<option value = "'.$row['id'].'">'.$row['tag_name'].'</option>';
                                }    
                            }
                            ?>
                        </datalist>
                    </th>
	                <th>TOTAL RECEIPTS
	                    <input required type = "number" name = "no_of_receipts" id = "no_of_receipts" class = "form-control" />
	                </th>
	                <th>USER RECEIPTS</th>
	                <th>START FROM
	                    <input required type = "number" name = "start_slip_no" id = "start_slip_no" class = "form-control" />
	                </th>
	                <th>END AT
	                    <input required type = "number" name = "end_slip_no" id = "end_slip_no" class = "form-control" />
	                </th>
	                <th>
	                    <input required type = "submit" name = "save_book" value = "SAVE BOOK" id = "save_book" class = "btn btn-primary" />
	                </th>
	                </form>
	            </tr>
	        </thead>
	        <tbody>
	            <?php
	            $s= 0 ;
	            $select = "SELECT * FROM `receipt_books` INNER JOIN branchs ON receipt_books.branch_id = branchs.id WHERE `receipt_book_status` = '1' ";
	            $run = mysqli_query($con, $select);
	            if(mysqli_num_rows($run) > 0)
	            {
	                while($row = mysqli_fetch_array($run))
	                {
	                    $s++;
	                ?>
	           <tr>
	               <td><?php echo $s; ?></td>
	               <td><?php echo $row['book_year']; ?></td>
	               <td><?php echo $row['book_no']; ?></td>
	               <td><?php echo $row['tag_name']; ?></td>
	               <td><?php echo $row['no_of_receipts']; ?></td>
	               <td><?php echo $row['used_receipts']; ?></td>
	               <td><?php echo $row['start_slip_no']; ?></td>
	               <td><?php echo $row['end_slip_no']; ?></td>
	               <td>
	                   <form action = "recepits.php" method = "POST">
	                       <input type = "hidden" value = "<?php echo $row['book_no']; ?>" name = "book_no" />
	                       <input type = "submit" value = "SHOW" name = "show_book_record" class = "btn btn-sm btn-info" />
	                   </form>
	               </td>
	           </tr>
	                <?php }
	            }
	            ?>
	        </tbody>
	    </table>