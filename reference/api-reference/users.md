# Users

{% hint style="info" %}
**Good to know:** All the methods shown below are synced to an example Swagger file URL and are kept up to date automatically with changes to the API.
{% endhint %}

## User actions

{% swagger method="post" path="/users/connect" baseUrl="https://www.beastblock.cz/api/v1" summary="Tělo requestu musí být ve formátu JSON. " %}
{% swagger-description %}
`{`\
&#x20; `"type": "minecraft",`\
&#x20; `"token": "token",`\
&#x20; `"data": {}`\
`}`\
Minecraft data:

`{`\
&#x20; `"uuid": "uuid hráče",`\
&#x20; `"nick": "nick hráče"`\
`}`\
\
Discord data:\
\
`{`\
&#x20; `"discord_id": "dc_id"`\
`}`
{% endswagger-description %}

{% swagger-parameter in="body" name="type" type="String" %}
Typ propojení - minecraft / discord
{% endswagger-parameter %}

{% swagger-parameter in="body" name="token" %}
Token vygenerovaný na webu
{% endswagger-parameter %}

{% swagger-parameter in="body" name="data" required="true" %}
Nick hráče
{% endswagger-parameter %}

{% swagger-parameter in="header" required="true" name="Authorizaton: Bearer TOKEN" %}
Bearer auth token
{% endswagger-parameter %}

{% swagger-response status="200: OK" description="Účet byl propojen" %}
```javascript
{
    "status": "ok",
    "user_id": "1"
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="Neznámý typ propojení" %}
```javascript
{
    "status": "error",
    "message": "Neznámý typ propojení"
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="UUID je již propojené s webovým účtem" %}
```javascript
{
    "status": "error",
    "message": "Účet je již propojen."
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="Chybí parametr" %}
```javascript
{
    "status": "error",
    "message": "Missing required parameter."
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="Neplatný token" %}
```javascript
{
    "status": "error",
    "message": "Neplatný token"
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

{% swagger-response status="500: Internal Server Error" description="Neznámá chyba" %}
```javascript
{
  "status": "error",
  "message": "Při zpracování požadavku nastala neznámá chyba."
}
```
{% endswagger-response %}
{% endswagger %}