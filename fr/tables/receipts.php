
	    <table class = "table table-bordered table-hover">
	        <thead>
	            <tr style = "text-align: center;">
	                <th>S#</th>
	                <th>DATE</th>
	                <th>SERIAL NO</th>
	                <th>ACCOUNT</th>
	                <th>DETAIL</th>
	                <th>PHONE</th>
	                <th>AMOUNT</th>
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