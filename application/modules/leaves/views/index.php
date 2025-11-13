<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <?php if (!empty($page_title)) : ?>
                    <h1 class="m-0"><?= ucfirst($page_title) ?></h1>
                <?php endif; ?>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Administration</li>
                    <li class="breadcrumb-item active">Leaves</li>
                </ol>
            </div>

            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="javascript:;" class="btn btn-pill btn-info btn-md text-white" data-toggle="modal" data-target="#filter-modal"> <i class="fa fa-filter"></i> Filter</a>
                    <a href="javascript:;" class="btn btn-pill btn-warning btn-md text-white add_leave-btn"> <i class="fa fa-user-plus"></i> Add Leave</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .sil-balance-badge {
        font-size: 0.85em;
        font-weight: 600;
    }
    
    .bg--success-soft {
        background-color: #d4edda;
        color: #155724;
    }
    
    .bg--warning-soft {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .bg--danger-soft {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .bg--secondary-soft {
        background-color: #e2e3e5;
        color: #495057;
    }
    
    .bg--primary-soft {
        background-color: #cce7ff;
        color: #004085;
    }

    .filter-info {
        font-size: 0.9em;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        background-color: #f8f9fa;
        border-left: 3px solid #007bff;
    }

    #sil-balance-info {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        background-color: #f8f9fa;
    }
</style>


<!-- Main content -->
<div class="content" <?= empty($page_title) ? "style='padding-top:15px;'" : "" ?>>
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Leave Records</h3>
                        <div class="card-tools">
                            <?php if (!empty($_GET)): ?>
                            <small class="text-muted">
                                <i class="fa fa-filter"></i> Filtered results 
                                <?php if (!empty($_GET['emp_id'])): ?>
                                    | Employee: <strong><?= htmlspecialchars($_GET['emp_id']) ?></strong>
                                <?php endif; ?>
                                <?php if (!empty($_GET['leave_type'])): ?>
                                    | Type: <strong><?= htmlspecialchars($_GET['leave_type']) ?></strong>
                                <?php endif; ?>
                                <?php if (!empty($_GET['status'])): ?>
                                    | Status: <strong><?= ucfirst($_GET['status']) ?></strong>
                                <?php endif; ?>
                                <?php if (!empty($_GET['date_from']) || !empty($_GET['date_to'])): ?>
                                    | Date: <strong><?= !empty($_GET['date_from']) ? $_GET['date_from'] : 'Start' ?> to <?= !empty($_GET['date_to']) ? $_GET['date_to'] : 'End' ?></strong>
                                <?php endif; ?>
                                <a href="<?= base_url('leaves') ?>" class="btn btn-xs btn-outline-secondary ml-2">Clear Filters</a>
                            </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table" id="leaveTable">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Leave Type</th>
                                        <th>Current SIL</th>
                                        <th>SIL Used</th>
                                        <th>Scheduled</th>
                                        <th>No. of Hours</th>
                                        <th>Actual Schedule</th>
                                        <th>Actual Hours</th>
                                        <th>Date Filed</th>
                                        <th>Supervisor Status</th>
                                        <th>Manager Status</th>
                                        <th>HR Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($list)) : ?>
                                        <?php foreach ($list as $leaves) : ?>
                                            <tr data-lid="<?= $this->mysecurity->encrypt_url($leaves['leave_id']); ?>">
                                                <td style="white-space: pre;"><?= $leaves['emp_fname'] . ' ' . $leaves['emp_lname'] ?></td>
                                                <td><a href="javascript:;" class="leave_reason_show-btn" style="white-space: pre;" data-toggle="tooltip" title="<?= truncate(strip_tags($leaves['reason']), 100) ?>"> <?= $leaves['leave_type'] ?> <i class="fa fa-question m-l-3" style="font-size: 11px;color: #fff !important;background: #007afd;width: 18px;line-height: 18px;border-radius: 50px;text-align: center;"></i> </a></td>

                                                <td>
                                                    <?php if (isset($leaves['current_sil'])) : ?>
                                                        <?php 
                                                        $sil_balance = $leaves['current_sil'];
                                                        $badge_class = 'bg--success-soft';
                                                        if ($sil_balance <= 0) {
                                                            $badge_class = 'bg--danger-soft';
                                                        } elseif ($sil_balance <= 20) {
                                                            $badge_class = 'bg--warning-soft';
                                                        }
                                                        ?>
                                                        <span class="badge <?= $badge_class ?> sil-balance-badge" title="Current SIL Balance">
                                                            <?= number_format($sil_balance, 2) ?>
                                                        </span>
                                                    <?php else : ?>
                                                        <span class="badge bg--secondary-soft">---</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= number_format($leaves['sil'], 2) ?></td>
                                                <td><span class="badge bg--primary-soft"><?= date('M d, Y h:i A', strtotime($leaves['date_from'])) ?></span> <span class="badge bg--primary-soft"><?= date('M d, Y h:i A', strtotime($leaves['date_to'])) ?></span></td>

                                                <!-- Compute Number of Hours Only not Minutes -->
                                                <td>
                                                    <?php
                                                    $lunch_minutes = 60; // lunch break in minutes

                                                    $dateFrom = new DateTime($leaves['date_from']);
                                                    $dateTo   = new DateTime($leaves['date_to']);

                                                    $interval = $dateFrom->diff($dateTo);

                                                    // total hours from days + hours
                                                    $hours = ($interval->days * 24) + $interval->h;

                                                    // add fractional hours from minutes
                                                    $minutes = $interval->i;
                                                    $totalHours = $hours + ($minutes / 60);

                                                    // subtract lunch break
                                                    $totalHours -= ($lunch_minutes / 60);

                                                    echo number_format($totalHours, 2);
                                                    ?>
                                                </td>

                                                <td>

                                                    <?php if ($leaves['actual_date_from'] && $leaves['actual_date_to']): ?>
                                                        <span class="badge bg--primary-soft"><?= date('M d, Y h:i A', strtotime($leaves['actual_date_from'])) ?></span> <span class="badge bg--primary-soft"><?= date('M d, Y h:i A', strtotime($leaves['actual_date_to'])) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg--warning-soft">---</span>
                                                    <?php endif; ?>

                                                </td>

                                                <td>
                                                    <?php
                                                    if (!empty($leaves['actual_date_from']) && !empty($leaves['actual_date_to'])) {
                                                        $from = new DateTime($leaves['actual_date_from']);
                                                        $to   = new DateTime($leaves['actual_date_to']);
                                                        if ($to <= $from) {
                                                            echo number_format(0, 2);
                                                            return;
                                                        }

                                                        // total minutes
                                                        $totalMin = ($to->getTimestamp() - $from->getTimestamp()) / 60;

                                                        // convert lunch start to 24h
                                                        $lh = strtolower($this->_lunchperiod) === 'pm'
                                                            ? ($this->_lunchstart == 12 ? 12 : ($this->_lunchstart % 12) + 12)
                                                            : ($this->_lunchstart % 12);

                                                        // first lunch window to check (on 'from' date at lunch start)
                                                        $check = (clone $from)->setTime($lh, 0, 0);

                                                        // if lunch time on that day is before 'from', move to next day
                                                        if ($check < $from) {
                                                            $check->modify('+1 day');
                                                        }

                                                        // accumulate overlapped lunch minutes across days
                                                        $overlapMin = 0;
                                                        while ($check < $to) {
                                                            $lunchStart = clone $check;
                                                            $lunchEnd   = (clone $check)->modify("+{$this->_lunchtime} minutes");

                                                            // overlap = max(0, min(to, lunchEnd) - max(from, lunchStart))
                                                            $start = max($from->getTimestamp(), $lunchStart->getTimestamp());
                                                            $end   = min($to->getTimestamp(),   $lunchEnd->getTimestamp());
                                                            if ($end > $start) {
                                                                $overlapMin += min($this->_lunchtime, ($end - $start) / 60);
                                                            }
                                                            $check->modify('+1 day');
                                                        }

                                                        $adjMin = max(0, $totalMin - $overlapMin);
                                                        echo number_format($adjMin / 60, 2);
                                                    } else {
                                                        echo '<span class="badge bg--warning-soft">---</span>';
                                                    }
                                                    ?>
                                                </td>

                                                <td><span class="badge bg--primary-soft"><?= date('M d, Y h:i A', strtotime($leaves['date_filed'])) ?></span></td>

                                                <td><a href="javascript:;" class="leave_approval-btn" data-approval="<?= $this->mysecurity->encrypt_url('sv') ?>"><?= leave_status_button($leaves['sv_status']) ?></a></td>

                                                <td><a href="javascript:;" class="leave_approval-btn" data-approval="<?= $this->mysecurity->encrypt_url('mgr') ?>"><?= leave_status_button($leaves['mgr_status']) ?></a></td>

                                                <td><a href="javascript:;" class="leave_hr_confirm-btn"><?= leave_status_button($leaves['hr_status']) ?></a></td>

                                                <td class="text-nowrap">
                                                    <a href="javascript:;" class="btn btn-xs btn-warning update_leave-btn m-r-5"> <i class="fa fa-edit"></i></a>
                                                    <a href="javascript:;" class="btn btn-xs btn-danger cancel_leave-btn" data-toggle="tooltip" title="Cancel Leave"> <i class="fa fa-times"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="13" class="text-center">No record found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<!-- Filter Modal -->
