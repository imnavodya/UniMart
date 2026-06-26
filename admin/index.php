<?php require_once __DIR__ . '/includes/header.php'; ?>

<?php
$totalProducts  = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders    = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalCustomers = $conn->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn();
$totalRevenue   = $conn->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();

function getPercentChange($conn, $table, $dateColumn = 'created_at', $sumColumn = null, $where = '') {
    $whereClause = $where ? "AND $where" : "";
    $agg = $sumColumn ? "COALESCE(SUM($sumColumn), 0)" : "COUNT(*)";
    $curr = (float)$conn->query("SELECT $agg FROM $table WHERE $dateColumn >= DATE_SUB(NOW(), INTERVAL 7 DAY) $whereClause")->fetchColumn();
    $prev = (float)$conn->query("SELECT $agg FROM $table WHERE $dateColumn >= DATE_SUB(NOW(), INTERVAL 14 DAY) AND $dateColumn < DATE_SUB(NOW(), INTERVAL 7 DAY) $whereClause")->fetchColumn();
    
    if ($prev == 0) {
        if ($curr == 0) return ['value' => '0%', 'up' => null];
        return ['value' => '+100%', 'up' => true];
    }
    
    $change = (($curr - $prev) / $prev) * 100;
    $up = $change > 0 ? true : ($change < 0 ? false : null);
    return ['value' => ($change > 0 ? '+' : '') . number_format($change, 1) . '%', 'up' => $up];
}

$prodChange = getPercentChange($conn, 'products');
$orderChange = getPercentChange($conn, 'orders');
$custChange = getPercentChange($conn, 'users', 'created_at', null, "role='customer'");
$revChange = getPercentChange($conn, 'orders', 'created_at', 'total_amount', "status != 'cancelled'");
$recentOrders = $conn->query("
    SELECT o.*, u.name as customer_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC LIMIT 5
")->fetchAll();

$sparkProducts = [];
$sparkOrders = [];
$sparkCustomers = [];
$sparkRevenue = [];
$chartLabels = [];
$chartData = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chartLabels[] = date('d M', strtotime("-$i days"));
    
    $p = $conn->prepare("SELECT COUNT(*) FROM products WHERE DATE(created_at) = ?"); $p->execute([$date]);
    $sparkProducts[] = (int)$p->fetchColumn();
    
    $o = $conn->prepare("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = ?"); $o->execute([$date]);
    $sparkOrders[] = (int)$o->fetchColumn();
    
    $c = $conn->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) = ? AND role='customer'"); $c->execute([$date]);
    $sparkCustomers[] = (int)$c->fetchColumn();
    
    $r = $conn->prepare("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE DATE(created_at) = ? AND status != 'cancelled'"); $r->execute([$date]);
    $rev = (float)$r->fetchColumn();
    $sparkRevenue[] = $rev;
    $chartData[] = $rev;
}

$catDist = $conn->query("
    SELECT c.name, COALESCE(SUM(oi.quantity), 0) as count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    LEFT JOIN order_items oi ON oi.product_id = p.id
    LEFT JOIN orders o ON o.id = oi.order_id AND o.status = 'completed'
    GROUP BY c.id
    ORDER BY count DESC
    LIMIT 6
")->fetchAll();
?>

<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['icon'=>'fas fa-box-open',     'color'=>'108,99,255',  'label'=>'Total Products',  'value'=> number_format($totalProducts),   'change'=>$prodChange['value'], 'up'=>$prodChange['up']],
        ['icon'=>'fas fa-shopping-bag', 'color'=>'0,212,255',   'label'=>'Total Orders',    'value'=> number_format($totalOrders),     'change'=>$orderChange['value'], 'up'=>$orderChange['up']],
        ['icon'=>'fas fa-users',        'color'=>'34,197,94',   'label'=>'Total Customers', 'value'=> number_format($totalCustomers),  'change'=>$custChange['value'], 'up'=>$custChange['up']],
        ['icon'=>'fas fa-coins',        'color'=>'139,92,246',  'label'=>'Total Revenue',   'value'=>'LKR '.number_format($totalRevenue,2), 'change'=>$revChange['value'], 'up'=>$revChange['up']],
    ];
    foreach ($cards as $i => $c):
    ?>
    <div class="col-6 col-xl-3">
        <div class="adm-stat-card position-relative overflow-hidden p-0">
            <div class="p-4 position-relative" style="z-index: 2;">
                <div class="adm-stat-icon" style="background:rgba(<?=$c['color']?>,0.12);color:rgb(<?=$c['color']?>);">
                    <i class="<?=$c['icon']?>"></i>
                </div>
                <div class="adm-stat-label"><?=$c['label']?></div>
                <div class="adm-stat-value mt-1"><?=$c['value']?></div>
                <div class="adm-stat-change <?= $c['up'] === true ? 'up' : ($c['up'] === false ? 'down' : 'text-muted') ?>">
                    <?php if ($c['up'] === true): ?>
                        <i class="fas fa-arrow-up me-1"></i>
                    <?php elseif ($c['up'] === false): ?>
                        <i class="fas fa-arrow-down me-1"></i>
                    <?php else: ?>
                        <i class="fas fa-minus me-1"></i>
                    <?php endif; ?>
                    <?= $c['change'] ?>
                    <span style="color:var(--text-muted);font-weight:normal;font-size:0.65rem;margin-left:4px;">vs last 7 days</span>
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 w-100" style="height: 50px; z-index: 1;">
                <canvas id="sparkline-<?= $i ?>" height="50"></canvas>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="adm-panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="adm-panel-title mb-0">Sales Overview</div>
                <span class="adm-badge adm-badge-success">Last 7 days</span>
            </div>
            <canvas id="salesChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="adm-panel h-100">
            <div class="adm-panel-title">Top Categories</div>
            <canvas id="catChart" height="160"></canvas>
        </div>
    </div>
