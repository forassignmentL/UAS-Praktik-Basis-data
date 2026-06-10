<?php
// layout/header.php — dipanggil di setiap halaman
if (!defined('APP_NAME')) require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers.php';
$flash        = get_flash();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($page_title ?? APP_NAME) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body      { background:#f8f9fa; font-family:'Segoe UI',sans-serif; }
    .sidebar  { min-height:100vh; background:#1a2332; }
    .sidebar .nav-link        { color:#adb5bd; border-radius:6px; padding:.55rem 1rem; }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active { background:#2c3e50; color:#fff; }
    .sidebar .brand           { color:#fff; font-weight:700; font-size:1.1rem; letter-spacing:.5px; }
    .sidebar .nav-section     { font-size:.7rem; color:#6c757d; text-transform:uppercase;
                                 letter-spacing:1px; padding:.5rem 1rem .2rem; }
    .main-content  { min-height:100vh; }
    .topbar        { background:#fff; border-bottom:1px solid #dee2e6; padding:.75rem 1.5rem; }
    .table-card    { background:#fff; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,.07); }
    .badge-peran   { font-size:.72em; }
    .btn-action    { padding:.25rem .55rem; font-size:.8rem; }
    /* modal form */
    .modal-header  { background:#1a2332; color:#fff; }
    .modal-header .btn-close { filter:invert(1); }
  </style>
</head>
<body>
<div class="d-flex">

  <!-- ── SIDEBAR ─────────────────────────────────────── -->
  <nav class="sidebar d-flex flex-column p-3" style="width:230px;min-width:230px">
    <div class="brand mb-4 mt-1 ps-1">
      <i class="bi bi-house-heart-fill me-2 text-warning"></i><?= APP_NAME ?>
    </div>
    <span class="nav-section">Master Data</span>
    <a href="pengguna.php"
       class="nav-link mb-1 <?= $current_page==='pengguna.php'?'active':'' ?>">
      <i class="bi bi-people me-2"></i>Pengguna
    </a>
    <a href="kos.php"
       class="nav-link mb-1 <?= $current_page==='kos.php'?'active':'' ?>">
      <i class="bi bi-building me-2"></i>Kos
    </a>
    <div class="mt-auto">
      <small class="text-secondary" style="font-size:.7rem">
        API: <?= API_BASE_URL ?>
      </small>
    </div>
  </nav>

  <!-- ── MAIN ────────────────────────────────────────── -->
  <div class="flex-grow-1 main-content">
    <div class="topbar d-flex align-items-center justify-content-between">
      <h6 class="mb-0 fw-semibold text-secondary">
        <?= htmlspecialchars($page_title ?? '') ?>
      </h6>
      <span class="badge bg-success-subtle text-success border border-success-subtle">
        <i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>API Connected
      </span>
    </div>

    <?php if ($flash): ?>
    <div class="mx-4 mt-3">
      <div class="alert alert-<?= $flash['type']==='ok'?'success':'danger' ?> alert-dismissible fade show" role="alert">
        <i class="bi bi-<?= $flash['type']==='ok'?'check-circle':'exclamation-triangle' ?>-fill me-2"></i>
        <?= htmlspecialchars($flash['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
    <?php endif; ?>

    <div class="p-4">
