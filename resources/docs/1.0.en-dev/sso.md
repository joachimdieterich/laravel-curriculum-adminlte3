# SSO with SAML2

---

- [Configuration](#section-1)


<a name="section-1"></a>
## Configuration

Curriculum uses [aacotroneo/laravel-saml2](https://github.com/aacotroneo/laravel-saml2) to integrate a SP (service provider).

Set up the `.env` to get SSO working. Example:
```bash
SAML2_RLP_IDP_HOST=https://idp_url
SAML2_RLP_IDP_ENTITYID=https://idp_url/idp/SAML2/METADATA
SAML2_RLP_IDP_SSO_URL=https://idp_url/idp/SAML2/REDIRECT/SSO
SAML2_RLP_IDP_SL_URL=https://idp_url/idp/SAML2/REDIRECT/SLO
SAML2_RLP_IDP_x509=IDPcertificate

SAML2_RLP_SP_x509=SP certificate
SAML2_RLP_SP_PRIVATEKEY=privatekey

SAML2_RLP_IDP_CONTACT_NAME=name
SAML2_RLP_IDP_CONTACT_EMAIL=email

SAML2_RLP_IDP_ORG_NAME=org name
SAML2_RLP_IDP_ORG_URL=some url
```

If `SAML2_RLP_IDP_SSO_URL` SSO Login-Button is available, if `SAML2_RLP_IDP_SL_UR` is set, Logout uses this URL

Further Settings are found in `config\saml2\rlp_idp_settings.php` and `config\saml2_settings.php`

You also have to set up your IDP. The following routes will help you:
```bash
http://laravelurl/saml2/rlp/acs
http://laravelurl/saml2/rlp/login
http://laravelurl/saml2/rlp/logout
http://laravelurl/saml2/rlp/metadata
http://laravelurl/saml2/rlp/sls
```
More information at [aacotroneo/laravel-saml2](https://github.com/aacotroneo/laravel-saml2)

