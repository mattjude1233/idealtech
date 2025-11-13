<div class="modal fade" id="employee-employee_document-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Upload Employee Document</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="employee-employee_document-form">

                <div class="modal-body">
                    <div class="form-group col-12">
                        <label for="document">Document Name</label>
                        <input id="document" name="document" type="text" class="form-control" placeholder="File Name">
                    </div>

                    <div class="form-group col-12">
                        <label for="File_Attachment">File Attachment</label>
                        <input id="File_Attachment" name="File_Attachment" type="file" class="form-control-file">
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
        $(document).on('click', '#employee-employee_document-btn', function() {
            const empId = $(this).data('empid');

            // clear the form fields
            $('#employee-employee_document-form')[0].reset();

            // show the modal
            $('#employee-employee_document-modal').modal('show');

            // process the form submission
            $('#employee-employee_document-form').off('submit').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('empId', empId);

                $.ajax({
                    url: '<?= base_url('employee/employee_document_upload') ?>',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json', // Add this
                    beforeSend: function() {
                        page_loader_show();
                        $('#employee-employee_document-form button[type="submit"]').prop('disabled', true);
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
                        alert('Error uploading document: ' + error);
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#employee-employee_document-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });

        });

        $(document).on('click', '.remove-document-btn', function() {
            const docid = $(this).data('docid');

            // check if has data-doctype attribute
            const doctype = $(this).data('doctype');

            $.confirm({
                title: 'Confirm Removal',
                content: 'Are you sure you want to remove this document?',
                type: 'red',
                buttons: {
                    yes: {
                        text: 'Yes',
                        btnClass: 'btn-red',
                        action: function() {
                            $.ajax({
                                url: '<?= base_url('employee/employee_document_remove') ?>',
                                type: 'POST',
                                data: {
                                    docid: docid,
                                    doctype: doctype
                                },
                                dataType: 'json',
                                beforeSend: function() {
                                    page_loader_show();
                                },
                                success: function(response) {
                                    page_loader_hide();
                                    if (response.status === 'success') {
                                        $.alert({
                                            title: 'Success',
                                            content: response.message || 'Document removed successfully.',
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
                                            content: response.message || 'Failed to remove document.',
                                            type: 'red',
                                            backgroundDismiss: true
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    alert('Error removing document: ' + error);
                                },
                                complete: function() {
                                    page_loader_hide();
                                }
                            });
                        }
                    },
                    no: {
                        text: 'No',
                        btnClass: 'btn-default'
                    }
                }
            });
        });
    });
</script>