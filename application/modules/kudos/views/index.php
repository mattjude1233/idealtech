<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <?php if (!empty($page_title)) : ?>
                    <h1 class="m-0"><?= ucfirst($page_title) ?></h1>
                <?php endif; ?>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Administration</li>
                    <li class="breadcrumb-item active">Kudos</li>
                </ol>
            </div>

            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="javascript:;" class="btn btn-pill btn-primary btn-md text-white add_kudos-btn">
                        <i class="fa fa-star m-r-5"></i> Add Kudos
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Main content -->
<div class="content" <?= empty($page_title) ? "style='padding-top:15px;'" : "" ?>>
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Kudos List</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <input type="text" name="search_kudos" id="search_kudos" placeholder="Search kudos..." class="form-control" style="width: 200px;">
                                        </div>
                                        <div>
                                            <input type="text" name="year_kudos" id="year_kudos" placeholder="Year" class="form-control yearpicker" style="width: 120px;" value="<?= !empty($yearsearch) ? $yearsearch : date('Y') ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <table class="table" id="kudosTable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Added By</th>
                                    <th>Date Added</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($kudoslist)) : ?>
                                    <?php foreach ($kudoslist as $intro) : ?>
                                        <tr data-hid="<?= $this->mysecurity->encrypt_url($intro['id']); ?>">
                                            <td>
                                                <!-- Kudos Image preview -->
                                                <?php if (!empty($intro['kudos_image'])) : ?>
                                                    <img src="<?= base_url($intro['kudos_image']) ?>" alt="Kudos Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else : ?>
                                                    <img src="<?= base_url('assets/images/no-image.png') ?>" alt="No Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php endif; ?>
                                            </td>

                                            <td><?= $intro['kudos_name'] ?></td>
                                            <td><?= $intro['kudos_category'] ?></td>
                                            <td>
                                                <?php if ($intro['active']) : ?>
                                                    <span class="badge badge-success">Active</span>
                                                <?php else : ?>
                                                    <span class="badge badge-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $intro['added_by_name'] ?></td>
                                            <td><?= date('d M Y', strtotime($intro['date_added'])) ?></td>
                                            <td>
                                                <?php if ($intro['active']) : ?>
                                                    <button class="btn btn-sm btn-outline-warning set-inactive-kudos" data-id="<?= $intro['id'] ?>">Set Inactive</button>
                                                <?php else : ?>
                                                    <button class="btn btn-sm btn-outline-success set-active-kudos" data-id="<?= $intro['id'] ?>">Set Active</button>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-outline-danger delete-kudos" data-id="<?= $intro['id'] ?>">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No kudos found for the selected period.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<div class="modal fade" id="add_kudos-modal" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h4 class="modal-title">Kudos</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="add_kudos-form">
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kudoslist_name">Name</label>
                                <input type="text" name="kudoslist_name" id="kudoslist_name" class="form-control" placeholder="Enter Name" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kudoslist_category">Category</label>
                                <select name="kudoslist_category" id="kudoslist_category" class="form-control select2-kudos" required>
                                    <option value=""></option>
                                    <?php foreach ($kudos_categories as $category) : ?>
                                        <option value="<?= $category['name'] ?>"><?= $category['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="Kudos_File">Upload Kudos File</label>
                        <input type="file" name="Kudos_File" id="Kudos_File" class="form-control-file" accept=".jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">Upload the kudos picture in JPG, JPEG, or PNG format.</small>
                    </div>

                    <hr>

                    <div id="kudos_file_preview"></div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    $(document).ready(function() {

        $(document).on('click', '.add_kudos-btn', function(e) {
            e.preventDefault();

            // reset the form
            $('#add_kudos-form')[0].reset();
            $('#kudos_file_preview').html('');
            $('#add_kudos-modal').modal('show');

            $('#add_kudos-form').off('submit').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: '<?= base_url('kudos/save') ?>',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function() {
                        page_loader_show();
                        $('#add_kudos-form button[type="submit"]').prop('disabled', true);
                    },
                    success: function(result) {
                        if (result.status === 'success') {
                            reloadKudosTable();
                            $('#add_kudos-modal').modal('hide');

                            $.alert({
                                title: 'Success!',
                                content: result.message,
                                type: 'green',
                                animateFromElement: false // prevents animation based on trigger element
                            });
                        } else {
                            showError(result.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        showError('An error occurred. Please try again.');
                    },
                    complete: function() {
                        page_loader_hide();
                        $('#add_kudos-form button[type="submit"]').prop('disabled', false);
                    }
                });
            });
        });


        // on kudos_file change, preview the file
        $('#Kudos_File').on('change', function() {
            var file = this.files[0];
            if (file) {
                var fileExt = file.name.split('.').pop().toLowerCase();

                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#kudos_file_preview').html(
                            '<img src="' + e.target.result + '" style="width: 100%; height: 100%; max-height:550px; object-fit: contain;">'
                        );
                    };
                    reader.readAsDataURL(file);
                } else if (fileExt === 'pdf') {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#kudos_file_preview').html(
                            '<embed src="' + e.target.result + '" type="application/pdf" width="100%" height="550px" />'
                        );
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#kudos_file_preview').html('Unsupported file type.');
                }
            } else {
                $('#kudos_file_preview').html('');
            }
        });


    })
