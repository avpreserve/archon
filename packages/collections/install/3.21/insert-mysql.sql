INSERT INTO tblCore_Configuration (PackageID, ModuleID, Directive, `Value`, InputType, PatternID, ReadOnly, Encrypted, ListDataSource) VALUES ((SELECT ID FROM tblCore_Packages WHERE APRCode = 'collections'), '0', 'Enable Finding Aid Caching', '0', 'radio', '3', '0', '0', NULL);