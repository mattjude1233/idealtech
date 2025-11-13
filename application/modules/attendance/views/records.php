<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <?php if (!empty($page_title)) : ?>
                    <h1 class="m-0"><?= ucfirst($page_title) ?></h1>
                <?php endif; ?>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ol>
            </div>

            <?php if (check_function('manage_attendance')) : ?>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="javascript:;" class="btn btn-pill btn-success btn-md upload_attendance-btn"> <i class="fa fa-upload"></i> Upload Attendance</a>
                        <a href="javascript:;" class="btn btn-pill btn-warning btn-md text-white add_attendance-btn"> <i class="fa fa-user-plus"></i> Add Attendance</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<!-- Main content -->
<div class="content" <?= empty($page_title) ? "style='padding-top:15px;'" : "" ?>>
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <?php if (check_function('manage_attendance')) : ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="employee_id">Employee</label>
                                        <select class="form-control select2" id="employee_id" name="employee_id">
                                            <option value="">All Employees</option>
                                            <?php if (!empty($employee)) : ?>
                                                <?php foreach ($employee as $emp) : ?>
                                                    <option value="<?= $this->mysecurity->encrypt_url($emp['id']) ?>" <?= ($selected_employee == $emp['id']) ? 'selected' : '' ?>>
                                                        <?= $emp['emp_id'] . ' - ' . $emp['emp_fname'] . ' ' . $emp['emp_lname'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="<?= check_function('manage_attendance') ? 'col-md-3' : 'col-md-4' ?>">
                                <div class="form-group">
                                    <label for="date_from">Date From</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?= $selected_date_from ?>">
                                </div>
                            </div>
                            <div class="<?= check_function('manage_attendance') ? 'col-md-3' : 'col-md-4' ?>">
                                <div class="form-group">
                                    <label for="date_to">Date To</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?= $selected_date_to ?>">
                                </div>
                            </div>
                            <div class="<?= check_function('manage_attendance') ? 'col-md-2' : 'col-md-4' ?>">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button class="btn btn-primary" id="submit-search">
                                            <i class="fa fa-search"></i> Filter
                                        </button>
                                        <a href="<?= base_url('attendance/records') ?>" class="btn btn-secondary">
                                            <i class="fa fa-refresh"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">

                        <table class="table table-hover" id="attendanceNew">
                            <thead>
                                <tr>
                                    <?php if (check_function('manage_attendance')) : ?>
                                        <th>Employee</th>
                                    <?php endif; ?>
                                    <th>Schedule</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Late</th>
                                    <th>Absent/Undertime</th>
                                    <th>Date</th>
                                    <th>Remarks</th>
                                    <th>Last Update</th>

                                    <?php if (check_function('manage_attendance')) : ?>
                                        <th>Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($attendance)) : ?>
                                    <?php foreach ($attendance as $att) :
                                        $shift_start = !empty($att['shift_start']) ? date('h:i A', strtotime($att['shift_start'])) : '-';
                                        $shift_end = !empty($att['shift_end']) ? date('h:i A', strtotime($att['shift_end'])) : '-';

                                    ?>
                                        <tr class="<?= !empty($att['absent']) ? 'danger-bg-subtle' : '' ?>" data-id="<?= $this->mysecurity->encrypt_url($att['id']) ?>">
                                            <?php if (check_function('manage_attendance')) : ?>
                                                <td><?= (!empty($att['emp_fname']) && !empty($att['emp_lname'])) ? $att['emp_fname'] . ' ' . $att['emp_lname'] : 'N/A' ?></td>
                                            <?php endif; ?>
                                            <td><?= "{$shift_start} - {$shift_end}" ?></td>
                                            <td><?= !empty($att['punch_in']) ? date('h:i A', strtotime($att['punch_in'])) : '-' ?></td>

                                            <td><?= !empty($att['punch_out']) ? date('h:i A', strtotime($att['punch_out'])) : '-' ?></td>
                                            <td><?= !empty($att['late']) && $att['late'] != '00:00:00' ? "<span class='text-red'>{$att['late']}<span>" : '-' ?></td>
                                            <td><?= !empty($att['absent']) ? "Absent" : '-' ?></td>
                                            <td><?= date('M d, Y', strtotime($att['date'])) ?></td>
                                            <td><?= $att['notes'] ?></td>
                                            <td class="text-center"><?php
                                                                    $last_update = '';
                                                                    if (!empty($att['date_last_update'] ?? $att['date_added'])) {
                                                                        $date = $att['date_last_update'] ?? $att['date_added'];
                                                                        $last_update = date('M d, Y h:i A', strtotime($date));
                                                                    }

                                                                    $last_by = $att['updated_full_name'] ?? $att['added_full_name'] ?? '';

                                                                    echo '<small title="' . $last_by . '" data-toggle="tooltip">' . $last_update . '</small>';

                                                                    ?></td>
                                            <?php if (check_function('manage_attendance')) : ?>
                                                <td>
                                                    <button class="btn btn-primary btn-sm update_attendance-btn">Edit</button>
                                                    <button class="btn btn-danger btn-sm delete_attendance-btn">Delete</button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="<?= check_function('manage_attendance') ? '10' : '8' ?>" class="text-center">No records found.</td>
                                    </tr>
                                <?php endif; ?>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<div class="modal fade" id="update_attendance-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Update Attendance</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="update_attendance-form">

                <div class="modal-body">
                    <div class="form-group col-12">
                        <label for="Punch_IN">Punch IN</label>
                        <input id="Punch_IN" name="Punch_IN" type="text" class="form-control datetimepicker" placeholder="Enter Punch IN time">
                    </div>

                    <div class="form-group col-12">
                        <label for="Punch_OUT">Punch OUT</label>
                        <input id="Punch_OUT" name="Punch_OUT" type="text" class="form-control datetimepicker" placeholder="Enter Punch OUT time">
                    </div>

                    <div class="form-group col-12">
                        <label for="Late">Late</label>
                        <input name="Late" value="00:00:00" id="Late" class="form-control timepickernew" type="text">
                    </div>

                    <div class="form-group">
                        <label for="attendance_type">Attendance Type <span class="text-danger">*</span></label>
                        <select name="attendance_type" id="attendance_type" class="form-control" required>
                            <option value="" selected disabled hidden>Select Attendance Type</option>
                            <?= admin__lang_select('attendance', 'type') ?>
                        </select>
                    </div>

                    <div class="form-group col-12">
                        <label for="Remarks">Remarks</label>
                        <textarea id="Remarks" name="Remarks" class="form-control" placeholder="Enter remarks"></textarea>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>



