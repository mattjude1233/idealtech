<div class="modal fade" id="edit-basic_info-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Basic Information: <span class="modal-empid"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="edit-basic_info-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" type="text" class="form-control" placeholder="Phone">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="text" class="form-control" placeholder="Email">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" class="form-control">
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="birthday">Birthday</label>
                            <input type="text" class="form-control" id="birthday-datepicker" placeholder="Select Birthday" readonly>
                            <input type="hidden" id="birthday-hidden" name="birthday">
                        </div>

                        <div class="form-group col-12">
                            <label for="present_address">Present Address</label>
                            <input name="present_address" id="present_address" placeholder="Select Present Address" type="text" class="form-control">
                        </div>

                        <div class="form-group col-12">
                            <label for="permanent_address">Permanent Address</label>
                            <input name="permanent_address" id="permanent_address" placeholder="Select Permanent Address" type="text" class="form-control">
                        </div>
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

<script>
    $(document).ready(function() {
        $('#birthday-datepicker').datepicker({
            format: 'M d, yyyy', // Display as: Jun 9, 2025
            autoclose: true,
            todayHighlight: true
        }).on('changeDate', function(e) {
            // Convert to Y-m-d format and store in hidden input
            const formatted = e.format('yyyy-mm-dd');
            $('#birthday-hidden').val(formatted);
        });

        $(document).on('click', '#edit-basic_info-btn', function() {
            const empId = $(this).data('empid');
            $('#edit-basic_info-modal .modal-empid').text('');

            // Reset the form
            $('#edit-basic_info-form')[0].reset();

            // Fetch employee details
            $.ajax({
                url: '<?= base_url('employee/get_empbasicinfo') ?>',
                type: 'POST',
                data: {
                    userid: empId
                },
                dataType: 'json',
                beforeSend: function() {
                    page_loader_show();
                    $('#edit-basic_info-form button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    page_loader_hide();
                    if (response.status === 'success') {
                        const data = response.data;
                        $('#phone').val(data.phone);
                        $('#email').val(data.email);
                        $('#gender').val(data.gender);
                        $('#birthday-hidden').val(data.birthday);
                        $('#present_address').val(data.present_address);
                        $('#permanent_address').val(data.permanent_address);

                        $('#birthday-datepicker').val(moment(data.birthday).format('MMM D, YYYY'));
                        $('#birthday-datepicker').datepicker('update', moment(data.birthday).toDate());

                        $('#edit-basic_info-modal .modal-empid').text(data.emp_id);
                    } else {
                        $.alert({
                            title: 'Error',
                            content: response.message || 'Failed to fetch employee details.',
                            type: 'red',
                            backgroundDismiss: true
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching employee details:', error);
                    page_loader_hide();
                    $.alert({
                        title: 'Error',
                        content: 'An error occurred while fetching employee details.',
                        type: 'red',
                        backgroundDismiss: true
                    });
                    $('#edit-basic_info-form button[type="submit"]').prop('disabled', false);
                },
                complete: function() {
                    page_loader_hide();
                    $('#edit-basic_info-modal').modal('show');
                    $('#edit-basic_info-form button[type="submit"]').prop('disabled', false);
                }
            });

            // process form submission
            $('#edit-basic_info-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();

                // Add emp_id to the form data
                formData.push({
                    name: 'emp_id',
                    value: empId
                });

                $.ajax({
                    url: '<?= base_url('employee/update_empbasicinfo') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        page_loader_show();
                        $('#edit-basic_info-form button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        page_loader_hide();
                        if (response.status === 'success') {
                            $.alert({
                                title: 'Success',
                                content: response.message || 'Basic information updated successfully.',
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
                            $('#edit-basic_info-modal').modal('hide');
                        } else {
                            $.alert({
                                title: 'Error',
                                content: response.message || 'Failed to update basic information.',
                                type: 'red',
                                backgroundDismiss: true
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating basic information:', error);
                        page_loader_hide();
                        $.alert({
                            title: 'Error',
                            content: 'An error occurred while updating basic information.',
                            type: 'red',
                            backgroundDismiss: true
                        });
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#edit-basic_info-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });

        });
    })
</script>