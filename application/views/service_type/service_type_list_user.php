<div class="container-fluid">
  <h2>Service Types</h2>
  <br>
    <table class="table table-hover align-middle text-center">
        <thead class="bg-primary text-white">
      <tr>
        <th>ID</th>
        <th>Service Name</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($service_types as $service_type): ?>
        <tr>
          <td><?= $service_type->id; ?></td>
          <td><?= $service_type->service_name; ?></td>
          <td><?= $service_type->description; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
