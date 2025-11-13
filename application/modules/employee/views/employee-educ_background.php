<div class="modal fade" id="employee-educ_background-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Education Information: <span class="modal-empid"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="employee-educ_background-form">

                <div class="modal-body">
                    <div class="educ_background-container">
                        <div class="row educ_background-row">
                            <div class="form-group col-12 col-md-6">
                                <label for="institution_name">Institution Name</label>
                                <input id="institution_name" name="educ_background[institution_name][]" type="text" class="form-control" placeholder="Institution Name">
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="Course">Course</label>
                                <input id="Course" name="educ_background[course][]" type="text" class="form-control" placeholder="Course">
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="start_date">Start Date</label>
                                <input id="start_date" name="educ_background[start_date][]" type="text" class="form-control" placeholder="Start Date">
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="end_date">End Date</label>
                                <input id="end_date" name="educ_background[end_date][]" type="text" class="form-control" placeholder="End Date">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-right mb-3 m-t-10">
                        <a href="javascript:;" class="btn btn-info btn-xs" id="educ_background-add_btn">Add More</a>
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
        $(document).on('click', '#educ_background-add_btn', function() {
            const newRow = $('.educ_background-row:first').clone();
            newRow.find('input').val(''); // Clear input values

            // add border top to the new row, and padding top 10px
            newRow.css({
                'border-top': '1px solid #ccc',
                'padding-top': '10px'
            });

            // append new button to remove the row
            newRow.append('<div class="col-12 text-right"><a href="javascript:;" class="btn btn-danger btn-xs educ_background-remove_this m-b-5">Remove</a></div>');

            $('#employee-educ_background-form .educ_background-container').append(newRow);
        });

        // remove the row when clicking on the remove button
        $(document).on('click', '.educ_background-remove_this', function() {
            const rows = $('#employee-educ_background-form .educ_background-row');
            $(this).closest('.educ_background-row').remove();
        });

        // activate datepicker for start_date and end_date
        $('#employee-educ_background-form').on('focus', 'input[name^="educ_background[start_date]"], input[name^="educ_background[end_date]"]', function() {
            $(this).datepicker({
                format: 'mm/dd/yyyy',
                autoclose: true,
                todayHighlight: true
            });
        });

        $(document).on('click', '#employee-educ_background-btn', function() {
            const empId = $(this).data('empid');
            $('#employee-educ_background-modal .modal-empid').text('');

            // Reset the form
            $('#employee-educ_background-form')[0].reset();

            // Fetch employee details
            $.ajax({
                url: '<?= base_url('employee/get_educ_background') ?>',
                type: 'POST',
                data: {
                    userid: empId
                },
                dataType: 'json',
                beforeSend: function() {
                    page_loader_show();
                    $('#employee-educ_background-form button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    page_loader_hide();
                    if (response.status === 'success') {
                        const data = response.data;
                        $('#employee-educ_background-modal .modal-empid').text(data.emp_id);

                        // if data.educ_background is not empty, populate the form
                        if (data.educ_background && data.educ_background.length > 0) {
                            data.educ_background.forEach(function(item, index) {
                                if (index === 0) {
                                    $('#institution_name').val(item.institution_name);
                                    $('#Course').val(item.course);
                                    $('#start_date').val(item.start_date);
                                    $('#end_date').val(item.end_date);
                                } else {
                                    const newRow = $('.educ_background-row:first').clone();
                                    newRow.find('input[name="educ_background[institution_name][]"]').val(item.institution_name);
                                    newRow.find('input[name="educ_background[course][]"]').val(item.course);
                                    newRow.find('input[name="educ_background[start_date][]"]').val(item.start_date);
                                    newRow.find('input[name="educ_background[end_date][]"]').val(item.end_date);

                                    // add border top to the new row, and padding top 10px
                                    newRow.css({
                                        'border-top': '1px solid #ccc',
                                        'padding-top': '10px'
                                    });

                                    // append new button to remove the row
                                    newRow.append('<div class="col-12 text-right"><a href="javascript:;" class="btn btn-danger btn-xs educ_background-remove_this m-b-5">Remove</a></div>');

                                    $('#employee-educ_background-form .educ_background-container').append(newRow);
                                }
                            });
                        }
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
                    $('#employee-educ_background-form button[type="submit"]').prop('disabled', false);
                },
                complete: function() {
                    page_loader_hide();
                    $('#employee-educ_background-modal').modal('show');
                    $('#employee-educ_background-form button[type="submit"]').prop('disabled', false);
                }
            });

            // process form submission
            $('#employee-educ_background-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();

                // Add emp_id to the form data
                formData.push({
                    name: 'emp_id',
                    value: empId
                });

                $.ajax({
                    url: '<?= base_url('employee/update_educ_background') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        page_loader_show();
                        $('#employee-educ_background-form button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        page_loader_hide();
                        if (response.status === 'success') {
                            $.alert({
                                title: 'Success',
                                content: response.message || 'Education information updated successfully.',
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
                            $('#employee-educ_background-modal').modal('hide');
                        } else {
                            $.alert({
                                title: 'Error',
                                content: response.message || 'Failed to update education information.',
                                type: 'red',
                                backgroundDismiss: true
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating education information:', error);
                        page_loader_hide();
                        $.alert({
                            title: 'Error',
                            content: 'An error occurred while updating education information.',
                            type: 'red',
                            backgroundDismiss: true
                        });
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#employee-educ_background-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });

        });
    })
</script>