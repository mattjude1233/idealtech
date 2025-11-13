<div class="modal fade" id="employee-employee_requirements-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Upload Employee Requirements</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="employee-employee_requirements-form">

                <div class="modal-body">
                    <div class="form-group col-12">
                        <label for="requirements">Requirements Name</label>
                        <input id="requirements" name="requirements" type="text" class="form-control" placeholder="File Name" readonly>
                    </div>

                    <div class="form-group col-12">
                        <label for="File_Attachment">File Attachment</label>
                        <input id="File_Attachment" name="File_Attachment" type="file" class="form-control-file">
                    </div>

                    <div class="form-group col-12">
                        <label for="remarks">Remarks (Optional)</label>
                        <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter any remarks here..."></textarea>
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
        $(document).on('click', '.employee-employee_requirements-btn', function() {
            const empId = $(this).data('empid');
            const requirementSection = $(this).closest('tr').data('section');
            const requirementItem = $(this).closest('tr').data('item');

            // clear the form fields
            $('#employee-employee_requirements-form')[0].reset();
            $('#employee-employee_requirements-form #requirements').val(requirementSection + ' - ' + requirementItem).prop('readonly', true);

            // show the modal
            $('#employee-employee_requirements-modal').modal('show');

            // process the form submission
            $('#employee-employee_requirements-form').off('submit').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('empId', empId);

                $.ajax({
                    url: '<?= base_url('employee/employee_requirements_upload') ?>',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json', // Add this
                    beforeSend: function() {
                        page_loader_show();
                        $('#employee-employee_requirements-form button[type="submit"]').prop('disabled', true);
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
                        alert('Error uploading requirements: ' + error);
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#employee-employee_requirements-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });

        });
    })
</script>