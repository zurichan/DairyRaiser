<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../../includes/classes.php';


$api = new MyAPI($main_conn);
$buffalos = $api->Read('buffalos', 'all');
$total_buffalo = $api->Read('buffalos', 'all', NULL, NULL, true);
$birthdate = date('Y-m-d');

$header = '';
$title = '';

if (isset($_GET['page'])) {

    $page = $_GET['page'];
    $page_arr = array('all', 'in_sick', 'deceased');

    $page_pass = 0;

    (in_array($page, $page_arr)) ? $page_pass++ : NULL;

    $male_buffalo = 0;
    $female_buffalo = 0;
    $sick_buffalo = 0;
    $deceased_buffalo = 0;

    foreach ($buffalos as $buffalo) {
        if ($buffalo->Marked_As == 'Deceased') {
            $deceased_buffalo++;
        } else {
            if ($buffalo->Health_Status == 'Sick') {
                $sick_buffalo++;
            }
            if ($buffalo->Gender == 'Male') {
                $male_buffalo++;
            }
            if ($buffalo->Gender == 'Female') {
                $female_buffalo++;
            }
        }
    }

    if ($page_pass == 1) {

        if ($page == 'all') {

            $header = 'All Buffalo List';
            $title = "All Buffalo List | Dairy Raisers";
        } else if ($page == 'in_sick') {

            $header = 'Sick Buffalo List';
            $title = 'Sick Buffalo List | Dairy Raisers';

            $sick_buffalos = $api->Read('buffalos', 'set', 'Health_Status', 'Sick');
            $row = $api->Read('buffalos', 'set', 'Health_Status', 'Sick', true);
        } else if ($page == 'deceased') {

            $header = 'Deceased Buffalo List';
            $title = 'Deceased Buffalo List | Dairy Raisers';

            $deceased_buffalos = $api->Read('buffalos', 'set', 'Marked_As', 'Deceased');
            $row = $api->Read('buffalos', 'set', 'Marked_As', 'Deceased', true);
        }
    } else {
        $header = 'Error Page';
        $title = "Error Page | Dairy Raisers";
    }
} else {
    $header = 'Error Page';
    $title = "Error Page | Dairy Raisers";
}

/** HEADER */
$path = 2;
require_once '../includes/admin.header.php';
require_once '../includes/admin.sidebar.php';
?>

