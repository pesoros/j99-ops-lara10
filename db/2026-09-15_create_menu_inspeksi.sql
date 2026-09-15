-- ============================================================
-- Membuat menu induk "Inspeksi" beserta anaknya "Fit Check".
--
-- Hasil di sidebar:
--   Inspeksi
--     └ Fit Check   ->  inspection/fit-check
--
-- Aman dijalankan berulang: setiap INSERT dijaga dengan NOT EXISTS,
-- sehingga tidak menimbulkan baris ganda bila sebagian sudah ada.
-- ============================================================

-- ------------------------------------------------------------
-- 1) HAK AKSES
-- ------------------------------------------------------------
-- Menu induk hanya tampil bila ada baris access = 'index':
-- getMenuSU() dan getMenuWithRole() men-join ops_menu ke ops_permission
-- lewat slug. Baris 'index' tidak memberi hak apa pun pada middleware,
-- karena getRoleAccess() justru membuangnya.
INSERT INTO ops_permission (slug, access, status, created_at, updated_at)
SELECT 'inspection', 'index', 1, NOW(), NOW() FROM (SELECT 1) d
WHERE NOT EXISTS (SELECT 1 FROM ops_permission WHERE slug = 'inspection' AND access = 'index');

-- Hak akses halaman fit check. Baris 'show' juga yang membuat menu
-- ANAK tampil (getChildMenuSU mensyaratkan perm.access = 'show').
INSERT INTO ops_permission (slug, access, status, created_at, updated_at)
SELECT 'fit-check', 'show', 1, NOW(), NOW() FROM (SELECT 1) d
WHERE NOT EXISTS (SELECT 1 FROM ops_permission WHERE slug = 'fit-check' AND access = 'show');

INSERT INTO ops_permission (slug, access, status, created_at, updated_at)
SELECT 'fit-check', 'add', 1, NOW(), NOW() FROM (SELECT 1) d
WHERE NOT EXISTS (SELECT 1 FROM ops_permission WHERE slug = 'fit-check' AND access = 'add');

INSERT INTO ops_permission (slug, access, status, created_at, updated_at)
SELECT 'fit-check', 'edit', 1, NOW(), NOW() FROM (SELECT 1) d
WHERE NOT EXISTS (SELECT 1 FROM ops_permission WHERE slug = 'fit-check' AND access = 'edit');

INSERT INTO ops_permission (slug, access, status, created_at, updated_at)
SELECT 'fit-check', 'delete', 1, NOW(), NOW() FROM (SELECT 1) d
WHERE NOT EXISTS (SELECT 1 FROM ops_permission WHERE slug = 'fit-check' AND access = 'delete');

-- ------------------------------------------------------------
-- 2) MENU INDUK "Inspeksi"
-- ------------------------------------------------------------
-- Urutan 5 (di bawah "Karyawan"). Bila slot 5 pada database lain masih
-- terpakai, jalankan dulu pergeseran berikut:
--   UPDATE ops_menu SET `order` = `order` + 1
--    WHERE parent_id IS NULL AND `order` >= 5;
INSERT INTO ops_menu (title, slug, url, module, parent_id, icon, `order`, status, created_at, updated_at)
SELECT 'Inspeksi', 'inspection', 'inspection', 'inspection', NULL,
       '<i class="far fa-circle nav-icon"></i>', 5, 1, NOW(), NOW()
FROM (SELECT 1) d
WHERE NOT EXISTS (SELECT 1 FROM ops_menu WHERE slug = 'inspection' AND parent_id IS NULL);

-- ------------------------------------------------------------
-- 3) MENU ANAK "Fit Check"
-- ------------------------------------------------------------
SET @inspeksi_id = (SELECT id FROM ops_menu WHERE slug = 'inspection' AND parent_id IS NULL LIMIT 1);

INSERT INTO ops_menu (title, slug, url, module, parent_id, icon, `order`, status, created_at, updated_at)
SELECT 'Fit Check', 'fit-check', 'inspection/fit-check', 'inspection', @inspeksi_id,
       '<i class="far fa-circle nav-icon"></i>', 1, 1, NOW(), NOW()
FROM (SELECT 1) d
WHERE NOT EXISTS (SELECT 1 FROM ops_menu WHERE slug = 'fit-check');

-- Bila baris fit-check sudah ada namun menempel di induk lain, pindahkan.
UPDATE ops_menu
   SET parent_id = @inspeksi_id, `order` = 1, updated_at = NOW()
 WHERE slug = 'fit-check';

-- ------------------------------------------------------------
-- 4) AKSES UNTUK ROLE SELAIN SUPER USER (opsional)
-- ------------------------------------------------------------
-- Super User (role_id 1) tidak perlu ini. Role lain WAJIB dipetakan,
-- termasuk permission 'inspection index', karena getMenuWithRole()
-- ikut men-join ops_role_permission untuk menampilkan menu induk.
-- Role: 2 Head Operational | 3 Operational | 4 Warehouse
--       5 Mechanic | 6 Accounting | 7 Purchasing | 8 Manifest Only
--
-- INSERT INTO ops_role_permission (role_id, permission_id, created_at, updated_at)
-- SELECT 3, p.id, NOW(), NOW() FROM ops_permission p
--  WHERE p.slug IN ('fit-check','inspection')
--    AND NOT EXISTS (SELECT 1 FROM ops_role_permission r
--                     WHERE r.role_id = 3 AND r.permission_id = p.id);

-- ============================================================
-- Logout lalu login ulang: menu_session dibangun saat login saja.
-- ============================================================

-- ------------------------------------------------------------
-- ROLLBACK
-- ------------------------------------------------------------
-- DELETE FROM ops_menu WHERE slug = 'fit-check';
-- DELETE FROM ops_menu WHERE slug = 'inspection' AND parent_id IS NULL;
-- DELETE FROM ops_permission WHERE slug = 'inspection' AND access = 'index';
