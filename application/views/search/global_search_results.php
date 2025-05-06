<div class="container-fluid">
    <?php if (!empty($user)): ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Search Results for: <span class="text-primary"><?= html_escape($user) ?></span></h2>
            <span class="badge bg-primary"><?= count($suppliers) + count($customers) + count($customer_groups) + count($service_types) ?> results</span>
        </div>
    <?php endif; ?>

    <?php if (isset($error) && !empty($error)): ?>
        <div class="alert alert-danger">
            <?= html_escape($error) ?>
        </div>
    <?php endif; ?>

    <div class="row" id="searchResults">
        <?php
        // Define which sections to show based on category
        $sections = [];
        if ($search_category === 'all' || $search_category === 'supplier') {
            $sections['suppliers'] = ['title' => 'Suppliers', 'data' => $suppliers];
            $sections['supplier_detail'] = ['title' => 'Supplier Details', 'data' => $supplier_detail];
        }
        if ($search_category === 'all' || $search_category === 'customer_group') {
            $sections['customer_groups'] = ['title' => 'Customer Groups', 'data' => $customer_groups];
        }
        if ($search_category === 'all' || $search_category === 'customer') {
            $sections['customers'] = ['title' => 'Customers', 'data' => $customers];
        }
        if ($search_category === 'all' || $search_category === 'service_type') {
            $sections['service_types'] = ['title' => 'Service Types', 'data' => $service_types];
        }

        foreach ($sections as $key => $section):
        ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-light">
                        <h3 class="card-title h5 mb-0">
                            <?= $section['title'] ?>
                            <span class="badge bg-secondary float-end">
                                <?= count($section['data']) ?>
                            </span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($section['data'])): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($section['data'] as $item): ?>
                                    <div class="list-group-item">
                                        <?php switch($key):
                                            case 'suppliers': ?>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong><?= html_escape($item->nama_supplier) ?></strong>
                                                    <span class="text-muted">Code: <?= html_escape($item->kdsupplier) ?></span>
                                                </div>
                                                <?php break;
                                            
                                            case 'supplier_detail': ?>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>ID: <?= html_escape($item->cid_supplier) ?></div>
                                                    <span class="badge bg-info"><?= html_escape($item->service_type_supplier) ?></span>
                                                </div>
                                                <?php break;
                                            
                                            case 'customer_groups': ?>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong><?= html_escape($item->group_name) ?></strong>
                                                    <span class="text-muted">ID: <?= html_escape($item->id) ?></span>
                                                </div>
                                                <?php break;
                                            
                                            case 'customers': ?>
                                                <div>
                                                    <strong><?= html_escape($item->customer) ?></strong>
                                                    <div class="small text-muted">
                                                        Group ID: <?= html_escape($item->customer) ?> | 
                                                        Supplier: <?= html_escape($item->kdsupplier) ?>
                                                    </div>
                                                </div>
                                                <?php break;
                                            
                                            case 'service_types': ?>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-cog me-2"></i>
                                                    <?= html_escape($item->service_type) ?>
                                                </div>
                                                <?php break;
                                        endswitch; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No <?= $section['title'] ?> found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($user)): ?>
        <div class="text-center text-muted mt-4">
            <i class="fas fa-search fa-3x mb-3"></i>
            <p>Enter a search term to see results</p>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Function to update specific section
    function updateSection(sectionKey, data) {
    const section = $(`#${sectionKey}`);
    if (!section.length) return;

    const listGroup = section.find('.list-group');
    listGroup.empty();

    if (data && data.length) {
        data.forEach(item => {
            let html = '';
            switch (sectionKey) {
                case 'suppliers':
                    html = `<div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>${escapeHtml(item.nama_supplier || 'N/A')}</strong>
                            <span class="text-muted">Code: ${escapeHtml(item.kdsupplier || 'N/A')}</span>
                        </div>
                    </div>`;
                    break;
                case 'supplier_detail':
                    html = `<div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>ID: ${escapeHtml(item.cid_supplier || 'N/A')}</div>
                            <span class="badge bg-info">${escapeHtml(item.service_type_supplier || 'N/A')}</span>
                        </div>
                    </div>`;
                    break;
                case 'customer_groups':
                    html = `<div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>${escapeHtml(item.group_name || 'N/A')}</strong>
                            <span class="text-muted">ID: ${escapeHtml(item.id || 'N/A')}</span>
                        </div>
                    </div>`;
                    break;
                case 'customers':
                    html = `<div class="list-group-item">
                        <strong>${escapeHtml(item.customer || 'N/A')}</strong>
                        <div class="small text-muted">
                            Group ID: ${escapeHtml(item.customer || 'N/A')} | 
                            Supplier: ${escapeHtml(item.kdsupplier || 'N/A')}
                        </div>
                    </div>`;
                    break;
                case 'service_types':
                    html = `<div class="list-group-item d-flex align-items-center">
                        <i class="fas fa-cog me-2"></i>
                        ${escapeHtml(item.service_type || 'N/A')}
                    </div>`;
                    break;
            }
            listGroup.append(html);
        });
    } else {
        listGroup.html('<p class="text-muted mb-0">No results found.</p>');
    }
}


    // Helper function to escape HTML
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>