<!-- HEADER CONTAINER -->
<div class="border-bottom d-flex flex-row justify-content-between align-items-center overflow-hidden pb-2 mb-3">
    <div class="d-flex justify-content-start align-items-center flex-row">
        <div class="header-container bg-primary d-flex flex-row justify-content-end align-items-center">
            <img src="../../img/buffalo3.svg" alt="buffalo" class="img-fluid me-4" style="width: 70px;">
        </div>
        <div class="d-flex flex-column justify-content-center align-items-start me-5">
            <h1 class="lead py-0"><?= $header; ?> <i class="bi bi-view-list ms-1"></i></h1>
            <div class="nav-item d-flex flex-row justify-content-center align-items-center">
                <i class="bi bi-filter-circle-fill me-2"></i>
                <a class="btn btn-sm btn-outline-primary <?php if (isset($_GET['page']) && $page == 'all') echo 'active'; ?> p-1 me-2" href="./buffalo_list.php?page=all">All</a></li>
                <a class="btn btn-sm btn-outline-primary <?php if (isset($_GET['page']) && $page == 'in_sick') echo 'active'; ?> p-1 me-2" href="./buffalo_list.php?page=in_sick">Sick</a>
                <a class="btn btn-sm btn-outline-primary <?php if (isset($_GET['page']) && $page == 'deceased') echo 'active'; ?> p-1 me-2" href="./buffalo_list.php?page=deceased">Deceased</a>
            </div>
        </div>
        <div class="ms-1 d-flex flex-column justify-content-center align-items-center py-2 px-4 rounded bg-primary text-light">
            <h1 class="lead py-0 mb-3" style="font-size: 27px;">Total Buffalo <i class="bi bi-check2-circle"></i> <span class="ms-4">:</span> <span class="fw-bold ms-2"><?= $total_buffalo; ?></span></h1>
            <div class="d-flex flex-row justify-content-between align-items-center text-light">
                <h1 class="lead py-0 opacity-75 text-center d-flex justify-content-between align-items-center  me-4" style="font-size: 12px;">Male <i class="mx-1 bi bi-gender-male"></i> : <span class="ms-1 fw-bold"><?= $male_buffalo; ?></span></h1>
                <h1 class="lead py-0 opacity-75 text-center d-flex justify-content-between align-items-center  me-4" style="font-size: 12px;">Female <i class="mx-1 bi bi-gender-female"></i> : <span class="ms-1 fw-bold"><?= $female_buffalo; ?></span></h1>
                <h1 class="lead py-0 opacity-75 text-center d-flex justify-content-between align-items-center  me-4" style="font-size: 12px;">Sick
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mx-1 bi bi-virus2" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 0a1 1 0 0 0-1 1v1.143c0 .557-.407 1.025-.921 1.24-.514.214-1.12.162-1.513-.231l-.809-.809a1 1 0 1 0-1.414 1.414l.809.809c.393.394.445.999.23 1.513C3.168 6.593 2.7 7 2.142 7H1a1 1 0 0 0 0 2h1.143c.557 0 1.025.407 1.24.921.214.514.162 1.12-.231 1.513l-.809.809a1 1 0 0 0 1.414 1.414l.809-.809c.394-.394.999-.445 1.513-.23.514.214.921.682.921 1.24V15a1 1 0 1 0 2 0v-1.143c0-.557.407-1.025.921-1.24.514-.214 1.12-.162 1.513.231l.809.809a1 1 0 0 0 1.414-1.414l-.809-.809c-.394-.394-.445-.999-.23-1.513.214-.514.682-.921 1.24-.921H15a1 1 0 1 0 0-2h-1.143c-.557 0-1.025-.407-1.24-.921-.214-.514-.163-1.12.231-1.513l.809-.809a1 1 0 1 0-1.415-1.414l-.808.809c-.394.393-.999.445-1.513.23C9.407 3.168 9 2.7 9 2.142V1a1 1 0 0 0-1-1Zm2 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM7 7a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm1 5a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm4-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" />
                    </svg> : <span class="ms-1 fw-bold"><?= $sick_buffalo; ?></span>
                </h1>
                <h1 class="lead py-0 opacity-75 text-center d-flex justify-content-between align-items-center " style="font-size: 12px;">Deceased <i class="mx-1 bi bi-x-circle"></i> : <span class="ms-1 fw-bold"><?= $deceased_buffalo; ?></span></h1>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['page']) && $page == 'all') { ?>
        <div class="">
            <button type="button" class=" btn btn-primary" id="add-buffalo-btn" data-bs-toggle="modal" data-bs-target="#add-buffalo"><i class="bi bi-patch-plus me-2"></i>Add New Buffalo</button>
        </div>
    <?php } ?>
</div>

