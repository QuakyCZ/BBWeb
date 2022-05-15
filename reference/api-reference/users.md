# Users

{% hint style="info" %}
**Good to know:** All the methods shown below are synced to an example Swagger file URL and are kept up to date automatically with changes to the API.
{% endhint %}

## User actions

{% swagger method="get" path="/users/account?user_id=int&type=string" baseUrl="https://beastblock.cz/api/v1" summary="Účet podle typu" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="query" name="user_id" required="true" %}
ID webového uživatele
{% endswagger-parameter %}

{% swagger-parameter in="query" name="type" required="true" %}
Typ účtu - minecraft / discord
{% endswagger-parameter %}

{% swagger-response status="200: OK" description="Účet byl nalezen" %}
```javascript
{
    "status": "ok",
    "account": {
        //minecraft:
        "uuid": "uuid",
        "nick": "nick"
        //discord
        "discord_id": "dc_id"
    }
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="Typ účtu neexistuje" %}
```javascript
{
    "status": "error",
    "message": "Neexistující typ účtu"
}
```
{% endswagger-response %}

{% swagger-response status="404: Not Found" description="Účet nebyl nalezen - webový / požadovaný" %}
```javascript
{
    // Response
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

{% swagger method="post" path="/users/connect" baseUrl="https://www.beastblock.cz/api/v1" summary="Propojení účtu" %}
{% swagger-description %}
Tělo musí být ve formátu JSON.

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

{% swagger method="post" path="/users/disconnect" baseUrl="https://beastblock.cz/api/v1" summary="Odpojení účtu" %}
{% swagger-description %}
Odpojí účet od serveru podle typu.
{% endswagger-description %}

{% swagger-parameter in="body" name="user_id" type="int" %}
id uživatele webu
{% endswagger-parameter %}

{% swagger-parameter in="body" name="type" %}
Typ propojení minecraft/discord
{% endswagger-parameter %}

{% swagger-response status="200: OK" description="" %}
```javascript
{
    // Response
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="user_id není int" %}
```javascript
{
    "status": "error",
    "scope": "api-error",
    "message": "Parametr user_id musí být celé číslo"
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="Neplatný typ propojení" %}
```javascript
{
    "status": "error",
    "scope": "api-error",
    "message": "Neplatný typ propojení"
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="Účet není propojen" %}
```javascript
{
    "status": "error",
    "scope": "logical-error",
    "message": "Tento účet není propojen s %type% serverem."
}
```
{% endswagger-response %}

{% swagger-response status="404: Not Found" description="Uživatel nebyl nalezen" %}
```javascript
{
    "status": "error",
    "scope": "logical-error",
    "message": "Uživatel nebyl nalezen."
}
```
{% endswagger-response %}

{% swagger-response status="500: Internal Server Error" description="Neznámá chyba" %}
```javascript
{
    "status": "error",
    "scope": "server-error",
    "message": "Při zpracování požadavku nastala neznámá chyba."
}
```
{% endswagger-response %}
{% endswagger %}
