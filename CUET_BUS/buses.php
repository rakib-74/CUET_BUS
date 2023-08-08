<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['utype'] != "Admin")
    header("Location: index.php");

include 'inc/basic_template.php';
t_header("Bus Ticket Booking &mdash; User Manager");
t_login_nav();
t_admin_sidebar();

if (isset($_GET['toggle'])) {
    require_once 'inc/database.php';
    $conn = initDB();
    if ($conn->query("update buses set approved=" . $_GET['toggle'] . " where id=" . $_GET['id']))
        echo '<script>alert("OK");</script>';
    else
        echo '<script>alert("Fail");</script>';
    $conn->close();
}
if (isset($_POST['addbus'])) {
    require_once 'inc/database.php';
    $conn = initDB();
    $sql = "insert into buses (bname, bus_no, owner_id, from_loc, from_time, to_loc,to_time,fare,approved) values ('";
    $sql .= $_POST['bname'] . "','" . $_POST['bus_no'] . "','" . $_POST['owner_id'] . "','" . $_POST['from_loc'] . "','";
    $sql .= $_POST['from_time'] . "','" . $_POST['to_loc'] . "','" . $_POST['to_time'] . "','00','1')";

    $place1= $_POST['from_loc'];
    $sql1= "insert into locations (name) values('$place1')";
    $place2= $_POST['to_loc'];
    $sql2= "insert into locations (name) values('$place2')";


    if ($conn->query($sql)) {
        $conn->query($sql1);
        $conn->query($sql2);
        echo '<script>alert("OK");</script>';
    } else {
        echo '<script>alert("Fail");</script>';
    }

    $conn->close();
}
if(isset($_POST['dltbus'])) {
    require_once 'inc/database.php';
    $conn = initDB();
    $busno= $_POST['bus_no'];
    $sql =" delete from buses where (buses.bus_no= '$busno') ";
    if($conn->query($sql))
    {
        echo '<script>alert("Successfully deleted");</script>';
    }
    else
    {
        echo '<script>alert("Unsuccessfull to delete");</script>';
    }

}

?>

<div class="row mb-2">
    <h4 class="col-md-3">Buses</h4>
    <div class="col-md-8 text-right ml-4">
        <form method="post" action="">
            <input type="text" name="bus" class="form-control-sm" value="<?php echo (isset($_POST['bus'])) ? $_POST['bus'] : ""; ?>">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Search</button>
        </form>
    </div>
</div>
<table width="95%" class="table-con">
    <tr class="head">
        <th>ID</th>
        <th>Bus Name</th>
        <th>Bus No.</th>
        <th>Owner</th>
        <th>From</th>
        <th>Diparture</th>
        <th>To</th>
        <th>Arrival</th>
        <th>Status</th>
    </tr>
    <?php
    require_once 'inc/database.php';
    $conn = initDB();
    $sql = "select *,users.uname as owner,buses.id as bid from buses, users where owner_id=users.id";
    if (isset($_POST['bus'])) {
        $sql .= " and (bname like '%" . $_POST['bus'] . "%' or bus_no like '%" . $_POST['bus'] . "%')";
    }
    $sql .= " order by approved";
    $res = $conn->query($sql);
    if ($res->num_rows == 0) {
        echo '
    <tr class="row">
        <td colspan="9" class="text-center">No Bus</td>
    </tr>';
    } else {
        while ($row = $res->fetch_assoc()) {
            echo '
        <tr class="content">
            <td>' . $row["bid"] . '</td>
            <td>' . $row["bname"] . '</td>
            <td>' . $row["bus_no"] . '</td>
            <td>' . $row["owner"] . '</td>
            <td>' . $row["from_loc"] . '</td>
            <td>' . $row["from_time"] . '</td>
            <td>' . $row["to_loc"] . '</td>
            <td>' . $row["to_time"] . '</td>
            <td><a href="buses.php?id=' . $row["bid"] . '&toggle=';
            if ($row["approved"])
                echo '0" title="Click to Unapprove"><i class="fa fa-check text-success">';
            else
                echo '1" title="Click to Approve"><i class="fa fa-times text-danger">';
            echo '</i></a></td></tr>';
        }
    }
    $conn->close();
    ?>
</table>

<!-- Button trigger modal -->
<div class="d-flex justify-content-center mt-5">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#btnpress">
        Add Bus
    </button>
    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#btnredpress">Remove Bus</button>
</div>
<!-- Modal -->
<div class="modal fade" id="btnpress" tabindex="-1" role="dialog" aria-labelledby="btnpressLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="btnpressLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="buses.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="formGroupExampleInput">Bus Name</label>
                        <input type="text" class="form-control" name="bname" id="formGroupExampleInput" placeholder="Bus Name">
                    </div>
                    <div class="form-group">
                        <label for="formGroupExampleInput2">Bus Number</label>
                        <input type="text" class="form-control" name="bus_no" id="formGroupExampleInput2" placeholder="Bus Number">
                    </div>
                    <div class="form-group">
                        <label for="formGroupExampleInput">Owner</label>
                        <input type="number" class="form-control" name="owner_id" id="formGroupExampleInput" placeholder="Owner">
                    </div>
                    <div class="form-group">
                        <label for="formGroupExampleInput2">From</label>
                        <input type="text" class="form-control" name="from_loc" id="formGroupExampleInput2" placeholder="From">
                    </div>
                    <div class="form-group">
                        <label for="formGroupExampleInput2">Departure Time</label>
                        <input type="text" class="form-control" name="from_time" id="formGroupExampleInput2" placeholder="Departure Time">
                    </div>
                    <div class="form-group">
                        <label for="formGroupExampleInput2">To</label>
                        <input type="text" class="form-control" name="to_loc" id="formGroupExampleInput2" placeholder="To">
                    </div>
                    <div class="form-group">
                        <label for="formGroupExampleInput2">Arrival Time</label>
                        <input type="text" class="form-control" name="to_time" id="formGroupExampleInput2" placeholder="Arrival Time">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="addbus">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="btnredpress" tabindex="-1" role="dialog" aria-labelledby="btnredpressLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="btnredpressLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="buses.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="formGroupExampleInput">Bus Name</label>
                        <input type="text" class="form-control" name="bname" id="formGroupExampleInput" placeholder="Bus Name">
                    </div>
                    <div class="form-group">
                        <label for="formGroupExampleInput2">Bus Number</label>
                        <input type="text" class="form-control" name="bus_no" id="formGroupExampleInput2" placeholder="Bus Number">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="dltbus">Delete</button>
                </div>
            </form>

        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<?php
t_footer();
?>