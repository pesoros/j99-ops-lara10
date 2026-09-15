-- Fit check: tanda tangan pengemudi (legalisasi hasil pemeriksaan)
-- Disimpan per-record di ops_fit_check. TIDAK disimpan di profil crew
-- (employee_history) dan tidak dibandingkan dengan spesimen apa pun.
ALTER TABLE ops_fit_check
  ADD COLUMN driver_signature MEDIUMTEXT NULL AFTER fit_to_work,
  ADD COLUMN driver_signed_at DATETIME  NULL AFTER driver_signature;

-- Rollback:
-- ALTER TABLE ops_fit_check
--   DROP COLUMN driver_signature,
--   DROP COLUMN driver_signed_at;
