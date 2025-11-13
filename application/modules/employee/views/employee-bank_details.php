<div class="modal fade" id="employee-bank_details-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Bank Details: <span class="modal-empid">EPM-001</span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="employee-bank_details-form">

                <div class="modal-body">
                    <!-- Primary Bank Details  -->
                    <h5>Primary Bank</h5>

                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="pb_name">Bank Name</label>
                            <input id="pb_name" name="primary_bank[name]" type="text" class="form-control" placeholder="Bank Name">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="pb_number">Account Number</label>
                            <input id="pb_number" name="primary_bank[number]" type="text" class="form-control" placeholder="Account Number">
                        </div>
                    </div>

                    <hr>

                    <!-- Secondary Bank Details  -->
                    <h5>Secondary Bank</h5>

                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="sb_name">Bank Name</label>
                            <input id="sb_name" name="secondary_bank[name]" type="text" class="form-control" placeholder="Bank Name">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="sb_number">Account Number</label>
                            <input id="sb_number" name="secondary_bank[number]" type="text" class="form-control" placeholder="Account Number">
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

        $(document).on('click', '#employee-bank_details-btn', function() {
            const empId = $(this).data('empid');
            $('#employee-bank_details-modal .modal-empid').text('');

            // Reset the form
            $('#employee-bank_details-form')[0].reset();

            // Fetch employee details
            $.ajax({
                url: '<?= base_url('employee/get_emp_bank') ?>',
                type: 'POST',
                data: {
                    userid: empId
                },
                dataType: 'json',
                beforeSend: function() {
                    page_loader_show();
                    $('#employee-bank_details-form button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    page_loader_hide();
                    if (response.status === 'success') {
                        const data = response.data;
                        $('#employee-bank_details-modal .modal-empid').text(data.emp_id);

                        // Populate primary bank details
                        $('#pb_name').val(data.primary_bank.name || '');
                        $('#pb_number').val(data.primary_bank.number || '');
                        // Populate secondary bank details

                        if (data.secondary_bank) {
                            $('#sb_name').val(data.secondary_bank.name || '');
                            $('#sb_number').val(data.secondary_bank.number || '');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching bank details:', error);
                    page_loader_hide();
                    $.alert({
                        title: 'Error',
                        content: 'An error occurred while fetching bank details.',
                        type: 'red',
                        backgroundDismiss: true
                    });
                    $('#employee-bank_details-form button[type="submit"]').prop('disabled', false);
                },
                complete: function() {
                    page_loader_hide();
                    $('#employee-bank_details-modal').modal('show');
                    $('#employee-bank_details-form button[type="submit"]').prop('disabled', false);
                }
            });

            // process form submission
            $('#employee-bank_details-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();

                // Add emp_id to the form data
                formData.push({
                    name: 'emp_id',
                    value: empId
                });

                $.ajax({
                    url: '<?= base_url('employee/update_emp_bank') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        page_loader_show();
                        $('#employee-bank_details-form button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        page_loader_hide();
                        if (response.status === 'success') {
                            $.alert({
                                title: 'Success',
                                content: response.message || 'Bank information updated successfully.',
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
                            $('#employee-bank_details-modal').modal('hide');
                        } else {
                            $.alert({
                                title: 'Error',
                                content: response.message || 'Failed to update bank information.',
                                type: 'red',
                                backgroundDismiss: true
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating bank information:', error);
                        page_loader_hide();
                        $.alert({
                            title: 'Error',
                            content: 'An error occurred while updating bank information.',
                            type: 'red',
                            backgroundDismiss: true
                        });
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#employee-bank_details-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });

        });
    })
</script>