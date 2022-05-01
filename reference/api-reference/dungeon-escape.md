# Dungeon Escape

## Get player

{% swagger baseUrl="https://beastblock.cz/api/v1/dungeon-escape" method="get" path="/player" summary="Get player data" %}
{% swagger-description %}
Sends basic data of a player. One of the parameters must be provided.
{% endswagger-description %}

{% swagger-parameter in="query" name="nick" %}
Player nick
{% endswagger-parameter %}

{% swagger-parameter in="query" name="uuid" %}
Player UUID
{% endswagger-parameter %}

{% swagger-response status="200" description="Player was found" %}
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

{% swagger-response status="400: Bad Request" description="name or uuid must be provided" %}
```javascript
{
    "status": "error",
    "message": "Name/UUID must be provided"
}
```
{% endswagger-response %}

{% swagger-response status="404: Not Found" description="" %}
```javascript
{
    "status": "error",
    "message": "Player was not found"
}
```
{% endswagger-response %}
{% endswagger %}

## Get statistics

{% swagger method="get" path="/statistics" baseUrl="https://beastblock.cz/api/v1/dungeon-escape/" summary="Get statistics by ID" %}
{% swagger-description %}
You can obtain statistics ID from #GetPlayer method
{% endswagger-description %}

{% swagger-parameter in="path" type="Int" required="true" %}
Statistics ID
{% endswagger-parameter %}

{% swagger-response status="200: OK" description="Statistics were found" %}
```javascript
{
  "player_statistics": {
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

{% swagger-response status="400: Bad Request" description="If ID was not specified" %}
```javascript
{
  "error": "ID must be specified."
}
```
{% endswagger-response %}

{% swagger-response status="404: Not Found" description="Statistics with provided ID were not found" %}
```javascript
{
    "error": "Statistics were not found."
}
```
{% endswagger-response %}
{% endswagger %}
