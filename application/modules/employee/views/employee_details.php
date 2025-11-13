<div class="modal fade" id="employee_details-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Employee: <span class="modal-empid">EPM-001</span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="employee_details-form" class="m-0">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-12 bg--gray-100 p-10 row">
                            <div class="col-4">
                                <div class="text-center">
                                    <img class="profile-user-img img-fluid img-circle m-b-0" src="<?= base_url('dist/img/user-account-profile.png') ?>" alt="User profile picture" id="profile_image_preview">
                                </div>
                            </div>

                            <div class="col-8">
                                <div class="form-group ">
                                    <label for="profile_image">Upload Profile Image</label>
                                    <div class="profile-image-upload ">
                                        <div class="profile-image-upload__image btn btn-primary btn-sm d-inline-block">
                                            Upload Image
                                            <input type="file" id="profile_image" name="profile_image" accept="image/*">
                                        </div>
                                        <a href="javascript:;" class="btn btn-sm btn-danger profile-image-upload_cancel">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="first_name">First Name <strong class="text-red">*</strong></label>
                            <input id="first_name" name="first_name" type="text" class="form-control" placeholder="First Name" required>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="last_name">Last Name <strong class="text-red">*</strong></label>
                            <input id="last_name" name="last_name" type="text" class="form-control" placeholder="Last Name" required>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="middle_name">Middle Name</label>
                            <input id="middle_name" name="middle_name" type="text" class="form-control" placeholder="Middle Name">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="suffix">Suffix</label>
                            <input id="suffix" name="suffix" type="text" class="form-control" placeholder="Suffix">
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="employee_id">Employee ID <strong class="text-red">*</strong></label>
                            <input id="employee_id" name="employee_id" type="text" class="form-control" placeholder="Employee ID" required>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="badge_number">Badge Number</label>
                            <input id="badge_number" name="badge_number" type="text" class="form-control" placeholder="Badge Number">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="locker_number">Locker Number</label>
                            <input id="locker_number" name="locker_number" type="text" class="form-control" placeholder="Locker Number">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="password">Password </label>
                            <input id="password" name="password" type="password" class="form-control" placeholder="Password">
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="date_hired_display">Date Hired</label>
                            <input id="date_hired_display" type="text" class="form-control" placeholder="Date Hired" readonly>
                            <input name="date_hired" id="date_hired" type="hidden">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="account">Account</label>
                            <input id="account" name="account" type="text" class="form-control" placeholder="Account">
                        </div>

                        <div class="form-group col-12 col-lg-6">
                            <label for="emp_level">Level</label>
                            <?php $items = users__lang('level'); ?>
                            <select name="emp_level" id="emp_level" class="form-control">
                                <option value="">Select Level</option>
                                <?php foreach ($items ?? [] as $item): ?>
                                    <option value="<?= htmlspecialchars($item['keyid']) ?>">
                                        <?= htmlspecialchars($item['value']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="emp_designation">Designation</label>
                            <?php $items = users__lang('designation'); ?>
                            <select id="emp_designation" name="emp_designation" class="form-control">
                                <option value="">Select Designation</option>
                                <?php foreach ($items ?? [] as $item): ?>
                                    <option value="<?= htmlspecialchars($item['keyid']) ?>">
                                        <?= htmlspecialchars($item['value']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="emp_supervisor">Supervisor</label>
                            <select id="emp_supervisor" name="emp_supervisor" class="form-control select2-supervisor">
                                <option value="">Select Supervisor</option>
                            </select>
                        </div>
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

<script>
    $(document).ready(function() {
        // Initialize Select2 for supervisor with AJAX
        $('.select2-supervisor').select2({
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

        // replace the profile image with the uploaded image
        $('#profile_image').on('change', function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#profile_image_preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        $('.profile-image-upload_cancel').on('click', function() {
            $('#profile_image').val('');
            $('#profile_image_preview').attr('src', '<?= base_url('dist/img/user-account-profile.png') ?>');
        });

        $('#date_hired_display').datepicker({
            format: 'M d, yyyy', // Display as: Jun 9, 2025
            autoclose: true,
            todayHighlight: true
        }).on('changeDate', function(e) {
            // Convert to Y-m-d format and store in hidden input
            const formatted = e.format('yyyy-mm-dd');
            $('#date_hired').val(formatted);
        });

        $(document).on('click', '#edit_info_btn', function() {
            const empId = $(this).data('empid');
            $('#employee_details-modal .modal-empid').text('');

            // Reset the form
            $('#employee_details-form')[0].reset();
            $('#profile_image_preview').attr('src', '<?= base_url('dist/img/user-account-profile.png') ?>');

            // Clear Select2
            $('.select2-supervisor').val(null).trigger('change');

            // Fetch employee details
            $.ajax({
                url: '<?= site_url('employee/get_empdetails') ?>',
                type: 'POST',
                data: {
                    userid: empId
                },
                dataType: 'json',
                beforeSend: function() {
                    page_loader_show();
                    $('#employee_details-form button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const data = response.data;
                        $('.modal-empid').text(data.employee_id);

                        $('#first_name').val(data.first_name);
                        $('#last_name').val(data.last_name);
                        $('#middle_name').val(data.middle_name);
                        $('#suffix').val(data.suffix);
                        $('#employee_id').val(data.employee_id);
                        $('#badge_number').val(data.badge_number);
                        $('#locker_number').val(data.locker_number);
                        $('#account').val(data.account);
                        $('#emp_level').val(data.emp_level);
                        $('#emp_designation').val(data.emp_designation);
                        $('#date_hired').val(data.date_hired); // Store in hidden input

                        // Set supervisor if exists
                        if (data.emp_supervisor) {
                            // Create option and append to select
                            var supervisorOption = new Option(data.supervisor_name, data.emp_supervisor, true, true);
                            $('.select2-supervisor').append(supervisorOption).trigger('change');
                        }

                        // set date hired display
                        $('#date_hired_display').val(moment(data.date_hired).format('MMM D, YYYY')); // Display as: Jun 9, 2025
                        $('#date_hired_display').datepicker('update', moment(data.date_hired).toDate()); // Update datepicker


                        // if profile not empty, set the profile image
                        if (data.profile) {
                            let profileImage = JSON.parse(data.profile);
                            profileImage = profileImage.file_path ? '<?= base_url() ?>' + profileImage.file_path : '<?= base_url('dist/img/user-account-profile.png') ?>';
                            $('#profile_image_preview').attr('src', profileImage);
                        } else {
                            $('#profile_image_preview').attr('src', '<?= base_url('dist/img/user-account-profile.png') ?>');
                        }
                    } else {
                        $.alert({
                            title: 'Error',
                            content: response.message || 'Failed to fetch employee details.',
                            type: 'red',
                            backgroundDismiss: true
                        });
                    }
                },
                error: function() {
                    alert('Failed to fetch employee details.');

                    page_loader_hide();
                    $('#employee_details-form button[type="submit"]').prop('disabled', false);
                },
                complete: function() {
                    page_loader_hide();
                    $('#employee_details-form button[type="submit"]').prop('disabled', false);
                }
            });

            // Show the modal
            $('#employee_details-modal').modal('show');

            // process form submission
            $('#employee_details-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('empid', empId); // Add employee ID to the form data

                $.ajax({
                    url: '<?= site_url('employee/update_empdetails') ?>',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        page_loader_show();
                        $('#employee_details-form button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            $('#employee_details-modal').modal('hide');

                            $.alert({
                                title: 'Notice',
                                content: result.message || 'Employee details updated successfully.',
                                type: 'green',
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
                                content: result.message || 'Failed to update employee details.',
                                type: 'red',
                                backgroundDismiss: true
                            });
                        }
                    },
                    error: function() {
                        console.error('Error updating employee details.');
                        page_loader_hide();
                        $('#employee_details-form button[type="submit"]').prop('disabled', false);
                        $('#employee_details-modal').modal('hide');
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#employee_details-form button[type="submit"]').prop('disabled', false);
                        $('#employee_details-modal').modal('hide');
                    }
                });
            });

        });
    })
</script>