<?php
if (isset($_GET['page'])) {

    if ($page_pass == 1 && $page == 'all') {
?>
        <!-- Add -->
        <div class="modal fade" id="add-buffalo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form class="modal-content" method="POST" action="../process/process-buffalo.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Add Buffalo: </span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label" for="buffalo_name">Name:</label>
                        <input type="text" class="form-control mb-5" name="buffalo_name" id="buffalo_name" required>

                        <label class="form-label" for="buffalo_gender">Buffalo Gender:</label>
                        <select class="form-select mb-5" name="buffalo_gender" id="buffalo_gender">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>

                        <label class="form-label" for="buffalo_weight">Buffalo Weight:</label>
                        <div class="input-group mb-5">
                            <input type="number" class="form-control" name="buffalo_weight" id="buffalo_weight" required>
                            <span class="py-2 px-4 bg-secondary text-white text-center">lbs</span>
                        </div>

                        <label class="form-label" for="health_status">Health Status:</label>
                        <select class="form-select mb-5" name="health_status" id="health_status">
                            <option value="Normal">Normal</option>
                            <option value="Sick">Sick</option>
                        </select>

                        <label class="form-label" for="buffalo_birthdate">Birthdate:</label>
                        <input type="date" class="form-control mb-5" value="<?= $birthdate; ?>" name="buffalo_birthdate" id="buffalo_birthdate" required>

                        <label class="form-label" for="lactation_cycle">Lactation Cycle:</label>
                        <select class="form-select mb-5" name="lactation_cycle" id="lactation_cycle" readonly>
                            <option value="Not Pregnant">Not Pregnant</option>
                            <option value="Early Lactation">Early Lactation</option>
                            <option value="Middle Lactation">Middle Lactation</option>
                            <option value="Late Lactation">Late Lactation</option>
                            <option value="Dry Period">Dry Period</option>
                        </select>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="add-matured-buffalo" name="add-matured-buffalo" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update -->
        <div class="modal fade" id="update_buffalo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form class="modal-content" method="POST" action="../process/process-buffalo.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title toggle_action_update" id="staticBackdropLabel">Update Buffalo:</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="buffalo_id" class="action_buffalo_id" id="update_buffalo_id" readonly>

                        <label class="form-label" for="update_name">Name:</label>
                        <input type="text" class="form-control mb-5" name="update_name" id="update_name" required>

                        <label class="form-label" for="update_weight">Buffalo Weight:</label>
                        <div class="input-group mb-5">
                            <input type="number" class="form-control" name="update_weight" id="update_weight" required>
                            <span class="py-2 px-4 bg-secondary text-white text-center">lbs</span>
                        </div>

                        <label class="form-label" for="update_status">Health Status:</label>
                        <select class="form-select mb-5" name="update_status" id="update_status">
                            <option value="Normal">Normal</option>
                            <option value="Sick">Sick</option>
                        </select>

                        <label class="form-label" for="update_cycle">Lactation Cycle:</label>
                        <select class="form-select mb-5" name="update_cycle" id="update_cycle" readonly>
                            <option value="Not Pregnant">Not Pregnant</option>
                            <option value="Early Lactation">Early Lactation</option>
                            <option value="Middle Lactation">Middle Lactation</option>
                            <option value="Late Lactation">Late Lactation</option>
                            <option value="Dry Period">Dry Period</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="update_buffalo" name="update_buffalo" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ALL BUFFALOS -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="buffaloTable" style="width: 100%; font-size: 14px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Gender</th>
                        <th>Weight</th>
                        <th>Health Status</th>
                        <th>Birthdate</th>
                        <th>Lactation Cycle</th>
                        <th>Modified at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="buffaloData">
                    <?php
                    $index = 1;
    
                    foreach ($buffalos as $buffalo) :
                        if ($buffalo->Marked_As !== 'Deceased') :
                            if ($buffalo->Marked_As !== 'Sold') :
                    ?>
                                <tr>
                                    <td class="fw-bold"><?= $index; ?>.</td>
                                    <td data-name="buffalo_name"><?= $buffalo->Name; ?></td>
                                    <td data-target="buffalo_id"><?= $buffalo->Buffalo_id; ?></td>
                                    <td data-target="gender"><?= $buffalo->Gender; ?></td>
                                    <td data-target="weight"><?= $buffalo->Weight; ?> kg</td>
                                    <td data-target="health_status"><?= $buffalo->Health_Status; ?></td>
                                    <td><?= $buffalo->Birthdate; ?></td>
                                    <td data-target="cycle"><?= $buffalo->Lactation_Cycle; ?></td>
                                    <td><?= $buffalo->lastUpdate; ?></td>
                                    <td>
                                        <a class=" dropdown-toggle btn btn-sm btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                                        <ul class="dropdown-menu dropdown-sm">
                                            <li><button type="button" class="dropdown-item" data-bs-target="#update_buffalo" data-bs-toggle="modal" data-btn="update_btn">Update</button></li>
                                            <li><button type="button" class="dropdown-item" data-btn="marked_sick_btn">Marked As Sick</button></li>
                                            <li><button type="button" class="dropdown-item" data-btn="marked_deceased_btn">Marked As Deceased</button></li>
                                            <li><button type="button" class="dropdown-item" data-btn="remove_buffalo_btn">Remove</button></li>
                                        </ul>
                                    </td>
                                </tr>
                    <?php
                                $index++;
                            endif;
                        endif;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    <?php
    } else if ($page_pass == 1 && $page == 'in_sick') {
    ?>
        <!-- MAIN CONTENTS -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="sickTable" style="width: 100%; font-size: 14px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Gender</th>
                        <th>Weight</th>
                        <th>Birthdate</th>
                        <th>Lactation Cycle</th>
                        <th>Modified at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="sickData" class="text-center" style="vertical-align: baseline;">
                    <?php
                    $index = 1;

                    foreach ($sick_buffalos as $buffalo) : ?>
                        <tr>
                            <td class="fw-bold"><?= $index; ?>.</td>
                            <td><?= $buffalo->Name; ?></td>
                            <td data-target="buffalo_id"><?= $buffalo->Buffalo_id; ?></td>
                            <td><?= $buffalo->Gender; ?></td>
                            <td><?= $buffalo->Weight; ?> kg</td>
                            <td><?= $buffalo->Birthdate; ?></td>
                            <td><?= $buffalo->Lactation_Cycle; ?></td>
                            <td><?= $buffalo->lastUpdate; ?></td>
                            <td>
                                <a class=" dropdown-toggle btn btn-sm btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                                <ul class="dropdown-menu dropdown-sm">
                                    <li><button type="button" class="dropdown-item" data-btn="marked_healthy_btn">Mark as Healthy</a></li>
                                    <li><button type="button" class="dropdown-item" data-btn="remove_sick_btn">Remove</button></li>
                                </ul>
                            </td>
                        </tr>
                    <?php
                        $index++;
                    endforeach;
                    ?>

                </tbody>
            </table>
        </div>
    <?php
    } else if ($page_pass == 1 && $page == 'deceased') {
    ?>
        <!-- DECEASED CONTENT -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="deceasedTable" style="width: 100%; font-size: 14px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>I.D</th>
                        <th>Gender</th>
                        <th>Weight</th>
                        <th>Birthdate</th>
                        <th>Caused</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="deceasedData" class="text-center">
                    <?php
                    $index = 1;
                    if ($row >= 1) {
                        foreach ($deceased_buffalos as $buffalo) : ?>
                            <tr>
                                <td class="fw-bold"><?= $index; ?>.</td>
                                <td><?= $buffalo->Name; ?></td>
                                <td data-target="buffalo_id"><?= $buffalo->Buffalo_id; ?></td>
                                <td><?= $buffalo->Gender; ?></td>
                                <td><?= $buffalo->Weight; ?> kg</td>
                                <td><?= $buffalo->Birthdate; ?></td>
                                <td><?= $buffalo->Comments; ?></td>
                                <td><?= $buffalo->lastUpdate; ?></td>
                                <td>
                                    <a class=" dropdown-toggle btn btn-sm btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                                    <ul class="dropdown-menu dropdown-sm">
                                        <li><button type="button" class="dropdown-item" data-btn="remove_buffalo_btn">Remove</button></li>
                                    </ul>
                                </td>
                            </tr>
                        <?php
                            $index++;
                        endforeach;
                    } else {

                        ?>
                        <tr>
                            <td colspan="10">
                                <p class="lead ">No matching records found.</p>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php
    } else {
        /** Something Went Wrong */
    ?>
        <div class="d-flex flex-column justify-content-around align-items-center my-4 w-100">
            <img src="../../img/undraw_page_not_found_re_e9o6.svg" class="img-fluid mb-4" alt="page not found">
            <h1 class="fw-bold">Page Not Found</h1>
        </div>
    <?php
    }
} else {
    /** Something Went Wrong */
    ?>
    <div class="d-flex flex-column justify-content-around align-items-center my-4 w-100">
        <img src="../../img/undraw_page_not_found_re_e9o6.svg" class="img-fluid mb-4" style="width: 400px;" alt="page not found">
        <h1 class="fw-bold">Page Not Found</h1>
    </div>
<?php
}
?>


<!-- FOOTER -->
<?php require_once '../includes/admin.footer.php'; ?>

<!-- ADDS-ONS -->
<?php if (isset($_SESSION['buffalo-message'])) : ?>
    <script>
        swal(
            "<?= $_SESSION['buffalo-message']['title']; ?>",
            "<?= $_SESSION['buffalo-message']['body']; ?>",
            "<?= $_SESSION['buffalo-message']['type']; ?>"
        );
    </script>
<?php endif;
unset($_SESSION['buffalo-message']);
?>

<?php if (isset($_GET['page']) && $page == 'all') { ?>
    <script>
        $('#buffaloTable').DataTable();
        const cycle = $('#update_cycle').html();
        const add_cycle = $('#lactation_cycle').html();

        var buffalo_id = Array.from($('[data-target="buffalo_id"]'));
        var gender = Array.from($('[data-target="gender"]'));
        var weight = Array.from($('[data-target="weight"]'));
        var health_status = Array.from($('[data-target="health_status"]'));
        var buffalo_cycle = Array.from($('[data-target="cycle"]'));

        var remove_btn = Array.from($('[data-btn="remove_buffalo_btn"]'));
        var update_btn = Array.from($('[data-btn="update_btn"]'));
        var deceased_btn = Array.from($('[data-btn="marked_deceased_btn"]'));
        var sick_btn = Array.from($('[data-btn="marked_sick_btn"]'));

        if ($('#buffalo_gender').val() == 'Male') {
            $('#lactation_cycle').find('option').remove().end()
                .append('<option value="N/A" selected>Not Applicable</option>');
        } else if ($('#buffalo_gender').val() == 'Female') {
            $('#lactation_cycle').html(add_cycle);
        }

        $('#buffalo_gender').change(() => {

            if ($('#buffalo_gender').val() == 'Male') {
                $('#lactation_cycle').find('option').remove().end()
                    .append('<option value="N/A" selected>Not Applicable</option>');
            } else if ($('#buffalo_gender').val() == 'Female') {
                $('#lactation_cycle').html(add_cycle);
            }
        })

        function reRead() {
            buffalo_id = Array.from($('[data-target="buffalo_id"]'));
            gender = Array.from($('[data-target="gender"]'));
            weight = Array.from($('[data-target="weight"]'));
            health_status = Array.from($('[data-target="health_status"]'));
            buffalo_cycle = Array.from($('[data-target="cycle"]'));

            remove_btn = Array.from($('[data-btn="remove_buffalo_btn"]'));
            update_btn = Array.from($('[data-btn="update_btn"]'));
            deceased_btn = Array.from($('[data-btn="marked_deceased_btn"]'));
            sick_btn = Array.from($('[data-btn="marked_sick_btn"]'));

            updateBtn();
            removeBtn();
            deceasedBtn();
            sickBtn();
        }

        var search = $('input[type=search]');

        search.keydown(() => {
            reRead();
        })

        function updateBtn() {
            update_btn.forEach((data, i) => {
                $(data).click(() => {
                    reRead();
                    var name = Array.from($('[data-name="buffalo_name"]'));
                    var main_weight = weight[i].innerText.replaceAll(' kg', '');
                    var health_stats = 0;
                    (health_status[i].innerText == 'Normal') ? health_stats = 0: health_stats = 1;

                    $('#update_name').val(name[i].innerText);
                    $('.action_buffalo_id').val(buffalo_id[i].innerText);
                    $('#update_weight').val(main_weight);
                    $('#update_status option').eq(health_stats).prop('selected', true);

                    if (gender[i].innerText == 'Male') {
                        $('#update_cycle').find('option').remove().end()
                            .append('<option value="N/A" selected>Not Applicable</option>');
                    } else if (gender[i].innerText == 'Female') {
                        $('#update_cycle').html(cycle);
                        $('#update_cycle').val(buffalo_cycle[i].innerText);
                    }
                });
            });
        };

        function removeBtn() {
            remove_btn.forEach((data, i) => {
                $(data).click(() => {
                    swal({
                        title: "Remove Buffalo: " + buffalo_id[i].innerText,
                        text: "Once deleted, you will not be able to recover.",
                        icon: "warning",
                        closeOnClickOutside: false,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                type: 'POST',
                                url: '../process/process-buffalo.php',
                                data: {
                                    position: 'all',
                                    remove_buffalo: buffalo_id[i].innerText
                                },
                                success: ((response) => {

                                    if (response) {
                                        $('#buffaloData').html(response);
                                    }
                                    reRead();

                                })
                            });
                            swal("Poof! Your Buffalo has been deleted!", {
                                icon: "success",
                                closeOnClickOutside: false
                            });
                        };
                    });
                });
            });
        };

        function deceasedBtn() {
            deceased_btn.forEach((data, i) => {
                $(data).click(() => {
                    swal({
                        title: "Marked Buffalo : " + buffalo_id[i].innerText + " as Deceased?",
                        closeOnClickOutside: false,
                        buttons: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                type: 'POST',
                                url: '../process/process-buffalo.php',
                                data: {
                                    position: 'all-deceased',
                                    marked_buffalo: buffalo_id[i].innerText
                                },
                                success: ((response) => {

                                    if (response) {
                                        $('#buffaloData').html(response);
                                    }
                                    reRead();

                                })
                            });
                            swal("Poof! Your Buffalo has been marked as Deceased!", {
                                icon: "success",
                                closeOnClickOutside: false
                            });
                        };
                    });
                });
            });
        };

        function sickBtn() {
            sick_btn.forEach((data, i) => {
                $(data).click(() => {
                    swal({
                        title: "Marked Buffalo : " + buffalo_id[i].innerText + " as In Sick?",
                        closeOnClickOutside: false,
                        buttons: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                type: 'POST',
                                url: '../process/process-buffalo.php',
                                data: {
                                    position: 'all-sick',
                                    marked_buffalo: buffalo_id[i].innerText
                                },
                                success: ((response) => {

                                    if (response) {
                                        $('#buffaloData').html(response);
                                    }
                                    reRead();

                                })
                            });
                            swal("Poof! Your Buffalo has been marked as sick!", {
                                icon: "success",
                                closeOnClickOutside: false
                            });
                        };
                    });
                });
            });
        }

        updateBtn();
        removeBtn();
        deceasedBtn();
        sickBtn();
    </script>
