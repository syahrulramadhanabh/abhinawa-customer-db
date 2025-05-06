<div class="container-fluid">
  <h2>Edit User</h2>

  <form action="<?= base_url('user/update/' . $user->id); ?>" method="post">
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" class="form-control" name="username" value="<?= $user->username; ?>" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password (leave blank to keep current password)</label>
      <input type="password" class="form-control" name="password">
    </div>
    <button type="submit" class="btn btn-primary">Update User</button>
    <a href="<?= base_url('user'); ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