<div class="modal fade" id="upload_attendance-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Upload Attendance</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="upload_attendance-form">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="File">File <span class="text-danger">*</span></label>
                                <input type="file" name="File" class="form-control" accept=".csv" required>
                                <small class="form-text text-muted">Accepted formats: .csv </small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12 text-right">
                            <!-- download csv template -->
                            <a href="<?= base_url('dist/templates/attendance_template.csv') ?>" class="btn btn-info btn-xs" download>Download CSV Template</a>
                        </div>
                    </div>

                    <hr>

                    <div class="p-b-10">
                        1. <strong>Type</strong>: Use the following codes:
                        <ul style="columns: 2; -webkit-columns: 2; -moz-columns: 2; list-style-type: disc; margin-left: 20px; padding-left: 0;">
                            <li>'<strong>P</strong>' - Present</li>
                            <li>'<strong>A</strong>' - Absent</li>
                            <li>'<strong>U</strong>' - Undertime</li>
                            <li>'<strong>L</strong>' - Late</li>
                            <li>'<strong>NCNS</strong>' - No Call, No Show</li>
                            <li>'<strong>VL</strong>' - Vacation Leave</li>
                            <li>'<strong>SL</strong>' - Sick Leave</li>
                            <li>'<strong>EL</strong>' - Emergency Leave</li>
                            <li>'<strong>AH</strong>' - Account Holiday</li>
                            <li>'<strong>LWOP</strong>' - Leave Without Pay</li>
                            <li>'<strong>HD</strong>' - Half-Day</li>
                            <li>'<strong>SUS</strong>' - Suspension</li>
                        </ul>

                        2. <strong>Notes</strong>: is optional.
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>