<?php } else if (isset($_GET['page']) && $page == 'in_sick') { ?>
    <script>
        $(document).ready(function() {
            $('#sickTable').DataTable();
            var buffalo_id = Array.from($('[data-target="buffalo_id"]'));
            var remove_btn = Array.from($('[data-btn="remove_sick_btn"]'));
            var marked_btn = Array.from($('[data-btn="marked_healthy_btn"]'));

            var search = $('input[type=search]');

            search.keydown(() => {
                buffalo_id = Array.from($('[data-target="buffalo_id"]'));
                remove_btn = Array.from($('[data-btn="remove_sick_btn"]'));
                marked_btn = Array.from($('[data-btn="marked_healthy_btn"]'));

                markBtns();
                RemoveBtns();
            })

            function markBtns() {
                marked_btn.forEach((data, i) => {
                    $(data).click(() => {
                        swal({
                            title: "Mark Buffalo: " + buffalo_id[i].innerText + " as Healthy?",
                            closeOnClickOutside: false,
                            buttons: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                $.ajax({
                                    type: 'POST',
                                    url: '../process/process-buffalo.php',
                                    data: {
                                        position: 'sick',
                                        marked_buffalo: buffalo_id[i].innerText
                                    },
                                    success: ((response) => {
                                        if (response) {
                                            $('#sickData').html(response);
                                        }
                                        buffalo_id = Array.from($('[data-target="buffalo_id"]'));
                                        remove_btn = Array.from($('[data-btn="remove_sick_btn"]'));
                                        marked_btn = Array.from($('[data-btn="marked_healthy_btn"]'));

                                        markBtns();
                                        RemoveBtns();
                                    })
                                });
                                swal("Poof! Your Buffalo is healthy!", {
                                    icon: "success",
                                    closeOnClickOutside: false
                                });
                            }
                        });
                    });
                });
            }

            function RemoveBtns() {
                remove_btn.forEach((data, i) => {
                    $(data).click(() => {
                        swal({
                            title: "Remove Buffalo: " + buffalo_id[i].innerText,
                            text: "Once deleted, you will not be able to recover.",
                            icon: "warning",
                            closeOnClickOutside: false,
                            buttons: true,
                            dangerMode: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                $.ajax({
                                    type: 'POST',
                                    url: '../process/process-buffalo.php',
                                    data: {
                                        position: 'sick',
                                        remove_buffalo: buffalo_id[i].innerText
                                    },
                                    success: ((response) => {
                                        console.log(response);
                                        if (response) {
                                            $('#sickData').html(response);
                                        }
                                        buffalo_id = Array.from($('[data-target="buffalo_id"]'));
                                        remove_btn = Array.from($('[data-btn="remove_sick_btn"]'));
                                        marked_btn = Array.from($('[data-btn="marked_healthy_btn"]'));

                                        markBtns();
                                        RemoveBtns();
                                    })
                                });
                                swal("Poof! Your Buffalo has been deleted!", {
                                    icon: "success",
                                    closeOnClickOutside: false
                                });
                            }
                        });
                    });
                });
            };

            RemoveBtns();
            markBtns();
        });
    </script>
