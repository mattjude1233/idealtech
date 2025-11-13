<div class="modal fade" id="edit-personal_info-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Personal Information: <span class="modal-empid">EPM-001</span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="edit-personal_info-form">

                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="Tin">TIN</label>
                            <input id="tin" name="tin" type="text" class="form-control" placeholder="TIN">
                        </div>

                        <div class="form-group col-12">
                            <label for="sss">SSS</label>
                            <input id="sss" name="sss" type="text" class="form-control" placeholder="SSS">
                        </div>

                        <div class="form-group col-12">
                            <label for="pag_ibig">Pag-Ibig</label>
                            <input id="pag_ibig" name="pag_ibig" type="text" class="form-control" placeholder="Pag-Ibig">
                        </div>

                        <div class="form-group col-12">
                            <label for="phil_health">Phil Health</label>
                            <input id="phil_health" name="phil_health" type="text" class="form-control" placeholder="Phil Health">
                        </div>

                        <div class="form-group col-12">
                            <label for="hmo_account">HMO</label>
                            <input id="hmo_account" name="hmo_account" type="text" class="form-control" placeholder="HMO">
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
        $(document).on('click', '#edit-personal_info-btn', function() {
            const empId = $(this).data('empid');
            $('#edit-personal_info-modal .modal-empid').text('');

            // Reset the form
            $('#edit-personal_info-form')[0].reset();

            // Fetch employee details
            $.ajax({
                url: '<?= base_url('employee/get_emppersonalinfo') ?>',
                type: 'POST',
                data: {
                    userid: empId
                },
                dataType: 'json',
                beforeSend: function() {
                    page_loader_show();
                    $('#edit-personal_info-form button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    page_loader_hide();
                    if (response.status === 'success') {
                        const data = response.data;

                        $('#edit-personal_info-modal .modal-empid').text(data.emp_id);
                        $('#tin').val(data.tin);
                        $('#sss').val(data.sss);
                        $('#pag_ibig').val(data.pag_ibig);
                        $('#phil_health').val(data.phil_health);
                        $('#hmo_account').val(data.hmo_account);
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
                    $('#edit-personal_info-form button[type="submit"]').prop('disabled', false);
                },
                complete: function() {
                    page_loader_hide();
                    $('#edit-personal_info-modal').modal('show');
                    $('#edit-personal_info-form button[type="submit"]').prop('disabled', false);
                }
            });

            // process form submission
            $('#edit-personal_info-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();

                // Add emp_id to the form data
                formData.push({
                    name: 'emp_id',
                    value: empId
                });

                $.ajax({
                    url: '<?= base_url('employee/update_emppersonalinfo') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        page_loader_show();
                        $('#edit-personal_info-form button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        page_loader_hide();
                        if (response.status === 'success') {
                            $.alert({
                                title: 'Success',
                                content: response.message || 'Personal information updated successfully.',
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
                            $('#edit-personal_info-modal').modal('hide');
                        } else {
                            $.alert({
                                title: 'Error',
                                content: response.message || 'Failed to update personal information.',
                                type: 'red',
                                backgroundDismiss: true
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating personal information:', error);
                        page_loader_hide();
                        $.alert({
                            title: 'Error',
                            content: 'An error occurred while updating personal information.',
                            type: 'red',
                            backgroundDismiss: true
                        });
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#edit-personal_info-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });

        });
    })
</script>