<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="material-icons-outlined">analytics</i> Analytics Dashboard</h1>
                <span>Advanced metrics, predictions & insights</span>
            </div>
            <div>
                <button class="btn btn-sm btn-outline-primary" onclick="refreshKPIs()">
                    <i class="material-icons-outlined">refresh</i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Key Performance Indicators -->
<div class="row">
    <div class="col-md-12 mb-3">
        <h5><i class="material-icons-outlined">speed</i> Key Performance Indicators (KPIs)</h5>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">groups</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Active Rate</span>
                        <span class="widget-stats-amount"><?= number_format($kpis['active_rate'], 1) ?>%</span>
                        <span class="widget-stats-info"><?= number_format($kpis['active_members']) ?> / <?= number_format($kpis['total_members']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-<?= $kpis['member_growth_rate'] >= 0 ? 'success' : 'danger' ?>">
                        <i class="material-icons-outlined">trending_<?= $kpis['member_growth_rate'] >= 0 ? 'up' : 'down' ?></i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Member Growth (MoM)</span>
                        <span class="widget-stats-amount"><?= number_format($kpis['member_growth_rate'], 1) ?>%</span>
                        <span class="widget-stats-info"><?= number_format($kpis['new_this_month']) ?> new members</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-info">
                        <i class="material-icons-outlined">payments</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">ARPU (Avg Revenue/User)</span>
                        <span class="widget-stats-amount">Rp <?= number_format($kpis['arpu'], 0, ',', '.') ?></span>
                        <span class="widget-stats-info">This month</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-warning">
                        <i class="material-icons-outlined">show_chart</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Collection Rate</span>
                        <span class="widget-stats-amount"><?= number_format($kpis['collection_rate'], 1) ?>%</span>
                        <span class="widget-stats-info">Current month</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary KPIs -->
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-<?= $kpis['revenue_growth_rate'] >= 0 ? 'success' : 'danger' ?>">
                        <i class="material-icons-outlined">account_balance</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Revenue Growth (MoM)</span>
                        <span class="widget-stats-amount"><?= number_format($kpis['revenue_growth_rate'], 1) ?>%</span>
                        <span class="widget-stats-info">Rp <?= number_format($kpis['revenue_this_month'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">savings</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Revenue YTD</span>
                        <span class="widget-stats-amount">Rp <?= number_format($kpis['revenue_ytd'] / 1000000, 1) ?>M</span>
                        <span class="widget-stats-info">Year to date</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-<?= $kpis['churn_rate'] < 5 ? 'success' : 'danger' ?>">
                        <i class="material-icons-outlined">person_remove</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Churn Rate</span>
                        <span class="widget-stats-amount"><?= number_format($kpis['churn_rate'], 2) ?>%</span>
                        <span class="widget-stats-info">This month</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-success">
                        <i class="material-icons-outlined">auto_awesome</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Conversion Rate</span>
                        <span class="widget-stats-amount"><?= number_format($kpis['conversion_rate'], 1) ?>%</span>
                        <span class="widget-stats-info">Candidate → Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Period Comparisons -->
<div class="row mt-3">
    <div class="col-md-12 mb-3">
        <h5><i class="material-icons-outlined">compare_arrows</i> Period-over-Period Comparisons</h5>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Month-over-Month (MoM)</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Metric</th>
                        <th class="text-end">Current</th>
                        <th class="text-end">Last Month</th>
                        <th class="text-end">Change</th>
                    </tr>
                    <tr>
                        <td><strong>New Members</strong></td>
                        <td class="text-end"><?= number_format($comparisons['mom']['members_current']) ?></td>
                        <td class="text-end"><?= number_format($comparisons['mom']['members_last']) ?></td>
                        <td class="text-end">
                            <span class="badge badge-<?= $comparisons['mom']['members_change'] >= 0 ? 'success' : 'danger' ?>">
                                <?= $comparisons['mom']['members_change'] >= 0 ? '+' : '' ?><?= number_format($comparisons['mom']['members_change'], 1) ?>%
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Revenue</strong></td>
                        <td class="text-end">Rp <?= number_format($comparisons['mom']['revenue_current'], 0, ',', '.') ?></td>
                        <td class="text-end">Rp <?= number_format($comparisons['mom']['revenue_last'], 0, ',', '.') ?></td>
                        <td class="text-end">
                            <span class="badge badge-<?= $comparisons['mom']['revenue_change'] >= 0 ? 'success' : 'danger' ?>">
                                <?= $comparisons['mom']['revenue_change'] >= 0 ? '+' : '' ?><?= number_format($comparisons['mom']['revenue_change'], 1) ?>%
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Year-over-Year (YoY)</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Metric</th>
                        <th class="text-end">This Year</th>
                        <th class="text-end">Last Year</th>
                        <th class="text-end">Change</th>
                    </tr>
                    <tr>
                        <td><strong>New Members</strong></td>
                        <td class="text-end"><?= number_format($comparisons['yoy']['members_current']) ?></td>
                        <td class="text-end"><?= number_format($comparisons['yoy']['members_last']) ?></td>
                        <td class="text-end">
                            <span class="badge badge-<?= $comparisons['yoy']['members_change'] >= 0 ? 'success' : 'danger' ?>">
                                <?= $comparisons['yoy']['members_change'] >= 0 ? '+' : '' ?><?= number_format($comparisons['yoy']['members_change'], 1) ?>%
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Revenue</strong></td>
                        <td class="text-end">Rp <?= number_format($comparisons['yoy']['revenue_current'], 0, ',', '.') ?></td>
                        <td class="text-end">Rp <?= number_format($comparisons['yoy']['revenue_last'], 0, ',', '.') ?></td>
                        <td class="text-end">
                            <span class="badge badge-<?= $comparisons['yoy']['revenue_change'] >= 0 ? 'success' : 'danger' ?>">
                                <?= $comparisons['yoy']['revenue_change'] >= 0 ? '+' : '' ?><?= number_format($comparisons['yoy']['revenue_change'], 1) ?>%
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Trends & Predictions -->
<div class="row mt-3">
    <div class="col-md-12 mb-3">
        <h5><i class="material-icons-outlined">timeline</i> Trends & Predictions</h5>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Member Growth Trend (12 Months)</h5>
            </div>
            <div class="card-body">
                <div id="memberTrendChart"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Revenue Trend (12 Months)</h5>
            </div>
            <div class="card-body">
                <div id="revenueTrendChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- Predictions -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">query_stats</i>
                    Growth Predictions (Next 3 Months)
                    <span class="badge badge-info ms-2">Confidence: <?= ucfirst($predictions['confidence']) ?></span>
                    <span class="badge badge-<?= $predictions['growth_trend'] === 'increasing' ? 'success' : ($predictions['growth_trend'] === 'decreasing' ? 'danger' : 'warning') ?> ms-2">
                        Trend: <?= ucfirst($predictions['growth_trend']) ?>
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Predicted New Members</th>
                                <th class="text-end">Predicted Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($predictions['member_predictions'] as $pred): ?>
                                <tr>
                                    <td><strong><?= esc($pred['month']) ?></strong></td>
                                    <td class="text-end"><?= number_format($pred['predicted_new_members']) ?></td>
                                    <td class="text-end text-success">
                                        Rp <?= number_format($pred['predicted_revenue'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Retention Metrics -->
<div class="row mt-3">
    <div class="col-md-12 mb-3">
        <h5><i class="material-icons-outlined">loyalty</i> Retention & Lifetime Value</h5>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="material-icons-outlined" style="font-size: 48px; color: #66bb6a;">stars</i>
                <h3 class="mt-3">Rp <?= number_format($retention['ltv'], 0, ',', '.') ?></h3>
                <p class="text-muted">Lifetime Value (LTV)</p>
                <small class="text-muted">Avg: <?= $retention['avg_member_lifetime'] ?> months</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="material-icons-outlined" style="font-size: 48px; color: #42a5f5;">verified</i>
                <h3 class="mt-3"><?= number_format($retention['payment_consistency'], 1) ?>%</h3>
                <p class="text-muted">Payment Consistency</p>
                <small class="text-muted">Members who pay regularly</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Cohort Retention (6 Months)</h5>
            </div>
            <div class="card-body">
                <div id="retentionChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="row mt-3">
    <div class="col-md-12 mb-3">
        <h5><i class="material-icons-outlined">leaderboard</i> Regional Performance</h5>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-success">
                    <i class="material-icons-outlined">emoji_events</i>
                    Top Performing Regions
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Region</th>
                                <th class="text-end">Members</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($performance['top_regions'] as $region): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($region['region_code']) ?></strong> -
                                        <?= esc($region['province_name'] ?? 'Unknown') ?>
                                    </td>
                                    <td class="text-end"><?= number_format($region['member_count']) ?></td>
                                    <td class="text-end text-success">
                                        <strong>Rp <?= number_format($region['total_revenue'] ?? 0, 0, ',', '.') ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-danger">
                    <i class="material-icons-outlined">warning</i>
                    Underperforming Regions
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($performance['underperforming_regions'])): ?>
                    <div class="alert alert-success">
                        <i class="material-icons-outlined">check_circle</i>
                        All regions performing well!
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Region</th>
                                    <th class="text-end">Members</th>
                                    <th class="text-end">Collection Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($performance['underperforming_regions'] as $region): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($region['region_code']) ?></strong> -
                                            <?= esc($region['province_name']) ?>
                                        </td>
                                        <td class="text-end"><?= number_format($region['members']) ?></td>
                                        <td class="text-end">
                                            <span class="badge badge-danger">
                                                <?= number_format($region['collection_rate'], 1) ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Member Trend Chart
    var memberTrendChart = new ApexCharts(document.querySelector("#memberTrendChart"), {
        series: [{
            name: 'Total Members',
            data: <?= json_encode($trends['member_trend']['total']) ?>
        }, {
            name: 'New Members',
            data: <?= json_encode($trends['member_trend']['new']) ?>
        }, {
            name: 'Active Members',
            data: <?= json_encode($trends['member_trend']['active']) ?>
        }],
        chart: { height: 350, type: 'line', toolbar: { show: false } },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: { categories: <?= json_encode($trends['member_trend']['labels']) ?> },
        colors: ['#5c6bc0', '#66bb6a', '#42a5f5']
    });
    memberTrendChart.render();

    // Revenue Trend Chart
    var revenueTrendChart = new ApexCharts(document.querySelector("#revenueTrendChart"), {
        series: [{
            name: 'Revenue',
            type: 'area',
            data: <?= json_encode($trends['revenue_trend']['revenue']) ?>
        }, {
            name: 'Transactions',
            type: 'line',
            data: <?= json_encode($trends['revenue_trend']['transactions']) ?>
        }],
        chart: { height: 350, type: 'line', toolbar: { show: false } },
        stroke: { curve: 'smooth', width: [0, 2] },
        fill: { type: ['gradient', 'solid'], gradient: { opacityFrom: 0.6, opacityTo: 0.1 } },
        xaxis: { categories: <?= json_encode($trends['revenue_trend']['labels']) ?> },
        yaxis: [{
            title: { text: 'Revenue (Rp)' },
            labels: {
                formatter: function(val) {
                    return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                }
            }
        }, {
            opposite: true,
            title: { text: 'Transactions' }
        }],
        colors: ['#26a69a', '#ffa726']
    });
    revenueTrendChart.render();

    // Retention Chart
    var retentionChart = new ApexCharts(document.querySelector("#retentionChart"), {
        series: [{
            name: 'Retention Rate',
            data: <?= json_encode(array_column($retention['cohorts'], 'retention_rate')) ?>
        }],
        chart: { height: 250, type: 'bar' },
        plotOptions: {
            bar: {
                borderRadius: 4,
                dataLabels: { position: 'top' }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val.toFixed(1) + '%';
            },
            offsetY: -20
        },
        xaxis: { categories: <?= json_encode(array_column($retention['cohorts'], 'month')) ?> },
        yaxis: {
            min: 0,
            max: 100,
            labels: {
                formatter: function(val) {
                    return val.toFixed(0) + '%';
                }
            }
        },
        colors: ['#ab47bc']
    });
    retentionChart.render();
});

function refreshKPIs() {
    // Placeholder for AJAX refresh
    fetch('<?= base_url('admin/analytics/kpi-api') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
}
</script>

<?= $this->endSection() ?>