</div>

<div class="adm-panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="adm-panel-title mb-0">Recent Orders</div>
        <a href="/UniMart/admin/orders.php" class="adm-btn adm-btn-ghost" style="font-size:0.78rem;padding:6px 14px;">View All</a>
    </div>
    <div class="table-responsive">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:30px;">No orders yet</td></tr>
                <?php else: foreach ($recentOrders as $order): ?>
                <tr>
                    <td style="color:var(--text-main);font-weight:600;">#UM-<?= str_pad($order['id'],5,'0',STR_PAD_LEFT) ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td style="color:var(--accent);font-weight:600;"><?= formatPrice($order['total_amount']) ?></td>
                    <td>
                        <?php if ($order['status'] === 'completed'): ?>
                            <span class="adm-badge adm-badge-success"><i class="fas fa-check-circle"></i> Completed</span>
                        <?php elseif ($order['status'] === 'pending'): ?>
                            <span class="adm-badge adm-badge-warning"><i class="fas fa-clock"></i> Pending</span>
                        <?php else: ?>
                            <span class="adm-badge adm-badge-danger"><i class="fas fa-times-circle"></i> Cancelled</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            label: 'Revenue ($)',
            data: <?= json_encode($chartData) ?>,
            borderColor: '#006A38',
            backgroundColor: function(ctx) {
                const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 200);
                g.addColorStop(0, 'rgba(0,106,56,0.35)');
                g.addColorStop(1, 'rgba(0,106,56,0)');
                return g;
            },
            borderWidth: 2.5,
            pointRadius: 4,
            pointBackgroundColor: '#006A38',
            fill: true,
            tension: 0.45,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'var(--glass-border)' }, ticks: { color: 'var(--text-muted)', font: { size: 11 } } },
            y: { grid: { color: 'var(--glass-border)' }, ticks: { color: 'var(--text-muted)', font: { size: 11 } } }
        }
    }
});

const catLabels = <?= json_encode(array_column($catDist, 'name')) ?>;
const catCounts = <?= json_encode(array_column($catDist, 'count')) ?>;
const catColors = ['#6C63FF','#00D4FF','#FF5BF1','#22C55E','#F59E0B','#8B5CF6'];
new Chart(document.getElementById('catChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: catLabels,
        datasets: [{ data: catCounts, backgroundColor: catColors, borderWidth: 0, hoverOffset: 6 }]
    },
    options: {
        cutout: '68%',
        plugins: { legend: { display: false } },
        responsive: true,
    }
});

const sparklines = [
    <?= json_encode($sparkProducts) ?>, 
    <?= json_encode($sparkOrders) ?>,  
    <?= json_encode($sparkCustomers) ?>, 
    <?= json_encode($sparkRevenue) ?> 
];
const cardColors = ['#6C63FF', '#00D4FF', '#22C55E', '#8B5CF6'];

sparklines.forEach((data, index) => {
    const ctx = document.getElementById('sparkline-' + index).getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['1','2','3','4','5','6','7'],
            datasets: [{
                data: data,
                borderColor: cardColors[index],
                borderWidth: 2,
                tension: 0.4,
                pointRadius: 0,
                fill: true,
                backgroundColor: function(context) {
                    const g = context.chart.ctx.createLinearGradient(0, 0, 0, 50);
                    g.addColorStop(0, cardColors[index] + '33');
                    g.addColorStop(1, cardColors[index] + '00'); 
                    return g;
                }
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: {
                x: { display: false },
                y: { display: false, min: 0 }
            },
            layout: { padding: 0 }
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
