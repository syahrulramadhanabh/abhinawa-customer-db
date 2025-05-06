<div class="container-fluid">
    <h2>Customer Groups</h2>

    <!-- Search Form -->
    <form method="get" action="<?= base_url('customer/index'); ?>" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" value="<?= isset($search) ? $search : ''; ?>" class="form-control" placeholder="Search customer groups...">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <!-- Display Customer Groups -->
    <?php if (!empty($customer_groups)): ?>
        <?php foreach ($customer_groups as $group): ?>
            <div class="card my-3">
                <div class="card-body">
                    <h5 class="card-title"><?= $group->group_name; ?></h5>
                    <p><?= $group->description; ?></p>
                    <a href="<?= base_url('customer/group_details/' . $group->id); ?>" class="btn btn-primary">View Customers</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No customer groups found.</p>
    <?php endif; ?>

    <!-- Pagination Links -->
    <?= $pagination; ?>
</div>