<div class="modal fade" id="filter-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Filter Leaves</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="filter-form" method="get" action="<?= base_url('leaves') ?>">
                <div class="modal-body">
                    <?php if (check_function('manage_leave')) : ?>
                    <div class="form-group">
                        <label for="filter_employee">Employee</label>
                        <select name="emp_id" id="filter_employee" class="form-control">
                            <option value="">All Employees</option>
                            <?php 
                            $employees = $this->model->getBySQL("SELECT emp_id, emp_fname, emp_lname FROM employees WHERE status = 0 ORDER BY emp_lname, emp_fname");
                            if (!empty($employees)) :
                                foreach ($employees as $emp) :
                            ?>
                                <option value="<?= $emp['emp_id'] ?>" <?= (isset($_GET['emp_id']) && $_GET['emp_id'] == $emp['emp_id']) ? 'selected' : '' ?>>
                                    <?= $emp['emp_fname'] . ' ' . $emp['emp_lname'] ?>
                                </option>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="filter_leave_type">Leave Type</label>
                        <select name="leave_type" id="filter_leave_type" class="form-control">
                            <option value="">All Leave Types</option>
                            <?php 
                            $leave_types = $this->model->getBySQL("SELECT keyid, value FROM admin_lang WHERE keyword = 'leave|type' AND status = 1 ORDER BY value");
                            if (!empty($leave_types)) :
                                foreach ($leave_types as $type) :
                            ?>
                                <option value="<?= $type['keyid'] ?>" <?= (isset($_GET['leave_type']) && $_GET['leave_type'] == $type['keyid']) ? 'selected' : '' ?>>
                                    <?= $type['value'] ?>
                                </option>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filter_status">Status</label>
                        <select name="status" id="filter_status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= (isset($_GET['status']) && $_GET['status'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                            <option value="denied" <?= (isset($_GET['status']) && $_GET['status'] == 'denied') ? 'selected' : '' ?>>Denied</option>
                            <option value="confirmed" <?= (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : '' ?>>Confirmed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filter_date_from">Date From</label>
                        <input type="date" name="date_from" id="filter_date_from" class="form-control" value="<?= isset($_GET['date_from']) ? $_GET['date_from'] : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="filter_date_to">Date To</label>
                        <input type="date" name="date_to" id="filter_date_to" class="form-control" value="<?= isset($_GET['date_to']) ? $_GET['date_to'] : '' ?>">
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <div>
                        <a href="<?= base_url('leaves') ?>" class="btn btn-secondary">Clear</a>
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- static modal -->
<div class="modal fade" id="add_leave-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Leave</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="add_leave-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="leave_type">Leave Type <span class="text-danger">*</span></label>
                        <select name="leave_type" id="leave_type" class="form-control" required>
                            <option value="" selected disabled hidden>Select Leave Type</option>
                            <?= admin__lang_select('leave', 'type') ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="leave_from">From <span class="text-danger">*</span></label>
                        <input type="text" name="leave_from" id="leave_from" class="form-control datetimepicker" required>
                    </div>

                    <div class="form-group">
                        <label for="leave_to">To <span class="text-danger">*</span></label>
                        <input type="text" name="leave_to" id="leave_to" class="form-control datetimepicker" required>
                    </div>

                    <?php if (check_function('manage_leave')) : ?>
                        <div class="form-group">
                            <label for="sil_used">SIL Used</label>
                            <input type="number" name="sil_used" id="sil_used" class="form-control" step="0.01" min="0" value="0.00" readonly>
                            <small class="form-text text-muted" id="sil-balance-info">Loading SIL balance...</small>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="leave_reason">Reason <span class="text-danger">*</span></label>
                        <textarea name="leave_reason" id="leave_reason" class="form-control" rows="3" required></textarea>
                    </div>

                    <?php if (check_function('manage_leave')) : ?>
                        <div class="actual_date_con m-t-30" style="display: none;">
                            <hr>

                            <div class="form-group">
                                <label for="actual_date_from">Actual Date From</label>
                                <input type="text" name="actual_date_from" id="actual_date_from" class="form-control datetimepicker" disabled>
                            </div>

                            <div class="form-group">
                                <label for="actual_date_to">Actual Date To</label>
                                <input type="text" name="actual_date_to" id="actual_date_to" class="form-control datetimepicker" disabled>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    $(document).ready(function() {

        // Initialize Select2 for better dropdown experience if available
        if ($.fn.select2) {
            $('#filter_employee, #filter_leave_type, #filter_status').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }

        $(document).on('click', '.add_leave-btn', function() {
            $('#add_leave-modal .modal-title').text('Add Leave');
            $('#add_leave-form')[0].reset();
            $('#add_leave-modal').modal('show');
            $('.actual_date_con').hide(); // hide actual date fields by default
            $('#sil_used').val('0.00').prop('readonly', true); // reset SIL used field

            // Load SIL balance info for current user
            loadSILBalance();

            $(document).off('submit', '#add_leave-form').on('submit', '#add_leave-form', function(e) {
                e.preventDefault();

                var formdata = $(this).serializeArray();
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('leaves/addleave') ?>',
                    data: formdata,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {

                            // success alert and reload
                            $.alert({
                                title: 'Success!',
                                content: response.message,
                                type: 'green',
                                buttons: {
                                    OK: {
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
                                title: 'Error!',
                                content: response.message,
                                type: 'red',
                                buttons: {
                                    Ok: {
                                        text: 'Ok',
                                        btnClass: 'btn-red',
                                        action: function() {
                                            $('#btn-save').buttonLoader('stop');
                                        }
                                    }
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $.alert({
                            title: 'Error!',
                            content: 'An error occurred while processing your request. Please try again later.',
                            type: 'red',
                            buttons: {
                                Ok: {
                                    text: 'Ok',
                                    btnClass: 'btn-red',
                                    action: function() {
                                        $('#btn-save').buttonLoader('stop');
                                    }
                                }
                            }
                        });
                    }
                });
            });
        });

        $(document).on('click', '.update_leave-btn', function() {
            var leave_id = $(this).closest('tr').data('lid');

            // change modal title
            $('#add_leave-modal .modal-title').text('Update Leave');

            $.ajax({
                type: 'POST',
                url: '<?= base_url('leaves/getleave') ?>',
                data: {
                    leaveid: leave_id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        $('#add_leave-form')[0].reset();
                        $('#add_leave-modal').modal('show');

                        // populate form fields with response data
                        $('#leave_type').val(response.data.type);
                        $('#leave_from').val(response.data.leave_from);
                        $('#leave_to').val(response.data.leave_to);
                        $('#leave_reason').val(response.data.reason);
                        $('#sil_used').val(response.data.sil).prop('readonly', false);

                        // show actual date fields if available
                        if (response.data.actual_date_from && response.data.actual_date_to) {
                            $('.actual_date_con').show();
                            $('#actual_date_from').val(response.data.actual_date_from).prop('disabled', false);
                            $('#actual_date_to').val(response.data.actual_date_to).prop('disabled', false);
                        } else {
                            $('.actual_date_con').show();
                            $('#actual_date_from').val('').prop('disabled', false);
                            $('#actual_date_to').val('').prop('disabled', false);
                        }

                        $(document).off('submit', '#add_leave-form').on('submit', '#add_leave-form', function(e) {
                            e.preventDefault();

                            var formdata = $(this).serializeArray();
                            formdata.push({
                                name: 'leaveid',
                                value: leave_id
                            });


                            // process form submission ajax
                            $.ajax({
                                type: 'POST',
                                url: '<?= base_url('leaves/updateleave') ?>',
                                data: formdata,
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status == 'success') {

                                        // success alert and reload
                                        $.alert({
                                            title: 'Success!',
                                            content: response.message,
                                            type: 'green',
                                            buttons: {
                                                OK: {
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
                                            title: 'Error!',
                                            content: response.message,
                                            type: 'red',
                                            buttons: {
                                                Ok: {
                                                    text: 'Ok',
                                                    btnClass: 'btn-red',
                                                    action: function() {
                                                        $('#btn-save').buttonLoader('stop');
                                                    }
                                                }
                                            }
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    $.alert({
                                        title: 'Error!',
                                        content: 'An error occurred while processing your request. Please try again later.',
                                        type: 'red',
                                        buttons: {
                                            Ok: {
                                                text: 'Ok',
                                                btnClass: 'btn-red',
                                                action: function() {
                                                    $('#btn-save').buttonLoader('stop');
                                                }
                                            }
                                        }
                                    });
                                }
                            });


                        });

                    } else {
                        $.alert({
                            title: 'Error!',
                            content: response.message,
                            type: 'red'
                        });
                    }
                }
            });
        });


        // compute SIL used based on leave_from and leave_to
        $(document).on('change', '#leave_from, #leave_to, #actual_date_from, #actual_date_to', function() {
            var fromStr = $('#leave_from').val();
            var toStr = $('#leave_to').val();

            var act_fromStr = $('#actual_date_from').val();
            var act_toStr = $('#actual_date_to').val();

            // override fromStr and toStr if actual date fields are filled
            if (act_fromStr && act_toStr) {
                fromStr = act_fromStr;
                toStr = act_toStr;
            }

            const hours = (fromStr && toStr) ? calcHours(fromStr, toStr) : 0;
            $('#sil_used').val(hours);
        });

    });

    function loadSILBalance(employeeId = null) {
        $.ajax({
            type: 'POST',
            url: '<?= base_url('leaves/getSILBalance') ?>',
            data: { employee_id: employeeId },
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    const data = response.data;
                    $('#sil-balance-info').html(
                        `Current SIL Balance: <strong>${data.current_sil}</strong> hours ` +
                        `(Earned: ${data.earned_sil}, Used: ${data.used_sil})`
                    ).removeClass('text-muted').addClass('text-info');
                } else {
                    $('#sil-balance-info').html('Unable to load SIL balance').removeClass('text-info').addClass('text-muted');
                }
            },
            error: function() {
                $('#sil-balance-info').html('Error loading SIL balance').removeClass('text-info').addClass('text-muted');
            }
        });
    }
</script>

<script>
    $(document).on('click', '.leave_reason_show-btn', function() {
        var leave_id = $(this).closest('tr').data('lid');

        $.ajax({
            type: 'POST',
            url: '<?= base_url('leaves/getleave') ?>',
            data: {
                leaveid: leave_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    $.alert({
                        title: 'Leave Reason',
                        content: response.data.reason,
                        type: 'blue',
                        backgroundDismiss: true,
                    });
                } else {
                    $.alert({
                        title: 'Error!',
                        content: response.message,
                        type: 'red'
                    });
                }
            }
        });
    });
</script>

<?php if (check_function('manage_leave')) : ?>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.leave_approval-btn', function(e) {
                e.preventDefault();

                var leave_id = $(this).closest('tr').data('lid');
                var leave_type = $(this).data('approval');

                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('leaves/getleave') ?>',
                    data: {
                        leaveid: leave_id,
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $.confirm({
                                title: 'Leave Approval',
                                content: '<label><strong>Leave Reason</strong></label>' +
                                    '<textarea readonly class="form-control" rows="5" style="resize: none;">' + response.data.reason + '</textarea>' +
                                    '<br><label><strong>Leave Remarks</strong></label>' +
                                    '<textarea class="approval-reason form-control" rows="3" placeholder="Enter your reason here..."></textarea>',
                                type: 'blue',
                                buttons: {
                                    approve: {
                                        text: 'Approve',
                                        btnClass: 'btn-green',
                                        action: function() {
                                            var reason = this.$content.find('.approval-reason').val();
                                            $.ajax({
                                                type: 'POST',
                                                url: '<?= base_url('leaves/leaveapproval') ?>',
                                                data: {
                                                    leaveid: leave_id,
                                                    type: leave_type,
                                                    approval: 'approved',
                                                    remarks: reason
                                                },
                                                dataType: 'json',
                                                success: function(res) {
                                                    $.alert({
                                                        title: res.status === 'success' ? 'Success!' : 'Error!',
                                                        content: res.message,
                                                        type: res.status === 'success' ? 'green' : 'red',
                                                        backgroundDismiss: true
                                                    });
                                                    if (res.status === 'success') location.reload();
                                                }
                                            });
                                        }
                                    },
                                    deny: {
                                        text: 'Deny',
                                        btnClass: 'btn-red',
                                        action: function() {
                                            var reason = this.$content.find('.approval-reason').val();
                                            if (!reason) {
                                                $.alert('Please provide a remarks.');
                                                return false;
                                            }

                                            $.ajax({
                                                type: 'POST',
                                                url: '<?= base_url('leaves/leaveapproval') ?>',
                                                data: {
                                                    leaveid: leave_id,
                                                    type: leave_type,
                                                    approval: 'denied',
                                                    remarks: reason
                                                },
                                                dataType: 'json',
                                                success: function(res) {
                                                    $.alert({
                                                        title: res.status === 'success' ? 'Success!' : 'Error!',
                                                        content: res.message,
                                                        type: res.status === 'success' ? 'green' : 'red',
                                                        backgroundDismiss: true,
                                                    });
                                                    if (res.status === 'success') location.reload();
                                                }
                                            });
                                        }
                                    },
                                    cancel: function() {}
                                }
                            });
                        } else {
                            $.alert({
                                title: 'Error!',
                                content: response.message,
                                type: 'red'
                            });
                        }
                    }
                });

            })

            $(document).on('click', '.leave_hr_confirm-btn', function(e) {
                e.preventDefault();
                var leave_id = $(this).closest('tr').data('lid');
                var hr_status = $(this).text().trim().toLowerCase();

                $.confirm({
                    title: 'HR Confirmation',
                    content: 'Are you sure you want to <strong>' + (hr_status == 'pending' ? 'confirm this leave?' : 'cancel this leave confirmation?') + '</strong>',
                    type: hr_status == 'pending' ? 'green' : 'red',
                    buttons: {
                        confirm: {
                            text: 'Confirm',
                            btnClass: 'btn-blue',
                            action: function() {
                                $.ajax({
                                    type: 'POST',
                                    url: '<?= base_url('leaves/hrconfirm') ?>',
                                    data: {
                                        leaveid: leave_id
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.status === 'success') {
                                            $.alert({
                                                title: 'Success!',
                                                content: response.message,
                                                type: 'green',
                                                backgroundDismiss: true,
                                            });


                                            // reload table without refreshing the page
                                            reloadLeaveTable();


                                        } else {
                                            $.alert({
                                                title: 'Error!',
                                                content: response.message,
                                                type: 'red',
                                                backgroundDismiss: true,
                                            });
                                        }
                                    }
                                });
                            }
                        },
                        cancel: function() {}
                    }
                });
            });
        })
    </script>
