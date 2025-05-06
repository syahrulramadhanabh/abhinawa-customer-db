<div class="container-fluid">
  <h2>Add New User</h2>

  <form action="<?= base_url('user/store'); ?>" method="post">
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" class="form-control" name="username" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" name="password" required>
    </div>
    <div class="mb-3">
      <label for="role_id" class="form-label">Role</label>
      <select class="form-select" name="role_id" required>
        <option value="" disabled selected>Select Role</option>
        <?php foreach ($roles as $role): ?>
          <option value="<?= $role->id; ?>"><?= $role->role_name; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Add User</button>
    <a href="<?= base_url('user'); ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
