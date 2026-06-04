UPDATE `pt_areas`
SET `icon` = CASE `area_key`
  WHEN 'portal' THEN 'icon-home'
  WHEN 'identity' THEN 'icon-shield'
  WHEN 'bibliocollect' THEN 'icon-library'
  WHEN 'methodenmatrix' THEN 'icon-methods'
  WHEN 'development' THEN 'icon-server'
  WHEN 'styleguide' THEN 'icon-layout'
  ELSE `icon`
END;