<?php endif; ?>

<script>
    $(document).ready(function() {
        $(document).on('click', '.cancel_leave-btn', function() {
            var leave_id = $(this).closest('tr').data('lid');

            $.confirm({
                title: 'Cancel Leave',
                content: 'Are you sure you want to cancel this leave?',
                type: 'red',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-red',
                        action: function() {
                            $.ajax({
                                type: 'POST',
                                url: '<?= base_url('leaves/cancelleave') ?>',
                                data: {
                                    leaveid: leave_id
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.status === 'success') {
                                        $.alert({
                                            title: 'Success!',
                                            content: response.message,
                                            type: 'green',
                                            backgroundDismiss: true,
                                        });

                                        reloadLeaveTable();
                                    } else {
                                        $.alert({
                                            title: 'Error!',
                                            content: response.message,
                                            type: 'red',
                                            backgroundDismiss: true,
                                        });
                                    }
                                }
                            });
                        }
                    },
                    cancel: function() {}
                }
            });
        });
    })
</script>

<script>
    function reloadLeaveTable() {
        const $table = $('#leaveTable');

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().destroy();
        }

        $("#leaveTable tbody").load(location.href + " #leaveTable tbody>*", function() {
            $table.DataTable({
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true
            });
        });
    }

    $(document).ready(function() {

        $('.datepicker').datepicker({
            autoclose: true,
            format: 'M dd, yyyy',
            todayHighlight: true
        });

        <?php if (!empty($list)) : ?>
            reloadLeaveTable();
        <?php endif; ?>
    });
</script>