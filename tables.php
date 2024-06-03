<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css'>
<link rel='stylesheet' href='https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css'>
<link rel='stylesheet' href='https://cdn.datatables.net/buttons/1.2.2/css/buttons.bootstrap.min.css'>


<style>
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
</style>

    <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">

        <thead>
            <tr>
                <!-- <th>Serial No.</th> -->
                <th class="sorting">ID Number</th>
                <th class="sorting">Name</th>
                <th class="sorting">Username</th>
                <th class="sorting">Email</th>
                <th class="sorting">Department</th>
                <th class="sorting">Role</th>
                <th class="sorting">Action</th>
            </tr>
        </thead>

        <tbody>
            <?php
            require ('config/db_con.php');
            $table = mysqli_query($conn, "SELECT 
                                u.UserID, 
                                u.IdNumber,
                                u.Fname, 
                                u.Lname, 
                                u.Gender,
                                u.Age,
                                u.Birthday,
                                u.Address,
                                u.ContactNumber,
                                u.is_Admin_Group,
                                u.is_Ancillary_Group,
                                u.is_Nursing_Group,
                                u.is_Outsource_Group,
                                u.Username, 
                                u.Password,
                                u.Email, 
                                u.UserRoleID,
                                u.ProfilePhoto,
                                bd.DepartmentName, 
                                u.BaypointeDepartmentID,
                                usr.UserRoleName,
                                p.PrivilegeID,
                                u.is_Lock,
                                COUNT(p.PrivilegeID) AS NumOfPrivileges
                            FROM 
                                users u
                            INNER JOIN 
                                userroles usr ON u.UserRoleID = usr.UserRoleID
                            INNER JOIN 
                                baypointedepartments bd ON u.BaypointeDepartmentID = bd.BaypointeDepartmentID
                            LEFT JOIN 
                                privileges p ON u.UserID = p.UserID
                            WHERE 
                                u.Active = 1 AND usr.UserRoleID NOT IN (0)
                            GROUP BY 
                                u.UserID, u.Fname, u.Lname,u.Gender, u.IdNumber,
                                u.Age,
                                u.Birthday,
                                u.Address,
                                u.ContactNumber,
                                u.is_Admin_Group,
                                u.is_Ancillary_Group,
                                u.is_Nursing_Group,
                                u.ProfilePhoto,
                                u.UserRoleID,
                                u.is_Lock,
                                u.BaypointeDepartmentID,
                                u.is_Outsource_Group, u.Username, u.Password,u.Email, bd.DepartmentName, usr.UserRoleName;");

            $serialNo = 1;
            while ($row = mysqli_fetch_assoc($table)) {
                $lockAccountValue = $row['is_Lock'];
                ?>
                <tr>
                    <!-- <td><?php echo $serialNo++; ?></td> -->
                    <td><?php echo $row['IdNumber']; ?></td>
                    <td><?php echo $row['Fname'] . ' ' . $row['Lname']; ?></td>
                    <td><?php echo $row['Username']; ?></td>
                    <td><?php echo $row['Email']; ?></td>
                    <td><?php echo $row['DepartmentName']; ?></td>
                    <td><?php echo $row['UserRoleName']; ?></td>
                    <td>
                        <div class="d-inline-flex gap-3">
                            <div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="View">

                                <?php
                                // Display the button if Action_Add is 1
                                if ($actionView == 1) {
                                    echo '<button type="button" class="btn btn-primary"  
                                                        data-bs-toggle="modal" data-bs-target="#viewUser" 
                                                        data-user-id="' . $row['UserID'] . '"
                                                        data-fname="' . $row['Fname'] . '"
                                                        data-lname="' . $row['Lname'] . '"
                                                        data-gender="' . $row['Gender'] . '"
                                                        data-birthdate="' . $row['Birthday'] . '"
                                                        data-address="' . $row['Address'] . '"
                                                        data-cnumber="' . $row['ContactNumber'] . '"
                                                        data-email="' . $row['Email'] . '"
                                                        data-username="' . $row['Username'] . '"
                                                        data-password="' . $row['Password'] . '"
                                                        data-is-admin="' . $row['is_Admin_Group'] . '"
                                                        data-is-ancillary="' . $row['is_Ancillary_Group'] . '"
                                                        data-is-nursing="' . $row['is_Nursing_Group'] . '"
                                                        data-is-outsource="' . $row['is_Outsource_Group'] . '"
                                                        data-baypointe-department-id="' . $row['BaypointeDepartmentID'] . '"
                                                        data-user-role-id="' . $row['UserRoleID'] . '"
                                                        data-user-profile="' . $row['ProfilePhoto'] . '">
                                                        <i class="bi bi-eye"></i>  
                                                    </button>';
                                }
                                ?>

                            </div>

                            <div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Edit">
                                <?php
                                // Display the button if Action_Add is 1
                                if ($actionUpdate == 1) {
                                    echo '<button type="button" class="btn btn-info"  
                                                        data-bs-toggle="modal" data-bs-target="#editUser">
                                                            <i class="bi bi-pencil"></i>  
                                                        </button>';
                                }
                                ?>
                            </div>

                            <div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Unlock Account">


                                <?php
                                // Display the button if Action_Add is 1
                                if ($actionLock == 1 && $actionUnlock == 1) {
                                    echo '<button type="button" class="btn ' . ($lockAccountValue == 1 ? 'btn-danger' : 'btn-success') . '"  
                                                    data-bs-toggle="modal" data-bs-target="#lockAccount">
                                                        <i class="bi ' . ($lockAccountValue == 1 ? 'bi-lock' : 'bi-unlock') . '"></i>  
                                                    </button>';
                                }
                                ?>

                            </div>



                            <div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Assign Modules">


                                <?php
                                // Display the button if Action_Add is 1
                                if ($actionModuleView == 1) {
                                    echo '<button type="button" class="btn btn-secondary" id="Assignmodule" data-bs-toggle="modal" data-bs-target="#priveleges" data-user-id="' . $row['UserID'] . '">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>';
                                }
                                ?>
                            </div>

                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

<!-- partial -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js'></script>
<script src='https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.colVis.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js'></script>
<script src='https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.2.2/js/buttons.bootstrap.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js'></script>
<script src='https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js'></script>
<script src='https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js'></script>


<script>
    $(document).ready(function () {
        //Only needed for the filename of export files.
        //Normally set in the title tag of your page.
        document.title = "Simple DataTable";
        // Create search inputs in footer
        $("#example tfoot th").each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" placeholder="Search ' + title + '" />');
        });
        // DataTable initialisation
        var table = $("#example").DataTable({
            dom: '<"dt-buttons"Bf><"clear">lirtp',
            paging: true,
            autoWidth: true,
            buttons: [
                "colvis",
                "copyHtml5",
                "csvHtml5",
                "excelHtml5",
                "pdfHtml5",
                "print"
            ],
            initComplete: function (settings, json) {
                var footer = $("#example tfoot tr");
                $("#example thead").append(footer);
            }
        });

        // Apply the search
        $("#example thead").on("keyup", "input", function () {
            table.column($(this).parent().index())
                .search(this.value)
                .draw();
        });
    });
</script>