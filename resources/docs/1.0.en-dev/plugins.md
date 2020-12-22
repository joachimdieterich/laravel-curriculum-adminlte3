# Plugins

---

- [edu-sharing](#section-1)
- [eVewa](#section-2)


<a name="section-1"></a>
## edu-sharing
Adding following lines to `.env` to config edu-sharing plugin (without square brackets)
```bash
EDUSHARING_GRAND_TYPE="password"
EDUSHARING_CLIENT_ID="eduApp"
EDUSHARING_CLIENT_SECRET="secret"
EDUSHARING_REPO_URL="[edu-sharing URL]"
EDUSHARING_REPO_USER="[admin]"
EDUSHARING_REPO_PWD="[password]"
EDUSHARING_REPO_PROXY=false
EDUSHARING_REPO_PROXY_PORT=false
```
Adding the following fields to config table. This could be done as administrator in the frontend under config/Einstellungen.
```
INSERT INTO `configs` (`key`, `value`, `referenceable_type`, `referenceable_id`, `data_type`)
VALUES
	('privateKey', '-----BEGIN PRIVATE KEY----[Key goes here]-----END PRIVATE KEY-----', 'App\\Edusharing', NULL, 'string'),
	('appId', 'curriculum', 'App\\Edusharing', NULL, 'string'),
	('wsdl', 'https://[URL]/edu-sharing/services/authbyapp?wsdl', 'App\\Edusharing', NULL, 'string'),
	('accessMode', 'personal', 'App\\Edusharing', NULL, 'string'),
	('metadata_password', ‚[PW]‘, NULL, NULL, 'string'),
	('repository', 'edusharing', NULL, NULL, 'string'); 
```
<a name="section-1"></a>
## eVewa
Adding following lines to `.env` to config eVewa plugin (without square brackets)
```bash
EVENTMANAGEMENTPLUGIN="eVewa"
EVEWA_API_USER="[api user]"
EVEWA_API_PASSWORD="[api password]"
EVEWA_API_URL="https://[DOMAIN]/evewa3/evewa3rest.php"
EVEWA_PROXY_PORT=false
EVEWA_PROXY=false
```
