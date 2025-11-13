<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="<?= base_url() ?>" class="brand-link">
    <img src="<?= base_url('dist/img/logo-02.png') ?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light"><strong><?= COMPANY_NAME ?></strong></span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?= base_url('dist/img/user2-160x160.jpg') ?>" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="<?= base_url('employee/profile') ?>" class="d-block"><?= $this->_name ?></a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <?php
        $mainTabs = tab__groups(1);
        ?>


        <?php if (!empty($mainTabs)) : ?>
          <?php foreach ($mainTabs as $key => $tab) : ?>
            <li class="nav-item">
              <a href="<?= !empty($tab['link']) ? base_url($tab['link']) : 'javascript:;' ?>" class="nav-link">
                <?= $tab['icon'] ? '<i class="nav-icon ' . $tab['icon'] . '"></i>' : '<i class="nav-icon fas fa-th"></i>' ?>

                <p>
                  <?= $tab['name'] ?> <?php if (!empty($tab['badge'])) : ?>
                    <span class="right badge badge-danger">New</span>
                  <?php endif; ?>
                </p>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else : ?>
          <li class="nav-header italic">No Tabs Found...</li>
        <?php endif; ?>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>


  <div class="brand-link" style="position: absolute; bottom: 0; left: 0; width: 100%;">
    <a href="javascript:;" class="btn btn-primary btn-block" id="timeclock_open-btn">Punch IN/OUT</a>
  </div>
  <!-- /.sidebar -->
</aside>