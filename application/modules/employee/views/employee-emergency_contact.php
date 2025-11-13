<div class="modal fade" id="employee-emergency_contact-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Emergency Contact Details: <span class="modal-empid">EPM-001</span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="employee-emergency_contact-form">

                <div class="modal-body">
                    <!-- Primary Contact Details  -->
                    <h5>Primary Contact</h5>

                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="pc_name">Name</label>
                            <input id="pc_name" name="primary_contact[name]" type="text" class="form-control" placeholder="Name">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="pc_relationship">Relationship</label>
                            <input id="pc_relationship" name="primary_contact[relationship]" type="text" class="form-control" placeholder="Relationship">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="pc_phone1">Phone No. 1</label>
                            <input id="pc_phone1" name="primary_contact[phone1]" type="text" class="form-control" placeholder="Phone">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="pc_phone2">Phone No. 2</label>
                            <input id="pc_phone2" name="primary_contact[phone2]" type="text" class="form-control" placeholder="Phone">
                        </div>

                        <!-- Address -->
                        <div class="form-group col-12">
                            <label for="pc_address">Address</label>
                            <textarea id="pc_address" name="primary_contact[address]" class="form-control" rows="2" placeholder="Address"></textarea>
                        </div>
                    </div>

                    <hr>

                    <!-- Secondary Contact Details  -->
                    <h5>Secondary Contact</h5>

                    <div class="row">
                        <div class="form-group col-12 col-md-6">
                            <label for="sc_name">Name</label>
                            <input id="sc_name" name="secondary_contact[name]" type="text" class="form-control" placeholder="Name">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="sc_relationship">Relationship</label>
                            <input id="sc_relationship" name="secondary_contact[relationship]" type="text" class="form-control" placeholder="Relationship">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="sc_phone1">Phone No. 1</label>
                            <input id="sc_phone1" name="secondary_contact[phone1]" type="text" class="form-control" placeholder="Phone">
                        </div>

                        <div class="form-group col-12 col-md-6">
                            <label for="sc_phone2">Phone No. 2</label>
                            <input id="sc_phone2" name="secondary_contact[phone2]" type="text" class="form-control" placeholder="Phone">
                        </div>

                        <!-- Address -->
                        <div class="form-group col-12">
                            <label for="sc_address">Address</label>
                            <textarea id="sc_address" name="secondary_contact[address]" class="form-control" rows="2" placeholder="Address"></textarea>
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
        $(document).on('click', '#employee-emergency_contact-btn', function() {
            const empId = $(this).data('empid');
            $('#employee-emergency_contact-modal .modal-empid').text('');

            // Reset the form
            $('#employee-emergency_contact-form')[0].reset();

            // Fetch employee details
            $.ajax({
                url: '<?= base_url('employee/get_emp_contact') ?>',
                type: 'POST',
                data: {
                    userid: empId
                },
                dataType: 'json',
                beforeSend: function() {
                    page_loader_show();
                    $('#employee-emergency_contact-form button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    page_loader_hide();
                    if (response.status === 'success') {
                        const data = response.data;
                        $('#employee-emergency_contact-modal .modal-empid').text(data.emp_id);

                        console.log(data.primary_contact);

                        // Populate primary contact details
                        $('#pc_name').val(data.primary_contact.name || '');
                        $('#pc_relationship').val(data.primary_contact.relationship || '');
                        $('#pc_phone1').val(data.primary_contact.phone1 || '');
                        $('#pc_phone2').val(data.primary_contact.phone2 || '');
                        // Populate secondary contact details
                        $('#sc_name').val(data.secondary_contact.name || '');
                        $('#sc_relationship').val(data.secondary_contact.relationship || '');
                        $('#sc_phone1').val(data.secondary_contact.phone1 || '');
                        $('#sc_phone2').val(data.secondary_contact.phone2 || '');

                        $('#pc_address').val(data.primary_contact.address || '');
                        $('#sc_address').val(data.secondary_contact.address || '');
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
                    $('#employee-emergency_contact-form button[type="submit"]').prop('disabled', false);
                },
                complete: function() {
                    page_loader_hide();
                    $('#employee-emergency_contact-modal').modal('show');
                    $('#employee-emergency_contact-form button[type="submit"]').prop('disabled', false);
                }
            });

            // process form submission
            $('#employee-emergency_contact-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serializeArray();

                // Add emp_id to the form data
                formData.push({
                    name: 'emp_id',
                    value: empId
                });

                $.ajax({
                    url: '<?= base_url('employee/update_emp_contact') ?>',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function() {
                        page_loader_show();
                        $('#employee-emergency_contact-form button[type="submit"]').prop('disabled', true);
                    },
                    success: function(response) {
                        page_loader_hide();
                        if (response.status === 'success') {
                            $.alert({
                                title: 'Success',
                                content: response.message || 'Emergency contact information updated successfully.',
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
                            $('#employee-emergency_contact-modal').modal('hide');
                        } else {
                            $.alert({
                                title: 'Error',
                                content: response.message || 'Failed to update emergency contact information.',
                                type: 'red',
                                backgroundDismiss: true
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating emergency contact information:', error);
                        page_loader_hide();
                        $.alert({
                            title: 'Error',
                            content: 'An error occurred while updating emergency contact information.',
                            type: 'red',
                            backgroundDismiss: true
                        });
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#employee-emergency_contact-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });

        });
    })
</script>