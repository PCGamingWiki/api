PCGamingWiki Redirect API
===

This is PCGamingWiki's custom redirect API which redirects incoming users using any registered GOG ID, Steam App ID, or WineHQ ID to the relevant PCGamingWiki page.

If you instead want to know how to interface with PCGamingWiki's backend database to look up or retrieve data from game articles, please see [the API page](https://www.pcgamingwiki.com/wiki/PCGamingWiki:API) over on the website which details how to use the [MediaWiki API](https://www.mediawiki.org/wiki/API:Main_page) and relevant [Cargo API actions](https://www.mediawiki.org/wiki/Extension:Cargo) to do so.


Redirect rules
---

Use the appropriate identifiers for the platform.

| Platform | Identifier                                                    | Base URL | Example |
|----------|---------------------------------------------------------------|----------|---------|
| Steam    | [AppID](https://partner.steamgames.com/doc/store/application) | https://pcgamingwiki.com/api/appid.php?appid= | https://pcgamingwiki.com/api/appid.php?appid=920210 |
| GOG      | [ProductID](https://docs.gog.com/bc-product-details/)         | http://pcgamingwiki.com/api/gog.php?page= | http://pcgamingwiki.com/api/gog.php?page=1094900565 |
| WineHQ   | PageID                                                        | http://pcgamingwiki.com/api/winehq.php?appid=  | http://pcgamingwiki.com/api/winehq.php?appid=18238  |


**Cheat sheet**

* Steam: the Steam AppID is `920210` for https://store.steampowered.com/app/920210/LEGO_Star_Wars_The_Skywalker_Saga/
* GOG: the GOG ProductID is not a part of any store links, so either one has to view the page source for the store page (for e.g. https://www.gog.com/game/rimworld ) and perform a search for `ProductId`, or retrieve it using the unaffiliated third-party GOG Database website (e.g. https://www.gogdb.org/product/1094900565 ).
* WineHQ: the Page ID is `18238` for https://appdb.winehq.org/objectManager.php?sClass=application&iId=18238
