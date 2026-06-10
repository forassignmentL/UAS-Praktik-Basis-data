"""
=============================================================
 Flask REST API — Sistem Informasi Pengelolaan Kos
 Tabel : PENGGUNA, KOS
 Jalankan : python app.py
 Base URL  : http://localhost:5000/api
=============================================================
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector
import hashlib
import os

app = Flask(__name__)
CORS(app)   # izinkan request dari PHP frontend (cross-origin)

# ── Konfigurasi koneksi MySQL ─────────────────────────────────────────────────
DB_CONFIG = {
    "host":     os.getenv("DB_HOST",     "localhost"),
    "port":     int(os.getenv("DB_PORT", 3306)),
    "user":     os.getenv("DB_USER",     "root"),
    "password": os.getenv("DB_PASSWORD", ""),
    "database": os.getenv("DB_NAME",     "db_pengelolaan_kos"),
    "charset":  "utf8mb4",
}

def get_db():
    """Buka koneksi baru ke database."""
    return mysql.connector.connect(**DB_CONFIG)

def ok(data=None, msg="Berhasil", code=200):
    return jsonify({"status": "ok", "message": msg, "data": data}), code

def err(msg="Terjadi kesalahan", code=400):
    return jsonify({"status": "error", "message": msg}), code

# =============================================================================
#  PENGGUNA — CRUD
# =============================================================================

@app.route("/api/pengguna", methods=["GET"])
def get_pengguna():
    """Ambil semua pengguna. Opsional: ?peran=Penyewa"""
    conn = get_db(); cur = conn.cursor(dictionary=True)
    try:
        peran = request.args.get("peran")
        if peran:
            cur.execute(
                "SELECT id_pengguna, nama, email, no_hp, peran, dibuat_pada "
                "FROM PENGGUNA WHERE peran = %s ORDER BY nama", (peran,))
        else:
            cur.execute(
                "SELECT id_pengguna, nama, email, no_hp, peran, dibuat_pada "
                "FROM PENGGUNA ORDER BY peran, nama")
        rows = cur.fetchall()
        # ubah datetime ke string agar JSON-serializable
        for r in rows:
            if r.get("dibuat_pada"):
                r["dibuat_pada"] = str(r["dibuat_pada"])
        return ok(rows)
    except Exception as e:
        return err(str(e))
    finally:
        cur.close(); conn.close()


@app.route("/api/pengguna/<int:id_pengguna>", methods=["GET"])
def get_pengguna_by_id(id_pengguna):
    """Ambil satu pengguna berdasarkan ID."""
    conn = get_db(); cur = conn.cursor(dictionary=True)
    try:
        cur.execute(
            "SELECT id_pengguna, nama, email, no_hp, peran, dibuat_pada "
            "FROM PENGGUNA WHERE id_pengguna = %s", (id_pengguna,))
        row = cur.fetchone()
        if not row:
            return err("Pengguna tidak ditemukan", 404)
        if row.get("dibuat_pada"):
            row["dibuat_pada"] = str(row["dibuat_pada"])
        return ok(row)
    except Exception as e:
        return err(str(e))
    finally:
        cur.close(); conn.close()


@app.route("/api/pengguna", methods=["POST"])
def create_pengguna():
    """Tambah pengguna baru."""
    d = request.get_json(force=True) or {}
    required = ["nama", "email", "no_hp", "password", "peran"]
    for f in required:
        if not d.get(f):
            return err(f"Field '{f}' wajib diisi")

    if d["peran"] not in ("Pemilik", "Admin", "Penyewa", "Teknisi"):
        return err("Peran tidak valid")

    pw_hash = hashlib.sha256(d["password"].encode()).hexdigest()
    conn = get_db(); cur = conn.cursor()
    try:
        cur.execute(
            "INSERT INTO PENGGUNA (nama, email, no_hp, password_hash, peran) "
            "VALUES (%s, %s, %s, %s, %s)",
            (d["nama"], d["email"], d["no_hp"], pw_hash, d["peran"]))
        conn.commit()
        return ok({"id_pengguna": cur.lastrowid}, "Pengguna berhasil ditambahkan", 201)
    except mysql.connector.IntegrityError:
        return err("Email sudah terdaftar")
    except Exception as e:
        return err(str(e))
    finally:
        cur.close(); conn.close()


@app.route("/api/pengguna/<int:id_pengguna>", methods=["PUT"])
def update_pengguna(id_pengguna):
    """Update data pengguna. Password opsional (kosongkan jika tidak diganti)."""
    d = request.get_json(force=True) or {}
    conn = get_db(); cur = conn.cursor()
    try:
        # cek exists
        cur.execute("SELECT id_pengguna FROM PENGGUNA WHERE id_pengguna = %s", (id_pengguna,))
        if not cur.fetchone():
            return err("Pengguna tidak ditemukan", 404)

        fields, vals = [], []
        for col in ("nama", "email", "no_hp", "peran"):
            if d.get(col):
                fields.append(f"{col} = %s")
                vals.append(d[col])
        if d.get("password"):
            fields.append("password_hash = %s")
            vals.append(hashlib.sha256(d["password"].encode()).hexdigest())

        if not fields:
            return err("Tidak ada data yang diubah")

        vals.append(id_pengguna)
        cur.execute(f"UPDATE PENGGUNA SET {', '.join(fields)} WHERE id_pengguna = %s", vals)
        conn.commit()
        return ok(msg="Pengguna berhasil diperbarui")
    except mysql.connector.IntegrityError:
        return err("Email sudah digunakan pengguna lain")
    except Exception as e:
        return err(str(e))
    finally:
        cur.close(); conn.close()


@app.route("/api/pengguna/<int:id_pengguna>", methods=["DELETE"])
def delete_pengguna(id_pengguna):
    """Hapus pengguna berdasarkan ID."""
    conn = get_db(); cur = conn.cursor()
    try:
        cur.execute("SELECT id_pengguna FROM PENGGUNA WHERE id_pengguna = %s", (id_pengguna,))
        if not cur.fetchone():
            return err("Pengguna tidak ditemukan", 404)
        cur.execute("DELETE FROM PENGGUNA WHERE id_pengguna = %s", (id_pengguna,))
        conn.commit()
        return ok(msg="Pengguna berhasil dihapus")
    except mysql.connector.IntegrityError:
        return err("Tidak bisa dihapus: data masih digunakan tabel lain")
    except Exception as e:
        return err(str(e))
    finally:
        cur.close(); conn.close()


# =============================================================================
#  KOS — CRUD
# =============================================================================

@app.route("/api/kos", methods=["GET"])
def get_kos():
    """Ambil semua kos, sertakan nama pemilik & jumlah kamar (via COUNT)."""
    conn = get_db(); cur = conn.cursor(dictionary=True)
    try:
        cur.execute("""
            SELECT k.id_kos, k.nama_kos, k.alamat, k.deskripsi,
                   k.id_pemilik, p.nama_usaha AS nama_pemilik,
                   COUNT(km.id_kamar) AS jumlah_kamar
            FROM KOS k
            LEFT JOIN PEMILIK_KOS p  ON k.id_pemilik = p.id_pemilik
            LEFT JOIN KAMAR       km ON k.id_kos     = km.id_kos
            GROUP BY k.id_kos
            ORDER BY k.nama_kos
        """)
        return ok(cur.fetchall())
    except Exception as e:
        return err(str(e))
    finally:
        cur.close(); conn.close()


@app.route("/api/kos/<int:id_kos>", methods=["GET"])
def get_kos_by_id(id_kos):
    """Ambil satu kos berdasarkan ID."""
    conn = get_db(); cur = conn.cursor(dictionary=True)
    try:
        cur.execute("""
            SELECT k.id_kos, k.nama_kos, k.alamat, k.deskripsi, k.id_pemilik,
                   p.nama_usaha AS nama_pemilik
            FROM KOS k
            LEFT JOIN PEMILIK_KOS p ON k.id_pemilik = p.id_pemilik
            WHERE k.id_kos = %s
        """, (id_kos,))
        row = cur.fetchone()
        if not row:
            return err("Kos tidak ditemukan", 404)
        return ok(row)
    except Exception as e:
        return err(str(e))
    finally:
        cur.close(); conn.close()


@app.route("/api/kos", methods=["POST"])
def create_kos():
    """Tambah kos baru."""
    d = request.get_json(force=True) or {}
    for f in ("nama_kos", "alamat", "id_pemilik"):
        if not d.get(f):
            return err(f"Field '{f}' wajib diisi")
    conn = get_db(); cur = conn.cursor()
    try:
        cur.execute(
            "INSERT INTO KOS (id_pemilik, nama_kos, alamat, deskripsi) VALUES (%s,%s,%s,%s)",
            (d["id_pemilik"], d["nama_kos"], d["alamat"], d.get("deskripsi", "")))
        conn.commit()
        return ok({"id_kos": cur.lastrowid}, "Kos berhasil ditambahkan", 201)
    except mysql.connector.IntegrityError as e:
        return err("id_pemilik tidak valid: pemilik tidak ditemukan")
    except Exception as e:
        return err(str(e))
    finally:
        cur.close(); conn.close()


@app.route("/api/kos/<int:id_kos>", methods=["PUT"])
def update_kos(id_kos):
    """Update data kos."""
    d = request.get_json(force=True) or {}
    conn = get_db(); cur = conn.cursor()
    try:
        cur.execute("SELECT id_kos FROM KOS WHERE id_kos = %s", (id_kos,))
        if not cur.fetchone():
            return err("Kos tidak ditemukan", 404)

        fields, vals = [], []
        for col in ("nama_kos", "alamat", "deskripsi", "id_pemilik"):
            if col in d:
                fields.append(f"{col} = %s")
                vals.append(d[col])
        if not fields:
            return err("Tidak ada data yang diubah")

        vals.append(id_kos)
        cur.execute(f"UPDATE KOS SET {', '.join(fields)} WHERE id_kos = %s", vals)
        conn.commit()
        return ok(msg="Kos berhasil diperbarui")
    except Exception as e:
        return err(str(e))
    finally:
        cur.close(); conn.close()


@app.route("/api/kos/<int:id_kos>", methods=["DELETE"])
def delete_kos(id_kos):
    """Hapus kos berdasarkan ID."""
    conn = get_db(); cur = conn.cursor()
    try:
        cur.execute("SELECT id_kos FROM KOS WHERE id_kos = %s", (id_kos,))
        if not cur.fetchone():
            return err("Kos tidak ditemukan", 404)
        cur.execute("DELETE FROM KOS WHERE id_kos = %s", (id_kos,))
        conn.commit()
        return ok(msg="Kos berhasil dihapus")
    except mysql.connector.IntegrityError:
        return err("Tidak bisa dihapus: masih memiliki kamar terdaftar")
    except Exception as e:
        return err(str(e))
    finally:
        cur.close(); conn.close()


# =============================================================================
#  HELPER — daftar pemilik kos (untuk dropdown di form)
# =============================================================================

@app.route("/api/pemilik", methods=["GET"])
def get_pemilik():
    conn = get_db(); cur = conn.cursor(dictionary=True)
    try:
        cur.execute(
            "SELECT pk.id_pemilik, pk.nama_usaha, p.nama AS nama_pemilik "
            "FROM PEMILIK_KOS pk JOIN PENGGUNA p ON pk.id_pengguna = p.id_pengguna "
            "ORDER BY pk.nama_usaha")
        return ok(cur.fetchall())
    except Exception as e:
        return err(str(e))
    finally:
        cur.close(); conn.close()


# =============================================================================
#  MAIN
# =============================================================================
if __name__ == "__main__":
    app.run(debug=True, host="0.0.0.0", port=5000)
