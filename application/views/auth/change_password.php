<div class="container-fluid">
  <h2>Change Password</h2>

  <!-- Display any success or error messages -->
  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $this->session->flashdata('success'); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php elseif ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= $this->session->flashdata('error'); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Change Password Form -->
  <form action="<?= base_url('auth/update_password'); ?>" method="post">
    <div class="mb-3">
      <label for="old_password" class="form-label">Old Password</label>
      <input type="password" class="form-control" name="old_password" required>
    </div>
    <div class="mb-3">
      <label for="new_password" class="form-label">New Password</label>
      <input type="password" class="form-control" name="new_password" required>
    </div>
    <div class="mb-3">
      <label for="confirm_password" class="form-label">Confirm New Password</label>
      <input type="password" class="form-control" name="confirm_password" required>
    </div>
    <button type="submit" class="btn btn-primary">Change Password</button>
    <a href="<?= base_url('admin'); ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