<?php } else if (isset($_GET['page']) && $page == 'deceased') { ?>
    <script>
        $(document).ready(function() {
            $('#deceasedTable').DataTable();

            var remove_btn = Array.from($('[data-btn="remove_buffalo_btn"]'));
            var buffalo_id = Array.from($('[data-target="buffalo_id"]'));

            var search = $('input[type=search]');

            search.keydown(() => {
                remove_btn = Array.from($('[data-btn="remove_buffalo_btn"]'));
                buffalo_id = Array.from($('[data-target="buffalo_id"]'));

                RemoveBtns();
            })

            function RemoveBtns() {
                remove_btn.forEach((data, i) => {
                    $(data).click(() => {
                        swal({
                            title: "Remove Buffalo: " + buffalo_id[i].innerText,
                            text: "Once deleted, you will not be able to recover.",
                            icon: "warning",
                            closeOnClickOutside: false,
                            buttons: true,
                            dangerMode: true,
                        }).then((willDelete) => {
                            if (willDelete) {
                                $.ajax({
                                    type: 'POST',
                                    url: '../process/process-buffalo.php',
                                    data: {
                                        position: 'deceased',
                                        remove_buffalo: buffalo_id[i].innerText
                                    },
                                    success: ((response) => {
                                        console.log(response);
                                        if (response) {
                                            $('#deceasedData').html(response);
                                        }
                                        remove_btn = Array.from($('[data-btn="remove_buffalo_btn"]'));
                                        buffalo_id = Array.from($('[data-target="buffalo_id"]'));

                                        RemoveBtns();

                                    })
                                });
                                swal("Poof! Your Buffalo has been deleted!", {
                                    icon: "success",
                                    closeOnClickOutside: false
                                });
                            }
                        });
                    });
                });
            };

            RemoveBtns();
        });
    </script>
<?php } ?>