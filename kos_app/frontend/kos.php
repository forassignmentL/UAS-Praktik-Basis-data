<?php
/**
 * kos.php — CRUD Kos
 */
require_once __DIR__ . '/helpers.php';

// ── Proses form ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['_action'] ?? '';

    if ($act === 'tambah') {
        $res = api_post('/kos', [
            'nama_kos'   => trim($_POST['nama_kos']   ?? ''),
            'alamat'     => trim($_POST['alamat']     ?? ''),
            'deskripsi'  => trim($_POST['deskripsi']  ?? ''),
            'id_pemilik' => (int)($_POST['id_pemilik'] ?? 0),
        ]);
        set_flash($res['status'] === 'ok' ? 'ok' : 'danger', $res['message']);

    } elseif ($act === 'edit') {
        $id  = (int)($_POST['id_kos'] ?? 0);
        $res = api_put("/kos/$id", [
            'nama_kos'   => trim($_POST['nama_kos']   ?? ''),
            'alamat'     => trim($_POST['alamat']     ?? ''),
            'deskripsi'  => trim($_POST['deskripsi']  ?? ''),
            'id_pemilik' => (int)($_POST['id_pemilik'] ?? 0),
        ]);
        set_flash($res['status'] === 'ok' ? 'ok' : 'danger', $res['message']);

    } elseif ($act === 'hapus') {
        $id  = (int)($_POST['id_kos'] ?? 0);
        $res = api_delete("/kos/$id");
        set_flash($res['status'] === 'ok' ? 'ok' : 'danger', $res['message']);
    }

    header('Location: kos.php');
    exit;
}

// ── Ambil data ────────────────────────────────────────────────────────────────
$res_kos    = api_get('/kos');
$kos_list   = $res_kos['data'] ?? [];

$res_pemilik = api_get('/pemilik');
$pemilik_list = $res_pemilik['data'] ?? [];

$page_title = 'Master Kos';

require_once __DIR__ . '/layout/header.php';
?>

<!-- ── TOOLBAR ───────────────────────────────────────────────────────────────── -->
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h5 class="fw-bold mb-0"><i class="bi bi-building-fill me-2 text-success"></i>Data Kos</h5>
    <small class="text-muted">Total: <?= count($kos_list) ?> properti kos</small>
  </div>
  <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
    <i class="bi bi-plus-lg me-1"></i>Tambah Kos
  </button>
</div>

<!-- ── TABEL ─────────────────────────────────────────────────────────────────── -->
<div class="table-card p-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width:50px">#</th>
          <th>Nama Kos</th>
          <th>Pemilik</th>
          <th>Alamat</th>
          <th style="width:90px" class="text-center">Kamar</th>
          <th>Deskripsi</th>
          <th style="width:110px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($kos_list)): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data kos.</td></tr>
        <?php else: ?>
        <?php foreach ($kos_list as $i => $k): ?>
        <tr>
          <td class="text-muted"><?= $i + 1 ?></td>
          <td class="fw-semibold"><?= htmlspecialchars($k['nama_kos']) ?></td>
          <td>
            <small class="text-muted d-block"><?= htmlspecialchars($k['nama_pemilik'] ?? '-') ?></small>
            <small><?= htmlspecialchars($k['nama_usaha'] ?? '') ?></small>
          </td>
          <td><small><?= htmlspecialchars($k['alamat']) ?></small></td>
          <td class="text-center">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
              <?= (int)$k['jumlah_kamar'] ?> kamar
            </span>
          </td>
          <td>
            <small class="text-muted">
              <?= htmlspecialchars(mb_strimwidth($k['deskripsi'] ?? '', 0, 60, '…')) ?>
            </small>
          </td>
          <td>
            <button class="btn btn-outline-warning btn-action me-1"
                    onclick="bukaEdit(<?= htmlspecialchars(json_encode($k)) ?>)"
                    title="Edit"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-outline-danger btn-action"
                    onclick="konfirmasiHapus(<?= $k['id_kos'] ?>, '<?= addslashes($k['nama_kos']) ?>')"
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
        <h6 class="modal-title"><i class="bi bi-building-add me-2"></i>Tambah Kos</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="kos.php">
        <input type="hidden" name="_action" value="tambah">
        <div class="modal-body" id="tambahBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success btn-sm px-4">
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
        <h6 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Kos</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="kos.php">
        <input type="hidden" name="_action" value="edit">
        <input type="hidden" name="id_kos" id="edit_id">
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
        <h6 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Hapus Kos</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Hapus kos <strong id="hapus_nama"></strong>? Semua kamar di dalamnya juga akan terhapus.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
        <form method="POST" action="kos.php" class="d-inline">
          <input type="hidden" name="_action" value="hapus">
          <input type="hidden" name="id_kos" id="hapus_id">
          <button type="submit" class="btn btn-danger btn-sm px-3">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Data pemilik dari PHP -> JS
const PEMILIK = <?= json_encode(array_map(fn($p) => [
    'id'   => $p['id_pemilik'],
    'text' => $p['nama_usaha'] . ' (' . $p['nama_pemilik'] . ')'
], $pemilik_list)) ?>;

function buildForm(data = {}) {
  const v  = (k, def='') => data[k] !== undefined ? String(data[k]).replace(/"/g,'&quot;') : def;
  const opts = PEMILIK.map(p =>
    `<option value="${p.id}" ${p.id==data.id_pemilik?'selected':''}>${p.text}</option>`
  ).join('');
  return `
  <div class="mb-3">
    <label class="form-label fw-semibold">Nama Kos <span class="text-danger">*</span></label>
    <input name="nama_kos" class="form-control" placeholder="Kos Melati Indah" value="${v('nama_kos')}" required>
  </div>
  <div class="mb-3">
    <label class="form-label fw-semibold">Pemilik <span class="text-danger">*</span></label>
    <select name="id_pemilik" class="form-select" required>
      <option value="">-- Pilih Pemilik --</option>${opts}
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
    <textarea name="alamat" class="form-control" rows="2" placeholder="Jl. Contoh No. 1, Surabaya" required>${v('alamat')}</textarea>
  </div>
  <div class="mb-3">
    <label class="form-label fw-semibold">Deskripsi</label>
    <textarea name="deskripsi" class="form-control" rows="2" placeholder="Keterangan tambahan (opsional)">${v('deskripsi')}</textarea>
  </div>`;
}

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('tambahBody').innerHTML = buildForm();
});

function bukaEdit(k) {
  document.getElementById('edit_id').value     = k.id_kos;
  document.getElementById('editBody').innerHTML = buildForm(k);
  new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function konfirmasiHapus(id, nama) {
  document.getElementById('hapus_id').value          = id;
  document.getElementById('hapus_nama').textContent  = nama;
  new bootstrap.Modal(document.getElementById('modalHapus')).show();
}
</script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
