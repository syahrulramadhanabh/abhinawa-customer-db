<div class="container-fluid">
    <h2>Search</h2>
    <form method="get" action="<?= base_url('search'); ?>" class="mb-4">
        <div class="input-group">
            <!-- Search input field -->
            <input 
                type="text" 
                name="user" 
                value="<?= isset($user) ? html_escape($user) : ''; ?>" 
                class="form-control" 
                placeholder="What do you want to search for?"
                aria-label="Search"
                autofocus
            >
            <!-- Category selection -->
            <select name="category" class="form-control">
                <option value="customer_group" <?= (isset($search_category) && $search_category == 'customer_group') ? 'selected' : ''; ?>>Customer Groups</option>
                <option value="customer" <?= (isset($search_category) && $search_category == 'customer') ? 'selected' : ''; ?>>Customers</option>
                <option value="supplier" <?= (isset($search_category) && $search_category == 'supplier') ? 'selected' : ''; ?>>Suppliers</option>
                <option value="service_type" <?= (isset($search_category) && $search_category == 'service_type') ? 'selected' : ''; ?>>Service Types</option>
                <option value="supplier_detail" <?= (isset($search_category) && $search_category == 'supplier_detail') ? 'selected' : ''; ?>>Supplier Detail</option>
            </select>
            <!-- Submit button -->
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>
</div>
