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
                    <li class="breadcrumb-item active">Employee</li>
                </ol>
            </div>


            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="javascript:;" class="btn btn-pill btn-warning btn-md text-white" id="add_employee-btn"> <i class="fa fa-user-plus"></i> Add Employee</a>
                </div>
            </div>
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
                            <div class="col-md-2">
                                <input type="text" id="filter_emp_search" name="filter_emp_search" placeholder="Employee (ID or Name)" class="form-control">
                            </div>

                            <div class="col-md-2">
                                <input type="text" id="filter_supervisor_search" name="filter_supervisor_search" placeholder="Supervisor Name" class="form-control">
                            </div>

                            <div class="col-md-2">
                                <select name="employee_category" id="employee_category" class="form-control">
                                    <option value="">All Roles</option>

                                    <?php
                                    $userlevels = users__lang('level');
                                    if (!empty($userlevels)) :
                                        foreach ($userlevels as $level) :
                                    ?>
                                            <option value="<?= $level['value'] ?>"><?= $level['value'] ?></option>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="employee_status" id="employee_status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <input type="date" id="filter_hired_from" name="filter_hired_from" placeholder="Hired From" class="form-control" title="Hired From Date">
                            </div>

                            <div class="col-md-1">
                                <input type="date" id="filter_hired_to" name="filter_hired_to" placeholder="Hired To" class="form-control" title="Hired To Date">
                            </div>

                            <div class="col-md-1">
                                <div class="d-flex justify-content-end">
                                    <button type="button" id="clear_filters_btn" class="btn btn-secondary" title="Clear Filters"><i class="fa fa-sync"></i></button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <div class="card-body">

                        <table class="table" id="employeeTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Emp. ID</th>
                                    <th>Supervisor</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Hired Date</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <!-- <th style="width: 40px">Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($list)) : ?>
                                    <?php foreach ($list as $employee) : ?>
                                        <tr data-lid="<?= $this->mysecurity->encrypt_url($employee['id']); ?>">
                                            <td><a href="<?= base_url('employee/profile/' . $this->mysecurity->encrypt_url($employee['id'])) ?>"><?= "{$employee['emp_lname']}, {$employee['emp_fname']}" ?></a></td>
                                            <td><?= $employee['emp_id'] ?></td>
                                            <td><?= (!empty($employee['sv_fname']) ? $employee['sv_fname'] : '') . ' ' . (!empty($employee['sv_lname']) ? $employee['sv_lname'] : '') ?></td>
                                            <td><?= $employee['email'] ?></td>
                                            <td><?= formatPhone($employee['phone']) ?></td>
                                            <td data-order="<?= strtotime($employee['hiring_date']) ?>"><?= date('M d, Y', strtotime($employee['hiring_date'])) ?></td>
                                            <td><?= users__lang('level', $employee['emp_level']) ?></td>
                                            <td>
                                                <?php
                                                if ($employee['status'] == '0') {
                                                    echo '<span class="badge badge-success">Active</span>';
                                                } elseif ($employee['status'] == '1') {
                                                    echo '<span class="badge badge-danger">Inactive</span>';
                                                }
                                                ?>
                                            </td>
                                            <!-- <td class="text-nowrap">
                                                <a href="javascript:;" class="btn btn-xs btn-danger"> <i class="fa fa-trash"></i></a>
                                            </td> -->
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="font-italic text-center">No records found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>


    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Employee</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="addemployee-form" class="m-0">
                <div class="modal-body">

                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="add_first_name">First Name <strong class="text-red">*</strong></label>
                            <input id="add_first_name" name="First_Name" type="text" class="form-control" placeholder="First Name" required>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_last_name">Last Name <strong class="text-red">*</strong></label>
                            <input id="add_last_name" name="Last_Name" type="text" class="form-control" placeholder="Last Name" required>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_middle_name">Middle Name</label>
                            <input id="add_middle_name" name="Middle_Name" type="text" class="form-control" placeholder="Middle Name">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_suffix">Suffix</label>
                            <input id="add_suffix" name="suffix" type="text" class="form-control" placeholder="Suffix">
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="add_employee_id">Employee ID <strong class="text-red">*</strong></label>
                            <input id="add_employee_id" name="Employee_ID" type="text" class="form-control" placeholder="Employee ID" required>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_password">Password <strong class="text-red">*</strong></label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <a href="javascript:;" class="generate_add_password"><i class="fa fa-key"></i></a>
                                    </span>
                                </div>

                                <input id="add_password" name="password" type="password" class="form-control" placeholder="Password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <a href="javascript:;" class="toggle_add_password_visibility"><i class="fa fa-eye"></i></a>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_email">Email <strong class="text-red">*</strong></label>
                            <input id="add_email" name="Email" type="email" class="form-control" placeholder="Email" required>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_badge_number">Badge Number</label>
                            <input id="add_badge_number" name="badge_number" type="text" class="form-control" placeholder="Badge Number">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_locker_number">Locker Number</label>
                            <input id="add_locker_number" name="locker_number" type="text" class="form-control" placeholder="Locker Number">
                        </div>
                    </div>

                    <hr>

                    <div class="row">

                        <div class="form-group col-12 col-md-6">
                            <label for="add_gender">Gender <strong class="text-red">*</strong></label>
                            <select name="Gender" id="add_gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_date_of_birth">Date of Birth <strong class="text-red">*</strong></label>
                            <input name="Date_of_Birth" placeholder="Date of Birth" id="add_date_of_birth" class="form-control datepicker-add" type="text" required readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="add_date_hired">Date Hired <strong class="text-red">*</strong></label>
                            <input id="add_date_hired" name="Hired_Date" type="text" class="form-control datepicker-add" placeholder="Date Hired" readonly required>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_account">Account</label>
                            <select id="add_account" name="account" class="form-control select2-search-account">
                                <option value=""></option>
                            </select>
                        </div>

                        <div class="form-group col-12 col-lg-6">
                            <label for="add_emp_level">Level <strong class="text-red">*</strong></label>
                            <?php $items = users__lang('level'); ?>
                            <select name="Emp_Role" id="add_emp_level" class="form-control" required>
                                <option value="">Select Level</option>
                                <?php foreach ($items ?? [] as $item): ?>
                                    <option value="<?= htmlspecialchars($item['keyid']) ?>">
                                        <?= htmlspecialchars($item['value']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_emp_designation">Designation <strong class="text-red">*</strong></label>
                            <?php $items = users__lang('designation'); ?>
                            <select id="add_emp_designation" name="Emp_Designation" class="form-control" required>
                                <option value="">Select Designation</option>
                                <?php foreach ($items ?? [] as $item): ?>
                                    <option value="<?= htmlspecialchars($item['keyid']) ?>">
                                        <?= htmlspecialchars($item['value']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_emp_supervisor">Supervisor</label>
                            <select id="add_emp_supervisor" name="emp_supervisor" class="form-control select2-add-supervisor">
                                <option value="">Select Supervisor</option>
                            </select>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_salary">Monthly Salary <strong class="text-red">*</strong></label>
                            <input name="Monthly_Salary" placeholder="Salary" type="text" id="add_salary" class="form-control number_only" required>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="add_semi_monthly_rate">Semi-Monthly Rate</label>
                            <input placeholder="Semi-Monthly Rate" id="add_semi_monthly_rate" class="form-control number_only" type="text" readonly>
                        </div>

                        <!-- Select multiple max 2 days Rest Day, Default: Sunday and Saturday -->
                        <div class="form-group col-12 col-md-6">
                            <label for="add_rest_days">Rest Days</label>
                            <select id="add_rest_days" name="Rest_Days[]" class="form-control" multiple>
                                <option value="Sunday" selected>Sunday</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday" selected>Saturday</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success has-spinner" id="btn-save-employee"><i class="fa fa-plus m-r-5"></i> Add Employee</button>
                    </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#employee_category, #employee_status").select2({
            theme: 'bootstrap4',
        });

        // Initialize Select2 for supervisor with AJAX in Add Employee Modal
        $('.select2-add-supervisor').select2({
            dropdownParent: $('#addEmployeeModal'),
            placeholder: 'Select Supervisor',
            allowClear: true,
            ajax: {
                url: '<?= site_url('employee/get_supervisors') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.results,
                        pagination: {
                            more: (params.page * 10) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: function(supervisor) {
                if (supervisor.loading) return supervisor.text;
                return $('<span>' + supervisor.text + '</span>');
            },
            templateSelection: function(supervisor) {
                return supervisor.text;
            }
        });

        // Initialize Select2 for account with AJAX in Add Employee Modal
        $('.select2-search-account').select2({
            dropdownParent: $('#addEmployeeModal'),
            placeholder: 'Search Account',
            allowClear: true,
            tags: true,
            ajax: {
                url: '<?= site_url('employee/get_accounts') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.results,
                        pagination: {
                            more: (params.page * 10) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: function(account) {
                if (account.loading) return account.text;
                return $('<span>' + account.text + '</span>');
            },
            templateSelection: function(account) {
                return account.text;
            }
        });

        $('#add_rest_days').select2({
            placeholder: 'Select Rest Days',
            maximumSelectionLength: 2,
        });

        // Open Add Employee Modal
        $('#add_employee-btn').on('click', function() {

            $.ajax({
                url: '<?= site_url('employee/getLatestEmpId') ?>',
                type: 'GET',
                dataType: 'text',
                beforeSend: function() {
                    $('#btn-save-employee').buttonLoader('start');
                }
            }).done(function(response) {
                $('#add_employee_id').val(response);
            }).fail(function() {
                console.error('Failed to fetch latest Employee ID.');
            }).always(function() {
                $('#addEmployeeModal').modal('show');
                $('#btn-save-employee').buttonLoader('stop');
            });
        });

        // Initialize datepicker for modal
        $('#addEmployeeModal').on('shown.bs.modal', function() {
            $('.datepicker-add').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'M d, yyyy',
            });
        });

        // Handle profile image upload preview
        $('#add_profile_image').on('change', function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#add_profile_image_preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        // Cancel profile image upload
        $('.add-profile-image-upload_cancel').on('click', function() {
            $('#add_profile_image').val('');
            $('#add_profile_image_preview').attr('src', '<?= base_url('dist/img/user-account-profile.png') ?>');
        });

        // Compute semi monthly rate based on monthly salary
        $(document).on('keyup', 'input[name="Monthly_Salary"]', function() {
            var monthly_salary = $(this).val();

            monthly_salary = monthly_salary.replace(/,/g, '');
            var semi_monthly_rate = monthly_salary / 2;
            semi_monthly_rate = semi_monthly_rate.toFixed(2).replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1').replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');

            $('input#add_semi_monthly_rate').val(semi_monthly_rate);
        });

        // Handle Add Employee Form Submission
        $(document).on('submit', '#addemployee-form', function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = new FormData(this);

            $.ajax({
                url: '<?= base_url('employee/processaddemployee') ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                beforeSend: function() {
                    $('#btn-save-employee').buttonLoader('start');
                }
            }).done(function(response) {
                if (response.status == 'success') {
                    $.alert({
                        title: 'Success!',
                        content: 'Employee added successfully!',
                        type: 'green',
                        buttons: {
                            Ok: {
                                text: 'Ok',
                                btnClass: 'btn-green',
                                action: function() {
                                    // Reload the page to refresh the employee list
                                    window.location.reload();
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
                                    $('#btn-save-employee').buttonLoader('stop');
                                }
                            }
                        }
                    });
                }
            }).fail(function() {
                $.alert({
                    title: 'Error!',
                    content: 'An error occurred while processing your request. Please try again later.',
                    type: 'red',
                    buttons: {
                        Ok: {
                            text: 'Ok',
                            btnClass: 'btn-red',
                            action: function() {
                                $('#btn-save-employee').buttonLoader('stop');
                            }
                        }
                    }
                });
            }).always(function() {
                $('#btn-save-employee').buttonLoader('stop');
            });
        });

        // Reset form when modal is closed
        $('#addEmployeeModal').on('hidden.bs.modal', function() {
            $('#addemployee-form')[0].reset();
            $('#btn-save-employee').buttonLoader('stop');
            $('#add_profile_image_preview').attr('src', '<?= base_url('dist/img/user-account-profile.png') ?>');
            $('.select2-add-supervisor').val(null).trigger('change');
            $('.select2-search-account').val(null).trigger('change');
        });

        <?php if (!empty($list)) : ?>
            var table = $('#employeeTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "dom": 'rtip', // Remove the default search box

                // inactive employee should be last
                "order": [
                    [6, 'asc'],
                    [0, 'asc']
                ]
            });

            // Debounce function
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = function() {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Custom filter function
            function applyFilters() {
                var empSearch = $('#filter_emp_search').val().toLowerCase();
                var supervisorSearch = $('#filter_supervisor_search').val().toLowerCase();
                var empRole = $('#employee_category').val();
                var empStatus = $('#employee_status').val();
                var hiredFrom = $('#filter_hired_from').val();
                var hiredTo = $('#filter_hired_to').val();

                // Check if any filters are active
                var hasActiveFilters = empSearch || supervisorSearch || empRole || empStatus || hiredFrom || hiredTo;

                // Disable/enable paging based on filter status
                if (hasActiveFilters) {
                    table.page.len(-1).draw(); // Show all rows (disable paging)
                } else {
                    table.page.len(10).draw(); // Reset to default page length
                }

                table.rows().every(function() {
                    var data = this.data();
                    var rowEmpId = $(data[1]).text().toLowerCase() || data[1].toLowerCase();
                    var rowEmpName = $(data[0]).text().toLowerCase() || data[0].toLowerCase();
                    var rowSupervisor = $(data[2]).text().toLowerCase() || data[2].toLowerCase();
                    var rowEmpRole = $(data[6]).text() || data[6];
                    var rowEmpStatus = $(data[7]).text() || data[7]; // Status column
                    // Use data-order attribute for hired date (timestamp)
                    var rowHiredDateCell = $(this.node()).find('td[data-order]');
                    var hiredTimestamp = rowHiredDateCell.length ? parseInt(rowHiredDateCell.attr('data-order')) : null;

                    var showRow = true;

                    // Filter by Employee ID or Name (single search input)
                    if (empSearch && rowEmpId.indexOf(empSearch) === -1 && rowEmpName.indexOf(empSearch) === -1) {
                        showRow = false;
                    }

                    // Filter by Supervisor Name
                    if (supervisorSearch && rowSupervisor.indexOf(supervisorSearch) === -1) {
                        showRow = false;
                    }

                    // Filter by Role
                    if (empRole && rowEmpRole !== empRole) {
                        showRow = false;
                    }

                    // Filter by Status
                    if (empStatus && rowEmpStatus !== empStatus) {
                        showRow = false;
                    }

                    // Filter by Hired Date Range
                    if ((hiredFrom || hiredTo) && hiredTimestamp) {
                        if (hiredFrom) {
                            var fromDate = new Date(hiredFrom);
                            var fromTimestamp = fromDate.setHours(0, 0, 0, 0) / 1000;
                            if (hiredTimestamp < fromTimestamp) {
                                showRow = false;
                            }
                        }
                        if (hiredTo) {
                            var toDate = new Date(hiredTo);
                            var toTimestamp = toDate.setHours(23, 59, 59, 999) / 1000;
                            if (hiredTimestamp > toTimestamp) {
                                showRow = false;
                            }
                        }
                    }

                    // Show or hide the row
                    if (showRow) {
                        $(this.node()).show();
                    } else {
                        $(this.node()).hide();
                    }
                });

                table.draw(false); // Redraw without changing page
            }

            // Create debounced version of applyFilters
            const debouncedApplyFilters = debounce(applyFilters, 300);

            // Clear filters button click event
            $('#clear_filters_btn').on('click', function() {
                $('#filter_emp_search').val('');
                $('#filter_supervisor_search').val('');
                $('#employee_category').val('').trigger('change');
                $('#employee_status').val('').trigger('change');
                $('#filter_hired_from').val('');
                $('#filter_hired_to').val('');

                // Re-enable paging when clearing filters
                table.page.len(10).draw();

                // Show all rows
                table.rows().every(function() {
                    $(this.node()).show();
                });
                table.draw(false);
            });

            // Apply filters on keyup with debounce
            $('#filter_emp_search, #filter_supervisor_search').on('keyup input', function() {
                debouncedApplyFilters();
            });

            // Apply filters when date inputs change
            $('#filter_hired_from, #filter_hired_to').on('change', function() {
                applyFilters();
            });

            // Apply filters when role or status selection changes (no debounce needed for dropdown)
            $('#employee_category, #employee_status').on('change', function() {
                applyFilters();
            });

            $(document).on('click', '.listuser_update--btn', function() {
                var tr = $(this).closest('tr');
                var lid = tr.data('lid');
                window.location.href = '<?= base_url('employee/updateemployee/') ?>' + lid;
            });

            $(document).on('click', '.listuser_schedule--btn', function() {
                var tr = $(this).closest('tr');
                var lid = tr.data('lid');
                window.location.href = '<?= base_url('employee/schedule/') ?>' + lid;
            });
        <?php endif; ?>
    });
</script>