DELETE FROM `#__rsform_config` WHERE `SettingName` IN ('recaptcha.private.key', 'recaptcha.public.key', 'recaptcha.theme');
DELETE FROM `#__rsform_component_types` WHERE `ComponentTypeId`='24';
DELETE FROM `#__rsform_component_type_fields` WHERE `ComponentTypeId`='24';

INSERT IGNORE INTO `#__rsform_config` (`ConfigId`, `SettingName`, `SettingValue`) VALUES ('', 'recaptcha.private.key', '');
INSERT IGNORE INTO `#__rsform_config` (`ConfigId`, `SettingName`, `SettingValue`) VALUES ('', 'recaptcha.public.key', '');
INSERT IGNORE INTO `#__rsform_config` (`ConfigId`, `SettingName`, `SettingValue`) VALUES ('', 'recaptcha.theme', '');

INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES (24, 'recaptcha');

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeFieldId`, `ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES('', 24, 'NAME', 'textbox', '', 0);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeFieldId`, `ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES('', 24, 'CAPTION', 'textbox', '', 1);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeFieldId`, `ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES('', 24, 'ADDITIONALATTRIBUTES', 'textarea', '', 2);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeFieldId`, `ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES('', 24, 'DESCRIPTION', 'textarea', '', 3);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeFieldId`, `ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES('', 24, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', 4);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeFieldId`, `ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES('', 24, 'COMPONENTTYPE', 'hidden', '24', 5);