# Users

{% hint style="info" %}
**Good to know:** All the methods shown below are synced to an example Swagger file URL and are kept up to date automatically with changes to the API.
{% endhint %}

## User actions

{% swagger method="post" path="/users/connect-minecraft" baseUrl="https://www.beastblock.cz/api/v1" summary="Request for connecting Minecraft accound to web account" %}
{% swagger-description %}
Vytvoří token, kterým hráč ověří svůj MC účet na webu.
{% endswagger-description %}

{% swagger-parameter in="body" name="nick" required="true" %}
Nick hráče
{% endswagger-parameter %}

{% swagger-parameter in="body" name="uuid" required="true" %}
UUID hráče
{% endswagger-parameter %}

{% swagger-parameter in="header" required="true" name="Authorizaton: Bearer TOKEN" %}
Bearer auth token
{% endswagger-parameter %}

{% swagger-response status="200: OK" description="Token byl vytvořen" %}
```javascript
{
    "status": "ok",
    "token": "XXXX-XXXX-XXXX-XXXX"
}
```
{% endswagger-response %}

{% swagger-response status="200: OK" description="UUID je již propojené s webovým účtem" %}
```javascript
{
    "status": "error",
    "message": "Účet je již propojen."
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="UUID nebo Nick nejsou vyplněny" %}
```javascript
{
    "status": "error",
    "message": "Missing required parameter."
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="Neplatný formát UUID" %}
```javascript
{
    "status": "error",
    "message": "Neplatný formát UUID."
}
```
{% endswagger-response %}

{% swagger-response status="401: Unauthorized" description="" %}
```javascript
{
    // Response
}
```
{% endswagger-response %}

{% swagger-response status="403: Forbidden" description="" %}
```javascript
{
    // Response
}
```
{% endswagger-response %}
{% endswagger %}
