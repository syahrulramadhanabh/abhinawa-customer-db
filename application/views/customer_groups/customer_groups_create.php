<div class="container-fluid">
  <h2>Add New Customer Group</h2>

  <form action="<?= base_url('customer_groups/store'); ?>" method="post">
    <div class="mb-3">
      <label for="group_name" class="form-label">Group Name</label>
      <input type="text" class="form-control" name="group_name" required>
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea class="form-control" name="description" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Add Customer Group</button>
    <a href="<?= base_url('customer_groups'); ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
