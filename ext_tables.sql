#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	tx_ns_social_login_source int(11) DEFAULT '0' NOT NULL,
	tx_ns_social_login_identifier varchar(255) DEFAULT '' NOT NULL,

	INDEX socialauth_idx (tx_ns_social_login_source, tx_ns_social_login_identifier)
);