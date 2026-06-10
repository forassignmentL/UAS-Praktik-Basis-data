<?php
/**
 * pengguna.php — CRUD Pengguna
 * Operasi POST diterima di sini, lalu redirect (PRG pattern).
 */
require_once __DIR__ . '/helpers.php';

// ── Proses form ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['_action'] ?? '';

    if ($act === 'tambah') {
        $res = api_post('/pengguna', [
            'nama'     => trim($_POST['nama']     ?? ''),
            'email'    => trim($_POST['email']    ?? ''),
            'no_hp'    => trim($_POST['no_hp']    ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'peran'    => trim($_POST['peran']    ?? ''),
        ]);
        set_flash($res['status'] === 'ok' ? 'ok' : 'danger', $res['message']);

    } elseif ($act === 'edit') {
        $id  = (int)($_POST['id_pengguna'] ?? 0);
        $body = [
            'nama'  => trim($_POST['nama']  ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'no_hp' => trim($_POST['no_hp'] ?? ''),
            'peran' => trim($_POST['peran'] ?? ''),
        ];
        if (!empty($_POST['password'])) $body['password'] = $_POST['password'];
        $res = api_put("/pengguna/$id", $body);
        set_flash($res['status'] === 'ok' ? 'ok' : 'danger', $res['message']);

    } elseif ($act === 'hapus') {
        $id  = (int)($_POST['id_pengguna'] ?? 0);
        $res = api_delete("/pengguna/$id");
        set_flash($res['status'] === 'ok' ? 'ok' : 'danger', $res['message']);
    }

    header('Location: pengguna.php');
    exit;
}

// ── Ambil data ────────────────────────────────────────────────────────────────
$res       = api_get('/pengguna');
$pengguna  = $res['data'] ?? [];
$page_title = 'Master Pengguna';

$peran_badge = [
    'Pemilik' => 'bg-warning text-dark',
    'Admin'   => 'bg-danger',
    'Penyewa' => 'bg-primary',
    'Teknisi' => 'bg-success',
];

require_once __DIR__ . '/layout/header.php';
?>

<!-- ── TOOLBAR ───────────────────────────────────────────────────────────────── -->
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h5 class="fw-bold mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Data Pengguna</h5>
    <small class="text-muted">Total: <?= count($pengguna) ?> pengguna</small>
  </div>
  <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-plus-lg me-1"></i>Tambah Pengguna
  </button>
</div>

<!-- ── TABEL ─────────────────────────────────────────────────────────────────── -->
<div class="table-card p-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="tblPengguna">
      <thead class="table-light">
        <tr>
          <th style="width:50px">#</th>
          <th>Nama</th>
          <th>Email</th>
          <th>No. HP</th>
          <th>Peran</th>
          <th>Dibuat</th>
          <th style="width:110px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($pengguna)): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data pengguna.</td></tr>
        <?php else: ?>
        <?php foreach ($pengguna as $i => $p): ?>
        <tr>
          <td class="text-muted"><?= $i + 1 ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($p['nama']) ?></td>
          <td><small><?= htmlspecialchars($p['email']) ?></small></td>
          <td><?= htmlspecialchars($p['no_hp']) ?></td>
          <td>
            <span class="badge badge-peran <?= $peran_badge[$p['peran']] ?? 'bg-secondary' ?>">
              <?= htmlspecialchars($p['peran']) ?>
            </span>
          </td>
          <td><small class="text-muted"><?= substr($p['dibuat_pada'] ?? '', 0, 10) ?></small></td>
          <td>
            <!-- Tombol Edit -->
            <button class="btn btn-outline-warning btn-action me-1"
                    onclick="bukaEdit(<?= htmlspecialchars(json_encode($p)) ?>)"
                    title="Edit"><i class="bi bi-pencil"></i></button>
            <!-- Tombol Hapus -->
            <button class="btn btn-outline-danger btn-action"
                    onclick="konfirmasiHapus(<?= $p['id_pengguna'] ?>, '<?= addslashes($p['nama']) ?>')"
                    title="Hapus"><i class="bi bi-trash"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ══ MODAL TAMBAH ══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title"><i class="bi bi-person-plus me-2"></i>Tambah Pengguna</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="pengguna.php">
        <input type="hidden" name="_action" value="tambah">
        <div class="modal-body">
          <?php include __DIR__ . '/layout/_form_pengguna.php'; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm px-4">
            <i class="bi bi-save me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ MODAL EDIT ════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Pengguna</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="pengguna.php">
        <input type="hidden" name="_action" value="edit">
        <input type="hidden" name="id_pengguna" id="edit_id">
        <div class="modal-body" id="editBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning btn-sm px-4">
            <i class="bi bi-save me-1"></i>Perbarui
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ══ MODAL HAPUS ═══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalHapus" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h6 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Pengguna</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Hapus pengguna <strong id="hapus_nama"></strong>? Tindakan ini tidak dapat dibatalkan.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
        <form method="POST" action="pengguna.php" class="d-inline">
          <input type="hidden" name="_action" value="hapus">
          <input type="hidden" name="id_pengguna" id="hapus_id">
          <button type="submit" class="btn btn-danger btn-sm px-3">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
const PERAN = ['Pemilik','Admin','Penyewa','Teknisi'];

function buildForm(data = {}) {
  const v = (k) => data[k] ? `value="${data[k].replace(/"/g,'&quot;')}"` : '';
  const sel = (v) => PERAN.map(p =>
    `<option ${p===v?'selected':''}>${p}</option>`).join('');
  return `
  <div class="mb-3">
    <label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
    <input name="nama" class="form-control" placeholder="Nama lengkap" ${v('nama')} required>
  </div>
  <div class="mb-3">
    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
    <input name="email" type="email" class="form-control" placeholder="email@contoh.com" ${v('email')} required>
  </div>
  <div class="mb-3">
    <label class="form-label fw-semibold">No. HP <span class="text-danger">*</span></label>
    <input name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" ${v('no_hp')} required>
  </div>
  <div class="mb-3">
    <label class="form-label fw-semibold">Password ${data.id_pengguna?'<small class=\'text-muted\'>(kosongkan jika tidak diubah)</small>':' <span class=\'text-danger\'>*</span>'}</label>
    <input name="password" type="password" class="form-control" placeholder="••••••••" ${data.id_pengguna?'':'required'}>
  </div>
  <div class="mb-3">
    <label class="form-label fw-semibold">Peran <span class="text-danger">*</span></label>
    <select name="peran" class="form-select" required><option value="">-- Pilih --</option>${sel(data.peran)}</select>
  </div>`;
}

// Set form di modal Tambah saat halaman load
document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('#modalTambah .modal-body').innerHTML = buildForm();
});

function bukaEdit(p) {
  document.getElementById('edit_id').value   = p.id_pengguna;
  document.getElementById('editBody').innerHTML = buildForm(p);
  new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function konfirmasiHapus(id, nama) {
  document.getElementById('hapus_id').value   = id;
  document.getElementById('hapus_nama').textContent = nama;
  new bootstrap.Modal(document.getElementById('modalHapus')).show();
}
</script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
