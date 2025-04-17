                    
                    
                    
                    <table id="table" class="table table-bordered table-sm text-center">
                        <thead>
                            <tr class="fs-6" style="vertical-align: middle">
                                <th class="col-1">Order Number</th>
                                <th class="col-5">Buyer</th>
                                <th class="col-2">Payment Method</th>
                                <th class="col-2">Total</th>
                                <th class="col-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        include "../connect.php";
                        $offset = $_POST["offset_count"];
                        $status = $_POST["current_status"];
                        
                        print_r($offset, $status);
                        // SQL query to get orders based on status
                        $sql = "SELECT * FROM `order` WHERE status = ? ORDER BY order_id DESC LIMIT 10 OFFSET ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "si", $status, $offset); // Bind the status parameter
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr class='text-truncate'>
                                    <td>" . $row["order_id"] . "</td>
                                    <td style='max-width: 20px; overflow: scroll;'>" . $row["ordered_by"] . "</td>
                                    <td>" . $row["payment_method"] . "</td>
                                    <td>" . $row["total_price"] . "</td>
                                    <td>
                                        <a href='order_details.php?order_id=" . $row["order_id"] . "' class='btn btn-success btn-sm'>Details</a>
                                    </td>
                                  </tr>";
                            }
                            mysqli_stmt_close($stmt);
                        }
                        ?>
                        </tbody>
                    </table>