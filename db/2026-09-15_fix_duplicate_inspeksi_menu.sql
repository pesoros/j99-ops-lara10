-- ============================================================
-- Memperbaiki menu "Inspeksi" / "Fit Check" yang tampil ganda.
--
-- PENYEBAB
-- getMenuSU() dan getMenuWithRole() men-join ops_menu ke ops_permission
-- lewat slug TANPA DISTINCT. Jadi satu baris menu akan tampil dua kali
-- bila ops_permission punya dua baris untuk slug+access yang sama.
-- Menu ganda bisa berasal dari baris menu ganda, baris permission ganda,
-- atau keduanya.
--
-- JALANKAN BAGIAN 1 DULU untuk melihat kondisinya, baru BAGIAN 2.
-- ============================================================


-- ============================================================
-- BAGIAN 1 - DIAGNOSA (hanya SELECT, tidak mengubah data)
-- ============================================================

-- 1a. Baris menu untuk slug terkait
SELECT 'MENU' AS jenis, id, title, slug, url, parent_id, `order`, status
  FROM ops_menu
 WHERE slug IN ('inspection', 'fit-check')
 ORDER BY slug, id;

-- 1b. Baris permission untuk slug terkait
SELECT 'PERM' AS jenis, id, slug, access, status
  FROM ops_permission
 WHERE slug IN ('inspection', 'fit-check')
 ORDER BY slug, access, id;

-- 1c. Permission kembar (inilah yang biasanya menggandakan menu)
SELECT slug, access, COUNT(*) AS jumlah, GROUP_CONCAT(id ORDER BY id) AS ids
  FROM ops_permission
 GROUP BY slug, access
HAVING COUNT(*) > 1;

-- 1d. Berapa baris yang sebenarnya dikembalikan ke sidebar
--     (meniru getMenuSU untuk menu induk)
SELECT menu.id, menu.title, COUNT(*) AS baris_dikembalikan
  FROM ops_menu AS menu
  JOIN ops_permission AS perm ON perm.slug = menu.slug
 WHERE perm.access = 'index' AND perm.status = 1 AND menu.parent_id IS NULL
 GROUP BY menu.id, menu.title
 ORDER BY menu.`order`;


-- ============================================================
-- BAGIAN 2 - PERBAIKAN
-- Menyisakan satu baris (id terkecil) untuk tiap kombinasi.
-- Disarankan backup dulu: mysqldump ... ops_menu ops_permission ops_role_permission
-- ============================================================

-- 2a. Arahkan pemberian akses role ke permission yang akan dipertahankan,
--     supaya hak akses tidak hilang saat baris kembar dihapus.
UPDATE ops_role_permission AS rp
  JOIN ops_permission AS p ON p.id = rp.permission_id
  JOIN (
        SELECT slug, access, MIN(id) AS keep_id
          FROM ops_permission
         WHERE slug IN ('inspection', 'fit-check')
         GROUP BY slug, access
       ) AS k ON k.slug = p.slug AND k.access = p.access
   SET rp.permission_id = k.keep_id
 WHERE p.slug IN ('inspection', 'fit-check');

-- 2b. Hapus pemberian akses yang jadi kembar setelah langkah 2a.
DELETE rp1 FROM ops_role_permission AS rp1
  JOIN ops_role_permission AS rp2
    ON rp1.role_id = rp2.role_id
   AND rp1.permission_id = rp2.permission_id
   AND rp1.id > rp2.id;

-- 2c. Hapus permission kembar, sisakan id terkecil.
DELETE p FROM ops_permission AS p
  JOIN (
        SELECT slug, access, MIN(id) AS keep_id
          FROM ops_permission
         WHERE slug IN ('inspection', 'fit-check')
         GROUP BY slug, access
       ) AS k ON k.slug = p.slug AND k.access = p.access
 WHERE p.slug IN ('inspection', 'fit-check')
   AND p.id <> k.keep_id;

-- 2d. Sisakan satu menu induk "Inspeksi".
SET @keep_parent = (SELECT MIN(id) FROM ops_menu WHERE slug = 'inspection' AND parent_id IS NULL);

DELETE FROM ops_menu
 WHERE slug = 'inspection' AND parent_id IS NULL AND id <> @keep_parent;

-- 2e. Sisakan satu menu anak "Fit Check", lalu tempelkan ke induk yang tersisa.
SET @keep_child = (SELECT MIN(id) FROM ops_menu WHERE slug = 'fit-check');

DELETE FROM ops_menu
 WHERE slug = 'fit-check' AND id <> @keep_child;

UPDATE ops_menu
   SET parent_id = @keep_parent, `order` = 1, updated_at = NOW()
 WHERE id = @keep_child;


-- ============================================================
-- BAGIAN 3 - VERIFIKASI (jalankan ulang 1a, 1b, 1d)
-- Hasil yang benar: 1 baris menu 'inspection', 1 baris menu 'fit-check',
-- 1 permission per slug+access, dan kolom baris_dikembalikan = 1 semua.
--
-- Setelah itu logout lalu login ulang.
-- ============================================================
