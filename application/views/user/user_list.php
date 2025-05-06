<div class="container-fluid">
  <h2>User Management</h2>

  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $this->session->flashdata('success'); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <a href="<?= base_url('user/create'); ?>" class="btn btn-primary mb-3">Add New User</a>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?= $user->id; ?></td>
          <td><?= $user->username; ?></td>
          <td><?= $user->role_name; ?></td>
          <td><?= $user->created_at; ?></td>
          <td>
            <a href="<?= base_url('user/edit/' . $user->id); ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="<?= base_url('user/delete/' . $user->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
