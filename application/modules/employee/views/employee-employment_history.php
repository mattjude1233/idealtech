<div class="modal fade" id="employee-employment_history-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Employment History <span class="modal-empid text-muted"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="employee-employment_history-form">

                <div class="modal-body">
                    <div class="employment_history-container">
                        <div class="row employment_history-row">
                            <div class="form-group col-12 col-md-6">
                                <label for="company_name">Company Name</label>
                                <input id="company_name" name="employment_history[company_name][]" type="text" class="form-control" placeholder="Company Name">
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="Designation">Designation</label>
                                <input id="Designation" name="employment_history[designation][]" type="text" class="form-control" placeholder="Designation">
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="start_date">Start Date</label>
                                <input id="start_date" name="employment_history[start_date][]" type="text" class="form-control" placeholder="Start Date">
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="end_date">End Date</label>
                                <input id="end_date" name="employment_history[end_date][]" type="text" class="form-control" placeholder="End Date">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-right mb-3 m-t-10">
                        <a href="javascript:;" class="btn btn-info btn-xs" id="employment_history-add_btn">Add More</a>
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
        $(document).on('click', '#employment_history-add_btn', function() {
            const newRow = $('.employment_history-row:first').clone();
            newRow.find('input').val(''); // Clear input values

            // add border top to the new row, and padding top 10px
            newRow.css({
                'border-top': '1px solid #ccc',
                'padding-top': '10px'
            });

            // append new button to remove the row
            newRow.append('<div class="col-12 text-right"><a href="javascript:;" class="btn btn-danger btn-xs employment_history-remove_this m-b-5">Remove</a></div>');

            $('#employee-employment_history-form .employment_history-container').append(newRow);
        });

        // remove the row when clicking on the remove button
        $(document).on('click', '.employment_history-remove_this', function() {
            const rows = $('#employee-employment_history-form .employment_history-row');
            $(this).closest('.employment_history-row').remove();
        });

        // activate datepicker for start_date and end_date
        $('#employee-employment_history-form').on('focus', 'input[name^="employment_history[start_date]"], input[name^="employment_history[end_date]"]', function() {
            $(this).datepicker({
                format: 'mm/dd/yyyy',
                autoclose: true,
                todayHighlight: true
            });
        });

        $(document).on('click', '#employee-employment_history-btn', function() {
            const empId = $(this).data('empid');
            $('#employee-employment_history-modal .modal-empid').text('');

            // Reset the form
            $('#employee-employment_history-form')[0].reset();

            // Fetch employee details
            $.ajax({
                url: '<?= base_url('employee/get_employment_history') ?>',
                type: 'POST',
                data: {
                    userid: empId
                },
                dataType: 'json',
                beforeSend: function() {
                    page_loader_show();
                    $('#employee-employment_history-form button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    page_loader_hide();
                    if (response.status === 'success') {
                        const data = response.data;
                        $('#employee-employment_history-modal .modal-empid').text(data.emp_id);

                        // if data.employment_history is not empty, populate the form
                        if (data.employment_history && data.employment_history.length > 0) {
                            data.employment_history.forEach(function(item, index) {
                                if (index === 0) {
                                    $('#company_name').val(item.company_name);
                                    $('#Designation').val(item.designation);
                                    $('#start_date').val(item.start_date);
                                    $('#end_date').val(item.end_date);
                                } else {
                                    const newRow = $('.employment_history-row:first').clone();
                                    newRow.find('input[name="employment_history[company_name][]"]').val(item.company_name);
                                    newRow.find('input[name="employment_history[designation][]"]').val(item.designation);
                                    newRow.find('input[name="employment_history[start_date][]"]').val(item.start_date);
                                    newRow.find('input[name="employment_history[end_date][]"]').val(item.end_date);

                                    // add border top to the new row, and padding top 10px
                                    newRow.css({
                                        'border-top': '1px solid #ccc',
                                        'padding-top': '10px'
                                    });

                                    // append new button to remove the row
                                    newRow.append('<div class="col-12 text-right"><a href="javascript:;" class="btn btn-danger btn-xs employment_history-remove_this m-b-5">Remove</a></div>');

                                    $('#employee-employment_history-form .employment_history-container').append(newRow);
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
                    $('#employee-employment_history-form button[type="submit"]').prop('disabled', false);
                },
                complete: function() {
                    page_loader_hide();
                    $('#employee-employment_history-modal').modal('show');
                    $('#employee-employment_history-form button[type="submit"]').prop('disabled', false);
                }
            });

            // process form submission
            $('#employee-employment_history-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();

                // Add emp_id to the form data
                formData.push({
                    name: 'emp_id',
                    value: empId
                });

                $.ajax({
                    url: '<?= base_url('employee/update_employment_history') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        page_loader_show();
                        $('#employee-employment_history-form button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        page_loader_hide();
                        if (response.status === 'success') {
                            $.alert({
                                title: 'Success',
                                content: response.message || 'Employment history updated successfully.',
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
                            $('#employee-employment_history-modal').modal('hide');
                        } else {
                            $.alert({
                                title: 'Error',
                                content: response.message || 'Failed to update employment history.',
                                type: 'red',
                                backgroundDismiss: true
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating employment history:', error);
                        page_loader_hide();
                        $.alert({
                            title: 'Error',
                            content: 'An error occurred while updating employment history.',
                            type: 'red',
                            backgroundDismiss: true
                        });
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#employee-employment_history-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });

        });
    })
</script>