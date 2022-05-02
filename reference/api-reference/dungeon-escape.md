# Dungeon Escape

## Hráč

{% swagger baseUrl="https://beastblock.cz/api/v1/dungeon-escape" method="get" path="/player" summary="Hráč podle UUID nebo Nicku" %}
{% swagger-description %}
Sends basic data of a player. One of the parameters must be provided.
{% endswagger-description %}

{% swagger-parameter in="query" name="nick" %}
Nick hráče
{% endswagger-parameter %}

{% swagger-parameter in="query" name="uuid" %}
UUID hráče
{% endswagger-parameter %}

{% swagger-response status="200" description="Hráč byl nalezen" %}
```javascript
{
    "status": "ok",
    "player": {
        "id": 1,
        "name": "QuakyCZ",
        "uuid": "UUID",
        "statisticsId": 1
    }
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="Nick nebo UUID musí být uvedeno" %}
```javascript
{
    "status": "error",
    "message": "Name/UUID must be provided"
}
```
{% endswagger-response %}

{% swagger-response status="404: Not Found" description="Hráč nebyl nalezen" %}
```javascript
{
    "status": "error",
    "message": "Player was not found"
}
```
{% endswagger-response %}
{% endswagger %}

## Statistiky

{% swagger method="get" path="/statistics/<id>" baseUrl="https://beastblock.cz/api/v1/dungeon-escape" summary="Statistiky podle ID (WIP)" %}
{% swagger-description %}
You can obtain statistics ID from #GetPlayer method
{% endswagger-description %}

{% swagger-parameter in="path" type="Int" required="true" %}
ID statistik
{% endswagger-parameter %}

{% swagger-response status="200: OK" description="Statistiky byly nalezeny" %}
```javascript
{
  "status": "ok",
  "statistics": {
    "id": 1,
    "normalGamesPlayed": 11,
    "coopGamesPlayed": 0,
    "battleRoyaleGamesPlayed": 4,
    "singleWins": 0,
    "coopWins": 0,
    "battleRoyaleWins": 0,
    "deaths": 3,
    "mobKills": 158,
    "bossKills": 4,
    "playerKills": 0,
    "highestScore": 2643,
    "totalDealtDamage": 5929.835649445653,
    "totalTakenDamage": 401.82054060697556
  }
}
```
{% endswagger-response %}

{% swagger-response status="400: Bad Request" description="ID nebylo specifikováno" %}
```javascript
{
  "status": "error"
  "message": "ID must be specified."
}
```
{% endswagger-response %}

{% swagger-response status="404: Not Found" description="Statistiky nebyly nalezeny" %}
```javascript
{
    "status": "error"
    "message": "Statistics were not found."
}
```
{% endswagger-response %}
{% endswagger %}
