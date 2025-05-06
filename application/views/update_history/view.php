<div class="container-fluid">
  <h2>Update History</h2>
  
  <!-- Button to add an update -->
  <div class="mb-3">
    <a href="<?= base_url('update_history/add_update'); ?>" class="btn btn-primary">Add Update</a>
  </div>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Version</th>
        <th>Date</th>
        <th>Changes</th>
        <th>Correction</th>
        <th>Author</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($updates as $update): ?>
        <tr>
          <td><?= $update->version; ?></td>
          <td><?= $update->update_date; ?></td>
          <td>
            <!-- Make sure the changes text fits inside and wraps properly -->
            <div class="text-wrap" style="max-width: 300px; white-space: normal; word-wrap: break-word;" 
                 title="<?= htmlspecialchars($update->changes); ?>">
              <?= nl2br(htmlspecialchars($update->changes)); ?>
            </div>
          </td>
          <td><?= $update->koreksi ? $update->koreksi : '-'; ?></td>
          <td><?= $update->author; ?></td>
          <td>
            <?php if (!$update->koreksi): ?>
              <a href="<?= base_url('update_history/add_correction/'.$update->id); ?>" class="btn btn-warning btn-sm">Add Correction</a>
            <?php else: ?>
              <span class="text-muted">Correction Added</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