</script>

<script>
    $(document).ready(function() {

        // on change year_kudos, period_kudos
        $('#year_kudos').on('change', function() {
            var year = $('#year_kudos').val();

            // format year to YYYY-MM
            year = moment(year, 'YYYY').format('YYYY');

            window.location.href = '<?= base_url('kudos/index') ?>/' + year
        });

        // Search functionality
        $('#search_kudos').on('keyup', function() {
            if ($.fn.DataTable.isDataTable('#kudosTable')) {
                $('#kudosTable').DataTable().search(this.value).draw();
            }
        });

        $('.yearpicker').datepicker({
            format: 'yyyy',
            autoclose: true,
            viewMode: 'years',
            minViewMode: 'years',
            todayHighlight: true
        });

        $('.select2-kudos').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: "Select Category",
            tags: true,
            tokenSeparators: [],
            dropdownParent: $('.select2-kudos').parent(),
            allowClear: true,
        });

        // Set Active Kudos
        $(document).on('click', '.set-active-kudos', function() {
            var id = $(this).data('id');
            $.confirm({
                title: 'Set as Active',
                content: 'Are you sure you want to set this kudos as active?',
                type: 'green',
                buttons: {
                    confirm: function() {
                        $.ajax({
                            url: '<?= base_url('kudos/set_active/') ?>' + id,
                            type: 'POST',
                            dataType: 'json',
                            beforeSend: function() {
                                page_loader_show();
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    reloadKudosTable();
                                    $.alert({
                                        title: 'Success!',
                                        content: res.message,
                                        type: 'green',
                                        animateFromElement: false
                                    });
                                } else {
                                    showError(res.message);
                                }
                            },
                            error: function() {
                                showError('An error occurred.');
                            },
                            complete: function() {
                                page_loader_hide();
                            }
                        });
                    },
                    cancel: function() {}
                }
            });
        });

        // Set Inactive Kudos
        $(document).on('click', '.set-inactive-kudos', function() {
            var id = $(this).data('id');
            $.confirm({
                title: 'Set as Inactive',
                content: 'Are you sure you want to set this kudos as inactive?',
                type: 'orange',
                buttons: {
                    confirm: function() {
                        $.ajax({
                            url: '<?= base_url('kudos/set_inactive/') ?>' + id,
                            type: 'POST',
                            dataType: 'json',
                            beforeSend: function() {
                                page_loader_show();
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    reloadKudosTable();
                                    $.alert({
                                        title: 'Success!',
                                        content: res.message,
                                        type: 'green',
                                        animateFromElement: false
                                    });
                                } else {
                                    showError(res.message);
                                }
                            },
                            error: function() {
                                showError('An error occurred.');
                            },
                            complete: function() {
                                page_loader_hide();
                            }
                        });
                    },
                    cancel: function() {}
                }
            });
        });

        // Delete Kudos
        $(document).on('click', '.delete-kudos', function() {
            var id = $(this).data('id');
            $.confirm({
                title: 'Delete Kudos',
                content: 'Are you sure you want to delete this kudos?',
                type: 'red',
                buttons: {
                    confirm: function() {
                        $.ajax({
                            url: '<?= base_url('kudos/delete/') ?>' + id,
                            type: 'POST',
                            dataType: 'json',
                            beforeSend: function() {
                                page_loader_show();
                            },
                            success: function(res) {
                                if (res.status === 'success') {
                                    reloadKudosTable();
                                    $.alert({
                                        title: 'Success!',
                                        content: res.message,
                                        type: 'green',
                                        animateFromElement: false
                                    });
                                } else {
                                    showError(res.message);
                                }
                            },
                            error: function() {
                                showError('An error occurred.');
                            },
                            complete: function() {
                                page_loader_hide();
                            }
                        });
                    },
                    cancel: function() {}
                }
            });
        });
    });

    function reloadKudosTable() {
        const $table = $('#kudosTable');

        <?php if (!empty($kudoslist)) : ?>
            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().destroy();
            }

            $("#kudosTable tbody").load(location.href + " #kudosTable tbody>*", function() {
                $table.DataTable({
                    paging: false,
                    lengthChange: false,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    responsive: true,
                    dom: 'rt' // Remove default search box, show only table and info
                });
            });
        <?php endif; ?>
    }

    $(document).ready(function() {
        reloadKudosTable();
    })
</script>