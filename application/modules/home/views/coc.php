<?php
// Page data
$title = !empty($page_title) ? ucfirst($page_title) : 'Memorandum and COC';

// Set default document and PDF URL
$default_pdf = 'dist/pdf/IDEAL TECH STAFFING - Code of Conduct and Discipline.pdf';
$pdf_url = base_url($default_pdf);

if (!empty($active_document)) {
    if (isset($active_document['file_path']) && isset($active_document['file_name'])) {
        $pdf_url = base_url($active_document['file_path'] . $active_document['file_name']);
    }
}

// Group documents by category
$grouped_documents = array();
if (!empty($documents)) {
    foreach ($documents as $doc) {
        $category = $doc['category'];
        if (!isset($grouped_documents[$category])) {
            $grouped_documents[$category] = array();
        }
        $grouped_documents[$category][] = $doc;
    }
}
?>

<style>
    .document-sidebar {
        background: #f8f9fa;
        border-right: 1px solid #dee2e6;
        height: calc(100vh - 260px);
        min-height: 420px;
        overflow-y: auto;
    }

    .document-item {
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .document-item:hover {
        background-color: #e9ecef;
    }

    .document-item:hover .document-delete-btn,
    .document-item:hover .document-feature-btn,
    .document-item:hover .document-edit-btn {
        opacity: 1 !important;
    }

    .document-item.active {
        background-color: #007bff;
        color: white;
    }

    .document-item.active:hover {
        background-color: #0056b3;
    }

    .document-item.active .document-delete-btn,
    .document-item.active .document-feature-btn,
    .document-item.active .document-edit-btn {
        opacity: 1 !important;
    }

    .document-actions {
        position: absolute;
        top: 8px;
        right: 8px;
    }

    .document-actions .btn-group-vertical .btn {
        padding: 3px 6px;
        font-size: 10px;
        line-height: 1;
    }

    .category-header {
        background: #e9ecef;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .document-meta {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .upload-section {
        border-top: 1px solid #dee2e6;
        background: #fff;
    }

    .file-upload-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }

    .file-upload-input {
        position: absolute;
        left: -9999px;
    }

    .file-upload-label {
        cursor: pointer;
        display: block;
        padding: 8px 12px;
        border: 2px dashed #ccc;
        border-radius: 4px;
        text-align: center;
        transition: all 0.2s ease;
    }

    .file-upload-label:hover {
        border-color: #007bff;
        background-color: #f8f9fa;
    }

    .file-selected {
        border-color: #28a745;
        background-color: #d4edda;
    }

    .pdf-viewer-container {
        height: calc(100vh - 260px);
        min-height: 420px;
    }

    .document-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
    }
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url() ?>" aria-label="Home">
                                <i class="fa fa-home" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Memorandum and COC</li>
                    </ol>
                </nav>
            </div>
            <div class="col-sm-6 text-right">
                <?php if (check_function('manage_coc_memo')): ?>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadModal">
                        <i class="fa fa-upload"></i> Upload Document
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $this->session->flashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Main content -->
<div class="content" <?= empty($page_title) ? "style='padding-top:15px;'" : "" ?>>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="documentSearch" placeholder="Search documents...">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0 document-sidebar">

                        <!-- Document List -->
                        <div id="documentList">
                            <?php if (!empty($grouped_documents)): ?>
                                <?php foreach ($grouped_documents as $category => $docs): ?>
                                    <?php
                                    $category_name = $category;
                                    if (!empty($categories)) {
                                        foreach ($categories as $cat) {
                                            if ($cat['keyid'] === $category) {
                                                $category_name = $cat['value'];
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="category-header p-2 sticky-top">
                                        <?= htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <?php foreach ($docs as $doc): ?>
                                        <div class="document-item p-3 border-bottom <?= (!empty($active_document) && $active_document['id'] == $doc['id']) ? 'active' : '' ?>"
                                            data-doc-id="<?= $doc['id'] ?>"
                                            data-doc-title="<?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?>"
                                            onclick="loadDocument(<?= $doc['id'] ?>)">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="font-weight-bold mb-1" style="font-size: 0.875rem;">
                                                        <?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?>
                                                        <?php if ($doc['is_featured']): ?>
                                                            <span class="badge badge-warning badge-sm ml-1">Featured</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if (!empty($doc['description'])): ?>
                                                        <div class="text-muted mb-1" style="font-size: 0.75rem;">
                                                            <?= character_limiter(htmlspecialchars($doc['description'], ENT_QUOTES, 'UTF-8'), 60) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="document-meta">
                                                        <i class="fa fa-eye"></i> <?= $doc['view_count'] ?> views
                                                        <br>
                                                        <i class="fa fa-clock"></i> <?= date('M d, Y', strtotime($doc['upload_date'])) ?>
                                                    </div>
                                                </div>
                                                <?php if (check_function('manage_coc_memo')): ?>
                                                    <div class="document-actions">
                                                        <div class="btn-group-vertical" role="group">
                                                            <button type="button"
                                                                class="btn btn-sm <?= $doc['is_featured'] ? 'btn-warning' : 'btn-outline-warning' ?> document-feature-btn mb-1"
                                                                onclick="event.stopPropagation(); toggleFeatured(<?= $doc['id'] ?>, <?= $doc['is_featured'] ? 'false' : 'true' ?>)"
                                                                title="<?= $doc['is_featured'] ? 'Remove from featured' : 'Set as featured' ?>"
                                                                style="opacity: 0; transition: opacity 0.2s;">
                                                                <i class="fa fa-star fa-xs"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-primary document-edit-btn mb-1"
                                                                onclick="event.stopPropagation(); editDocument(<?= $doc['id'] ?>)"
                                                                title="Edit document info"
                                                                style="opacity: 0; transition: opacity 0.2s;">
                                                                <i class="fa fa-edit fa-xs"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger document-delete-btn"
                                                                onclick="event.stopPropagation(); deleteDocumentAjax(<?= $doc['id'] ?>)"
                                                                title="Delete document"
                                                                style="opacity: 0; transition: opacity 0.2s;">
                                                                <i class="fa fa-trash fa-xs"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">
                                    <i class="fa fa-file-alt fa-2x mb-2"></i>
                                    <p>No documents available.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Viewer -->
            <div class="col-lg-9 col-md-8">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
                        <span class="mr-auto font-weight-bold">
                            <?= !empty($active_document) ? htmlspecialchars($active_document['title'], ENT_QUOTES, 'UTF-8') : 'Document Viewer' ?>
                        </span>
                        <div class="btn-group" role="group" aria-label="Document actions">
                            <?php if (!empty($active_document)): ?>
                                <a class="btn btn-sm btn-outline-primary"
                                    href="<?= base_url('home/download_document/' . $active_document['id']) ?>"
                                    title="Download document">
                                    <i class="fa fa-download" aria-hidden="true"></i> Download
                                </a>
                                <a class="btn btn-sm btn-outline-primary"
                                    href="<?= $pdf_url ?>"
                                    target="_blank"
                                    rel="noopener"
                                    title="Open in new tab">
                                    <i class="fa fa-external-link-alt" aria-hidden="true"></i> Open
                                </a>
                                <?php if (check_function('manage_coc_memo')): ?>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete(<?= $active_document['id'] ?>)"
                                        title="Delete document">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body p-0 position-relative">
                        <!-- Document viewer wrapper -->
                        <div id="documentViewer" class="pdf-viewer-container">
                            <?php if (!empty($active_document)): ?>
                                <?php if (in_array(strtolower(pathinfo($active_document['file_name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                    <!-- Image viewer -->
                                    <div class="text-center p-3">
                                        <img src="<?= $pdf_url ?>"
                                            alt="<?= htmlspecialchars($active_document['title'], ENT_QUOTES, 'UTF-8') ?>"
                                            class="img-fluid"
                                            style="max-height: calc(100vh - 300px);">
                                    </div>
                                <?php else: ?>
                                    <!-- PDF viewer -->
                                    <object data="<?= $pdf_url ?>#toolbar=1&navpanes=1&scrollbar=1"
                                        type="application/pdf"
                                        width="100%"
                                        height="100%">
                                        <div class="text-center p-5">
                                            <i class="fa fa-file-pdf fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Your browser doesn't support PDF viewing.</p>
                                            <a href="<?= $pdf_url ?>"
                                                target="_blank"
                                                class="btn btn-primary">
                                                <i class="fa fa-external-link-alt"></i> Open PDF in New Tab
                                            </a>
                                        </div>
                                    </object>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <div class="text-center">
                                        <i class="fa fa-file-alt fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">Select a document to view</h5>
                                        <p class="text-muted">Choose a document from the sidebar to display it here.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($active_document)): ?>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="fa fa-user"></i>
                                        Uploaded by: <?= htmlspecialchars($active_document['emp_fname'] . ' ' . $active_document['emp_lname'], ENT_QUOTES, 'UTF-8') ?>
                                    </small>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small class="text-muted">
                                        <i class="fa fa-calendar"></i>
                                        <?= date('F d, Y \a\t g:i A', strtotime($active_document['upload_date'])) ?>
                                    </small>
                                </div>
                            </div>
                            <?php if (!empty($active_document['description'])): ?>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <?= htmlspecialchars($active_document['description'], ENT_QUOTES, 'UTF-8') ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<?php if (check_function('manage_coc_memo')): ?>
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="<?= base_url('home/coc') ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">
                            <i class="fa fa-upload"></i> Upload Document
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="document_title">Document Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="document_title" name="document_title" required>
                        </div>

                        <div class="form-group">
                            <label for="document_description">Description</label>
                            <textarea class="form-control" id="document_description" name="document_description" rows="3" placeholder="Brief description of the document..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="document_category">Category <span class="text-danger">*</span></label>
                            <select class="form-control" id="document_category" name="document_category" required>
                                <option value="">Select Category</option>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['keyid'] ?>"><?= htmlspecialchars($category['value'], ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Document File <span class="text-danger">*</span></label>
                            <div class="file-upload-wrapper">
                                <input type="file" class="file-upload-input" id="document_file" name="document_file" accept=".pdf,.jpg,.jpeg,.png,.gif" required>
                                <label for="document_file" class="file-upload-label">
                                    <i class="fa fa-cloud-upload-alt fa-2x mb-2"></i>
                                    <br>
                                    <span class="upload-text">Click to select file or drag and drop</span>
                                    <br>
                                    <small class="text-muted">PDF, JPG, JPEG, PNG, GIF (Max: 10MB)</small>
                                </label>
                            </div>
                            <div id="file-info" class="mt-2" style="display: none;">
                                <small class="text-success">
                                    <i class="fa fa-check"></i> <span id="selected-file-name"></span>
                                </small>
                            </div>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1">
                            <label class="form-check-label" for="is_featured">
                                Set as featured document
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-upload"></i> Upload Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    $(function() {
        // File upload handling
        $('#document_file').on('change', function() {
            const file = this.files[0];
            if (file) {
                $('#selected-file-name').text(file.name);
                $('#file-info').show();
                $('.file-upload-label').addClass('file-selected');
                $('.upload-text').text('File selected: ' + file.name);
            } else {
                $('#file-info').hide();
                $('.file-upload-label').removeClass('file-selected');
                $('.upload-text').text('Click to select file or drag and drop');
            }
        });

        // Document search
        $('#documentSearch').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('.document-item').each(function() {
                const title = $(this).data('doc-title').toLowerCase();
                if (title.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    });

    function loadDocument(docId) {
        window.location.href = '<?= base_url("home/coc/") ?>' + docId;
    }

    function confirmDelete(docId) {
        // Use the same jQuery Confirm function for consistency
        deleteDocumentAjax(docId);
    }

    function deleteDocumentAjax(docId) {
        $.confirm({
            title: 'Delete Document',
            content: 'Are you sure you want to delete this document? This action cannot be undone.',
            type: 'red',
            typeAnimated: true,
            icon: 'fa fa-trash',
            theme: 'bootstrap',
            columnClass: 'col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2',
            buttons: {
                deleteDocument: {
                    text: 'Delete',
                    btnClass: 'btn-danger',
                    action: function() {
                        // Show loading state
                        var loadingDialog = $.dialog({
                            title: 'Deleting...',
                            content: 'Please wait while the document is being deleted.',
                            theme: 'bootstrap',
                            columnClass: 'col-md-4 col-md-offset-4',
                            closeIcon: false,
                            backgroundDismiss: false
                        });

                        $.ajax({
                            url: '<?= base_url("home/delete_document") ?>',
                            type: 'POST',
                            data: {
                                document_id: docId
                            },
                            dataType: 'json',
                            success: function(response) {
                                loadingDialog.close();

                                if (response.status === 'success') {
                                    // Show success message
                                    $.alert({
                                        title: 'Success!',
                                        content: response.message,
                                        type: 'green',
                                        typeAnimated: true,
                                        icon: 'fa fa-check',
                                        theme: 'bootstrap',
                                        buttons: {
                                            ok: {
                                                btnClass: 'btn-success'
                                            }
                                        }
                                    });

                                    // Remove the document item from sidebar
                                    $('.document-item[data-doc-id="' + docId + '"]').fadeOut(300, function() {
                                        $(this).remove();
                                    });

                                    // If this was the active document, redirect to main page
                                    if ($('.document-item[data-doc-id="' + docId + '"]').hasClass('active')) {
                                        setTimeout(function() {
                                            window.location.href = '<?= base_url("home/coc") ?>';
                                        }, 1500);
                                    }
                                } else {
                                    $.alert({
                                        title: 'Error!',
                                        content: response.message,
                                        type: 'red',
                                        typeAnimated: true,
                                        icon: 'fa fa-exclamation-triangle',
                                        theme: 'bootstrap',
                                        buttons: {
                                            ok: {
                                                btnClass: 'btn-danger'
                                            }
                                        }
                                    });
                                }
                            },
                            error: function() {
                                loadingDialog.close();

                                $.alert({
                                    title: 'Error!',
                                    content: 'An error occurred while deleting the document. Please try again.',
                                    type: 'red',
                                    typeAnimated: true,
                                    icon: 'fa fa-exclamation-triangle',
                                    theme: 'bootstrap',
                                    buttons: {
                                        ok: {
                                            btnClass: 'btn-danger'
                                        }
                                    }
                                });
                            }
                        });
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-secondary'
                }
            }
        });
    }

    function toggleFeatured(docId, setFeatured) {
        const action = setFeatured ? 'set as featured' : 'remove from featured';
        const confirmTitle = setFeatured ? 'Set as Featured' : 'Remove Featured';
        const confirmContent = setFeatured ?
            'This will set this document as featured and remove featured status from other documents. Continue?' :
            'Are you sure you want to remove the featured status from this document?';

        $.confirm({
            title: confirmTitle,
            content: confirmContent,
            type: setFeatured ? 'blue' : 'orange',
            typeAnimated: true,
            icon: 'fa fa-star',
            theme: 'bootstrap',
            columnClass: 'col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2',
            buttons: {
                confirm: {
                    text: setFeatured ? 'Set Featured' : 'Remove Featured',
                    btnClass: setFeatured ? 'btn-primary' : 'btn-warning',
                    action: function() {
                        $.ajax({
                            url: '<?= base_url("home/toggle_featured") ?>',
                            type: 'POST',
                            data: {
                                document_id: docId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    $.alert({
                                        title: 'Success!',
                                        content: response.message,
                                        type: 'green',
                                        typeAnimated: true,
                                        icon: 'fa fa-check',
                                        theme: 'bootstrap'
                                    });

                                    // Update the button appearance
                                    const button = $('.document-item[data-doc-id="' + docId + '"] .document-feature-btn');
                                    if (response.is_featured) {
                                        button.removeClass('btn-outline-warning').addClass('btn-warning');
                                        button.attr('title', 'Remove from featured');
                                        button.attr('onclick', 'event.stopPropagation(); toggleFeatured(' + docId + ', false)');

                                        // Update badge in the title
                                        const titleDiv = $('.document-item[data-doc-id="' + docId + '"] .font-weight-bold');
                                        if (!titleDiv.find('.badge-warning').length) {
                                            titleDiv.append('<span class="badge badge-warning badge-sm ml-1">Featured</span>');
                                        }

                                        // Remove featured badges from other documents
                                        $('.document-item').not('[data-doc-id="' + docId + '"]').each(function() {
                                            $(this).find('.badge-warning').remove();
                                            $(this).find('.document-feature-btn')
                                                .removeClass('btn-warning')
                                                .addClass('btn-outline-warning')
                                                .attr('title', 'Set as featured');
                                        });
                                    } else {
                                        button.removeClass('btn-warning').addClass('btn-outline-warning');
                                        button.attr('title', 'Set as featured');
                                        button.attr('onclick', 'event.stopPropagation(); toggleFeatured(' + docId + ', true)');

                                        // Remove badge from title
                                        $('.document-item[data-doc-id="' + docId + '"] .badge-warning').remove();
                                    }
                                } else {
                                    $.alert({
                                        title: 'Error!',
                                        content: response.message,
                                        type: 'red',
                                        typeAnimated: true,
                                        icon: 'fa fa-exclamation-triangle',
                                        theme: 'bootstrap'
                                    });
                                }
                            },
                            error: function() {
                                $.alert({
                                    title: 'Error!',
                                    content: 'An error occurred while updating the document status.',
                                    type: 'red',
                                    typeAnimated: true,
                                    icon: 'fa fa-exclamation-triangle',
                                    theme: 'bootstrap'
                                });
                            }
                        });
                    }
                },
                cancel: {
                    text: 'Cancel',
                    btnClass: 'btn-secondary'
                }
            }
        });
    }

    function editDocument(docId) {
        // Show loading dialog while fetching document data
        var loadingDialog = $.dialog({
            title: 'Loading...',
            content: 'Please wait while loading document information.',
            theme: 'bootstrap',
            columnClass: 'col-md-4 col-md-offset-4',
            closeIcon: false,
            backgroundDismiss: false
        });

        // Fetch document data first
        $.ajax({
            url: '<?= base_url("home/get_document_data") ?>',
            type: 'POST',
            data: {
                document_id: docId
            },
            dataType: 'json',
            success: function(response) {
                loadingDialog.close();

                if (response.status === 'success') {
                    const docData = response.data;

                    $.confirm({
                        title: 'Edit Document Information',
                        content: `
                            <form class="form">
                                <div class="form-group">
                                    <label>Document Title:</label>
                                    <input type="text" id="edit-title" class="form-control" value="${docData.title}" required>
                                </div>
                                <div class="form-group">
                                    <label>Description:</label>
                                    <textarea id="edit-description" class="form-control" rows="3" placeholder="Enter document description...">${docData.description}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Category:</label>
                                    <select id="edit-category" class="form-control">
                                        <?php if (!empty($categories)): ?>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category['keyid'] ?>"><?= htmlspecialchars($category['value'], ENT_QUOTES, 'UTF-8') ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </form>
                        `,
                        type: 'blue',
                        typeAnimated: true,
                        icon: 'fa fa-edit',
                        theme: 'bootstrap',
                        columnClass: 'col-md-8 col-md-offset-2',
                        onContentReady: function() {
                            // Set the selected category after the modal is ready
                            $('#edit-category').val(docData.category);
                        },
                        buttons: {
                            saveChanges: {
                                text: 'Save Changes',
                                btnClass: 'btn-primary',
                                action: function() {
                                    const title = $('#edit-title').val().trim();
                                    const description = $('#edit-description').val().trim();
                                    const category = $('#edit-category').val();

                                    if (!title) {
                                        $.alert({
                                            title: 'Error!',
                                            content: 'Document title is required.',
                                            type: 'red',
                                            theme: 'bootstrap'
                                        });
                                        return false;
                                    }

                                    $.ajax({
                                        url: '<?= base_url("home/update_document_info") ?>',
                                        type: 'POST',
                                        data: {
                                            document_id: docId,
                                            title: title,
                                            description: description,
                                            category: category
                                        },
                                        dataType: 'json',
                                        success: function(response) {
                                            if (response.status === 'success') {
                                                $.alert({
                                                    title: 'Success!',
                                                    content: response.message,
                                                    type: 'green',
                                                    typeAnimated: true,
                                                    icon: 'fa fa-check',
                                                    theme: 'bootstrap',
                                                    buttons: {
                                                        ok: {
                                                            action: function() {
                                                                // Reload the page to show updated information
                                                                window.location.reload();
                                                            }
                                                        }
                                                    }
                                                });
                                            } else {
                                                $.alert({
                                                    title: 'Error!',
                                                    content: response.message,
                                                    type: 'red',
                                                    typeAnimated: true,
                                                    icon: 'fa fa-exclamation-triangle',
                                                    theme: 'bootstrap'
                                                });
                                            }
                                        },
                                        error: function() {
                                            $.alert({
                                                title: 'Error!',
                                                content: 'An error occurred while updating the document information.',
                                                type: 'red',
                                                typeAnimated: true,
                                                icon: 'fa fa-exclamation-triangle',
                                                theme: 'bootstrap'
                                            });
                                        }
                                    });
                                }
                            },
                            cancel: {
                                text: 'Cancel',
                                btnClass: 'btn-secondary'
                            }
                        }
                    });
                } else {
                    $.alert({
                        title: 'Error!',
                        content: response.message,
                        type: 'red',
                        typeAnimated: true,
                        icon: 'fa fa-exclamation-triangle',
                        theme: 'bootstrap'
                    });
                }
            },
            error: function() {
                loadingDialog.close();
                $.alert({
                    title: 'Error!',
                    content: 'An error occurred while loading document information.',
                    type: 'red',
                    typeAnimated: true,
                    icon: 'fa fa-exclamation-triangle',
                    theme: 'bootstrap'
                });
            }
        });
    }

    // Drag and drop functionality
    $(document).ready(function() {
        const uploadLabel = $('.file-upload-label');

        uploadLabel.on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('file-selected');
        });

        uploadLabel.on('dragleave', function(e) {
            e.preventDefault();
            if (!$('#document_file')[0].files.length) {
                $(this).removeClass('file-selected');
            }
        });

        uploadLabel.on('drop', function(e) {
            e.preventDefault();
            const files = e.originalEvent.dataTransfer.files;
            if (files.length) {
                $('#document_file')[0].files = files;
                $('#document_file').trigger('change');
            }
        });
    });
</script>