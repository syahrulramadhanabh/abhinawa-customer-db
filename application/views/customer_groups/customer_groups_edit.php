<div class="container-fluid">
  <h2>Edit Customer Group</h2>

  <form action="<?= base_url('customer_groups/update/' . $customer_group->id); ?>" method="post">
    <div class="mb-3">
      <label for="group_name" class="form-label">Group Name</label>
      <input type="text" class="form-control" name="group_name" value="<?= $customer_group->group_name; ?>" required>
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea class="form-control" name="description" rows="3"><?= $customer_group->description; ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update Customer Group</button>
    <a href="<?= base_url('customer_groups'); ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>