<?php if (check_function('manage_attendance')) : ?>
    <script>
        $(document).ready(function() {
            $('.select2-search-upload').select2({
                theme: 'bootstrap4',
                placeholder: 'Select Employee',
                allowClear: true,
                dropdownParent: $('#upload_attendance-modal')
            });


            $('.upload_attendance-btn').on('click', function() {
                $('#upload_attendance-form')[0].reset();
                $('#upload_attendance-modal').modal('show');

                // Handle form submission
                $('#upload_attendance-form').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);

                    $.ajax({
                        url: '<?= base_url('attendance/uploadAttendance') ?>',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            page_loader_show();
                            $('#upload_attendance-form button[type="submit"]').prop('disabled', true);
                        },
                        success: function(response) {
                            page_loader_hide();
                            if (response.status === 'success') {
                                $.alert({
                                    title: 'Success',
                                    content: response.message || 'Attendance uploaded successfully.',
                                    type: 'green',
                                    backgroundDismiss: true,
                                    buttons: {
                                        ok: {
                                            text: 'OK',
                                            btnClass: 'btn-green',
                                            action: function() {
                                                location.reload();
                                            }
                                        }
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            page_loader_hide();
                            $.alert({
                                title: 'Error',
                                content: 'An error occurred while uploading attendance.',
                                type: 'red',
                                backgroundDismiss: true
                            });
                        },
                    });
                });
            });
        });
    </script>

    <div class="modal fade" id="add_attendance-modal" data-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Update Attendance</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="add_attendance-form">

                    <div class="modal-body">

                        <div class="form-group col-12">
                            <label for="Employee">Employee</label>
                            <select name="Employee" class="form-control select2-search-add" required>
                                <option value="">Select Employee</option>
                                <?php if (!empty($employee)) : ?>
                                    <?php foreach ($employee as $emp) : ?>
                                        <option value="<?= $this->mysecurity->encrypt_url($emp['id']) ?>"><?= $emp['emp_fname'] . ' ' . $emp['emp_lname'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group col-12">
                            <label for="Date">Date</label>
                            <input type="text" name="Date" class="form-control datepicker" placeholder="Select Date" required>
                        </div>

                        <div class="form-group col-12">
                            <label for="shift_start">Shift Start</label>
                            <input id="shift_start" name="Shift_Start" type="text" class="form-control timepickershift" placeholder="Enter Shift Start time">
                        </div>

                        <div class="form-group col-12">
                            <label for="shift_end">Shift End</label>
                            <input id="shift_end" name="Shift_End" type="text" class="form-control timepickershift" placeholder="Enter Shift End time">
                        </div>

                        <div class="form-group col-12">
                            <label for="Punch_IN">Punch IN</label>
                            <input id="Punch_IN" name="Punch_IN" type="text" class="form-control datetimepicker" placeholder="Enter Punch IN time">
                        </div>

                        <div class="form-group col-12">
                            <label for="Punch_OUT">Punch OUT</label>
                            <input id="Punch_OUT" name="Punch_OUT" type="text" class="form-control datetimepicker" placeholder="Enter Punch OUT time">
                        </div>

                        <div class="form-group col-12">
                            <label for="Late">Late</label>
                            <input name="Late" value="00:00:00" id="Late" class="form-control timepickernew" type="text">
                        </div>

                        <div class="form-group">
                            <label for="attendance_type">Attendance Type <span class="text-danger">*</span></label>
                            <select name="attendance_type" id="attendance_type" class="form-control" required>
                                <option value="" selected disabled hidden>Select Attendance Type</option>
                                <?= admin__lang_select('attendance', 'type') ?>
                            </select>
                        </div>

                        <div class="form-group col-12">
                            <label for="Remarks">Remarks</label>
                            <textarea id="Remarks" name="Remarks" class="form-control" placeholder="Enter remarks"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    $(document).ready(function() {

        <?php if (check_function('manage_attendance')) : ?>
            // Initialize Select2 for employee dropdown
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Select an employee',
                allowClear: true
            });
        <?php endif; ?>

        $('.select2-search-add').select2({
            theme: 'bootstrap4',
            placeholder: 'Select Employee',
            allowClear: true,
            dropdownParent: $('#add_attendance-modal')
        });

        $('.datepicker').datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        // 00:00:00 format for timepicker
        $('.timepickernew').inputmask("99:99:99", {
            placeholder: "HH:MM:SS",
            insertMode: false,
            showMaskOnHover: false
        })

        $('.timepickershift').inputmask("h:s t", {
            placeholder: "HH:MM AM/PM",
            alias: "datetime",
            hourFormat: "12",
            insertMode: false,
            casing: "upper"
        })

        $('#submit-search').on('click', function() {
            var emp_id = $('select[name="employee_id"]').val();
            var date_from = $('input[name="date_from"]').val();
            var date_to = $('input[name="date_to"]').val();

            // format date to YYYY-MM-DD for URL
            if (date_from) {
                date_from = moment(date_from, 'YYYY-MM-DD').format('YYYY-MM-DD');
            }

            if (date_to) {
                date_to = moment(date_to, 'YYYY-MM-DD').format('YYYY-MM-DD');
            }

            if (emp_id || date_from || date_to) {
                var url = '<?= base_url('attendance/records') ?>';

                if (date_from && date_to) {
                    url += '/' + date_from + '/' + date_to;
                }

                if (emp_id) {
                    url += '/' + emp_id;
                } else {
                    // get the first employee if none selected
                    var firstEmp = $('.select2-search option:first').val();
                    if (firstEmp) {
                        url += '/' + firstEmp;
                    }
                }

                window.location.href = url;
            } else {
                alert('Please select at least one search criteria.');
            }
        });


        <?php if (check_function('manage_attendance')) : ?>
            $(document).on('click', '.update_attendance-btn', function() {
                var row = $(this).closest('tr');
                var attendanceId = row.data('id');

                // Reset the form
                $('#update_attendance-form')[0].reset();
                $('#update_attendance-modal').find('.modal-title').text('Edit Attendance');

                // Fetch attendance details
                $.ajax({
                    url: '<?= base_url('attendance/getAttendanceDetails') ?>',
                    type: 'POST',
                    data: {
                        attendance_id: attendanceId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#Punch_IN').val(response.data.punch_in_date);
                            $('#Punch_OUT').val(response.data.punch_out_date);
                            $('#Late').val(response.data.late);
                            $('#Remarks').val(response.data.notes);
                            $('#attendance_type').val(response.data.type).trigger('change');
                            $('#update_attendance-modal').data('attendance-id', attendanceId).modal('show');
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Failed to fetch attendance details.');
                    }
                });

                // Handle form submission
                $('#update_attendance-form').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    var formData = $(this).serializeArray();
                    formData.push({
                        name: 'attendance_id',
                        value: attendanceId
                    });

                    $.ajax({
                        url: '<?= base_url('attendance/updateAttendance') ?>',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        beforeSend: function() {
                            page_loader_show();
                            $('#update_attendance-form button[type="submit"]').prop('disabled', true);
                        },
                        success: function(response) {
                            page_loader_hide();
                            if (response.status === 'success') {
                                $.alert({
                                    title: 'Success',
                                    content: response.message || 'Attendance updated successfully.',
                                    type: 'green',
                                    backgroundDismiss: true,
                                    buttons: {
                                        ok: {
                                            text: 'OK',
                                            btnClass: 'btn-green',
                                            action: function() {
                                                location.reload();
                                            }
                                        }
                                    }
                                });
                            } else {
                                $.alert({
                                    title: 'Error',
                                    content: response.message || 'Failed to update attendance.',
                                    type: 'red',
                                    backgroundDismiss: true
                                });
                            }
                        },
                        error: function() {
                            page_loader_hide();
                            $.alert({
                                title: 'Error',
                                content: 'An error occurred while updating attendance.',
                                type: 'red',
                                backgroundDismiss: true
                            });
                        },
                        complete: function() {
                            page_loader_hide();
                            $('#update_attendance-form button[type="submit"]').prop('disabled', false);
                        }
                    });
                });
            });

            $(document).on('click', '.delete_attendance-btn', function() {
                var row = $(this).closest('tr');
                var attendanceId = row.data('id');

                $.confirm({
                    title: 'Confirm Delete',
                    content: 'Are you sure you want to delete this attendance record?',
                    type: 'red',
                    buttons: {
                        confirm: {
                            text: 'Delete',
                            btnClass: 'btn-red',
                            action: function() {
                                $.ajax({
                                    url: '<?= base_url('attendance/deleteAttendance') ?>',
                                    type: 'POST',
                                    data: {
                                        attendance_id: attendanceId
                                    },
                                    dataType: 'json',
                                    beforeSend: function() {
                                        page_loader_show();
                                        $('.delete_attendance-btn').prop('disabled', true);
                                    },
                                    success: function(response) {
                                        page_loader_hide();
                                        if (response.status === 'success') {
                                            row.remove();
                                            $.alert({
                                                title: 'Success',
                                                content: response.message || 'Attendance deleted successfully.',
                                                type: 'green',
                                                backgroundDismiss: true
                                            });
                                        } else {
                                            $.alert({
                                                title: 'Error',
                                                content: response.message || 'Failed to delete attendance.',
                                                type: 'red',
                                                backgroundDismiss: true
                                            });
                                        }
                                    },
                                    error: function() {
                                        page_loader_hide();
                                        $.alert({
                                            title: 'Error',
                                            content: 'An error occurred while deleting attendance.',
                                            type: 'red',
                                            backgroundDismiss: true
                                        });
                                    },
                                    complete: function() {
                                        page_loader_hide();
                                        $('.delete_attendance-btn').prop('disabled', false);
                                    }
                                });
                            }
                        },
                        cancel: {
                            text: 'Cancel',
                            btnClass: 'btn-secondary'
                        }
                    }
                });
            });

            $('.add_attendance-btn').on('click', function() {
                $('#add_attendance-modal').find('.modal-title').text('Add Attendance');
                $('#add_attendance-form')[0].reset();
                $('#add_attendance-modal').modal('show');

                // Handle form submission
                $('#add_attendance-form').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    var formData = $(this).serializeArray();

                    $.ajax({
                        url: '<?= base_url('attendance/addAttendance') ?>',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        beforeSend: function() {
                            page_loader_show();
                            $('#add_attendance-form button[type="submit"]').prop('disabled', true);
                        },
                        success: function(response) {
                            page_loader_hide();
                            if (response.status === 'success') {
                                $.alert({
                                    title: 'Success',
                                    content: response.message || 'Attendance added successfully.',
                                    type: 'green',
                                    backgroundDismiss: true,
                                    buttons: {
                                        ok: {
                                            text: 'OK',
                                            btnClass: 'btn-green',
                                            action: function() {
                                                location.reload();
                                            }
                                        }
                                    }
                                });
                            } else {
                                $.alert({
                                    title: 'Error',
                                    content: response.message || 'Failed to add attendance.',
                                    type: 'red',
                                    backgroundDismiss: true
                                });
                            }
                        },
                        error: function() {
                            page_loader_hide();
                            $.alert({
                                title: 'Error',
                                content: 'An error occurred while adding attendance.',
                                type: 'red',
                                backgroundDismiss: true
                            });
                        },
                        complete: function() {
                            page_loader_hide();
                            $('#add_attendance-form button[type="submit"]').prop('disabled', false);
                        }
                    });
                });
            });
        <?php endif; ?>
    });

    <?php if (!empty($attendance)) : ?>
        $('#attendanceNew').DataTable({
            "responsive": true,
            "paging": false,
            "autoWidth": false,
            "order": [
                [<?= check_function('manage_attendance') ? '6' : '5' ?>, 'desc']
            ], // Order by date descending
            "columnDefs": [{
                "targets": <?= check_function('manage_attendance') ? '[0, 1, 2, 3, 4, 5, 6, 7]' : '[0, 1, 2, 3, 4, 5, 6]' ?>,
                "className": "text-center"
            }],
            "language": {
                "emptyTable": "No attendance records found."
            }
        });
    <?php endif; ?>
</script>