<div class="container-fluid">
  <h2>Customer Groups List</h2>

  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= $this->session->flashdata('success'); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <a href="<?= base_url('customer_groups/create'); ?>" class="btn btn-primary mb-3">Add New Customer Group</a>

                    <table class="table table-hover align-middle text-center">
                        <thead class="bg-primary text-white">
      <tr>
        <th>ID</th>
        <th>Group Name</th>
        <th>Description</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($customer_groups as $group): ?>
        <tr>
          <td><?= $group->id; ?></td>
          <td><?= $group->group_name; ?></td>
          <td><?= $group->description; ?></td>
          <td>
            <a href="<?= base_url('customer_groups/edit/' . $group->id); ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="<?= base_url('customer_groups/delete/' . $group->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this customer group?');">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
