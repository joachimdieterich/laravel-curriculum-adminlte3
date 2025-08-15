# Plugins

---

- [edu-sharing](#section-1)
- [eVewa](#section-2)


<a name="section-1"></a>
## edu-sharing
Adding following lines to `.env` to config edu-sharing plugin (without square brackets)
```bash
EDUSHARING_REPO_URL="[edu-sharing URL]"

EDUSHARING_REPO_PROXY=false
EDUSHARING_REPO_PROXY_PORT=false
EDUSHARING_UPLOAD_IFRAME_URL="https://[edu-sharing URL]/edu-sharing/components/upload?reurl=IFRAME"
EDUSHARING_CLOUD_IFRAME_URL="https://[edu-sharing URL]/edu-sharing/components/search?applyDirectories=true&locale=utf-8&reurl=IFRAME"
EDUSHARING_APP_ID="[appId]"
EDUSHARING_CURLOPT_SSL_VERIFYHOST=0
EDUSHARING_CURLOPT_SSL_VERIFYPEER=0
EDUSHARING_PRIV_KEY="-----BEGIN PRIVATE KEY-----
[...]
-----END PRIVATE KEY-----"
```
Adding the following fields to config table. This could be done as administrator in the frontend under config/Einstellungen.
```
INSERT INTO `configs` (`key`, `value`, `referenceable_type`, `referenceable_id`, `data_type`)
VALUES
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
