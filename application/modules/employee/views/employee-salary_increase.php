<div class="modal fade" id="employee-salary_increase-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Salary Increase: <span class="modal-empid"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="employee-salary_increase-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="current_salary">Current Salary</label>
                        <input id="current_salary" type="text" class="form-control" placeholder="Current Salary" readonly>
                    </div>

                    <div class="salary_increase-container">
                        <div class="row salary_increase-row">
                            <div class="form-group col-12 col-md-6">
                                <label for="new_salary">New Salary</label>
                                <input id="new_salary" name="new_salary" type="text" class="form-control number_only" placeholder="New Salary" required>
                            </div>

                            <div class="form-group col-12 col-md-6">
                                <label for="effective_date">Effective Date</label>
                                <input id="effective_date" name="effective_date" type="text" class="form-control" placeholder="Effective Date">
                            </div>

                            <div class="form-group col-12 col-md-12">
                                <label for="remarks">Remarks</label>
                                <textarea id="remarks" name="remarks" class="form-control" placeholder="Remarks"></textarea>
                            </div>
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
        // activate datepicker for start_date and end_date
        $('#employee-salary_increase-form').on('focus', 'input[name^="effective_date"]', function() {
            $(this).datepicker({
                format: 'mm/dd/yyyy',
                autoclose: true,
                todayHighlight: true
            });
        });

        $(document).on('click', '#employee-salary_increase-btn', function() {
            const empId = $(this).data('empid');
            $('#employee-salary_increase-modal .modal-empid').text('');

            // Reset the form
            $('#employee-salary_increase-form')[0].reset();

            // Fetch employee salary
            $.ajax({
                url: '<?= base_url('employee/get_current_salary') ?>',
                type: 'POST',
                data: {
                    userid: empId
                },
                dataType: 'json',
                beforeSend: function() {
                    page_loader_show();
                    $('#employee-salary_increase-form button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    page_loader_hide();
                    if (response.status === 'success') {
                        const data = response.data;
                        $('#employee-salary_increase-modal .modal-empid').text(data.emp_id);

                        if (data.salary) {
                            $('#current_salary').val(data.salary);
                        }
                        $('#employee-salary_increase-modal').modal('show');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching employee salary details:', error);
                    page_loader_hide();
                    $.alert({
                        title: 'Error',
                        content: 'An error occurred while fetching employee salary details.',
                        type: 'red',
                        backgroundDismiss: true
                    });
                },
                complete: function() {
                    page_loader_hide();
                    $('#employee-salary_increase-form button[type="submit"]').prop('disabled', false);
                }
            });

            // process form submission
            $('#employee-salary_increase-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();

                // Add emp_id to the form data
                formData.push({
                    name: 'emp_id',
                    value: empId
                });

                $.ajax({
                    url: '<?= base_url('employee/update_salary_increase') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        page_loader_show();
                        $('#employee-salary_increase-form button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        page_loader_hide();
                        if (response.status === 'success') {
                            $.alert({
                                title: 'Success',
                                content: response.message || 'Salary information updated successfully.',
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
                            $('#employee-salary_increase-modal').modal('hide');
                        } else {
                            $.alert({
                                title: 'Error',
                                content: response.message || 'Failed to update salary information.',
                                type: 'red',
                                backgroundDismiss: true
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating salary information:', error);
                        page_loader_hide();
                        $.alert({
                            title: 'Error',
                            content: 'An error occurred while updating salary information.',
                            type: 'red',
                            backgroundDismiss: true
                        });
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#employee-salary_increase-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });

        });
    })
</script>