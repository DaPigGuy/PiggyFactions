![PiggyFactions Banner](https://raw.githubusercontent.com/Aericio/piggydocs-rtd/master/source/_static/img/piggyfactions/banner.png)

# PiggyFactions [![Poggit-CI](https://poggit.pmmp.io/shield.dl/PiggyFactions)](https://poggit.pmmp.io/p/PiggyFactions) [![Discord](https://img.shields.io/discord/330850307607363585?logo=discord)](https://discord.gg/qmnDsSD)

PiggyFactions is an open-sourced factions plugin for [PocketMine-MP](https://github.com/pmmp/PocketMine-MP).

## Documentation
*Documentation is currently not up to date as of version 2.0.0*
* PiggyFaction's documentation is available at [piggydocs-rtd](https://rtdx.aericio.net/en/latest/plugins/piggyfactions/index.html).
  * [Prerequisites](https://rtdx.aericio.net/en/latest/plugins/piggyfactions/docs/prerequisites.html)
  * [Quick Start Guide](https://rtdx.aericio.net/en/latest/plugins/piggyfactions/docs/quickstart.html)
  * [Commands & Permissions](https://rtdx.aericio.net/en/latest/plugins/piggyfactions/docs/commands-and-permissions.html)
  * [Functionality](https://rtdx.aericio.net/en/latest/plugins/piggyfactions/docs/functionality.html)
    * How do I overclaim other factions? How does power work? --> Functionality

## Addons
| Plugin | Description | Installation |
|-|-|:-:|
| [![Download] PiggyCustomEnchants](https://poggit.pmmp.io/p/PiggyCustomEnchants) | AllyChecks Integration | Automatically enabled. |
| [![Download] HRKChat](https://poggit.pmmp.io/ci/CortexPE/HRKChat) | Chat Integration | Requires [Hierarchy](https://poggit.pmmp.io/ci/CortexPE/Hierarchy). [Additional setup required](https://rtdx.aericio.net/en/latest/plugins/piggyfactions/docs/quickstart.html#hrkchat). |
| [![Download] PureChat-PiggyFactions](https://github.com/Heisenburger69/PureChat/releases/download/2.0.0/PureChat-PiggyFactions_v2.0.0.phar) | Chat Integration | Requires [PurePerms](https://poggit.pmmp.io/ci/Heisenburger69/PureChat). [Additional setup required](https://rtdx.aericio.net/en/latest/plugins/piggyfactions/docs/quickstart.html#purechat). |
| [![Download] ScoreHud v5](https://poggit.pmmp.io/p/ScoreHud/5.2.0) | Scoreboard Integration | Requires [Addon Script](https://gist.github.com/DaPigGuy/07442f8b98a70e5973a528e4516e35d1). See [installation steps](https://github.com/Ifera/ScoreHud/tree/v5#how-to-use-addons) and [tags](https://rtdx.aericio.net/en/latest/plugins/piggyfactions/docs/addons.html#scorehud-v5-legacy). |
| [![Download] ScoreHud v6](https://poggit.pmmp.io/p/ScoreHud) | Scoreboard Integration (recommended) | Automatically enabled. See [tags](https://rtdx.aericio.net/en/latest/plugins/piggyfactions/docs/addons.html#scorehud-v6). |
| [![Download] EconomyAPI](https://poggit.pmmp.io/p/EconomyAPI) | Economy Integration | [Additional setup required](https://rtdx.aericio.net/en/latest/plugins/piggyfactions/docs/further-configuration.html#economy). |
| [![Download] MultiEconomy](https://poggit.pmmp.io/p/MultiEconomy) | Economy Integration | [Additional setup required](https://rtdx.aericio.net/en/latest/plugins/piggyfactions/docs/further-configuration.html#economy). |

[Download]: https://i.imgur.com/PnWVUhK.png

## Features
| Feature | PiggyFactions | SimpleFactions | FactionsPro |
|-|:-:|:-:|:-:|
| PiggyCE Integration | ✔ | ❌ | ❌ |
| Hierarchy/HRKChat Integration | ✔ | ❌ | ❌ |
| Economy Integration | ✔ | ✔ | ❌ |
| ScoreHud Integration (v5 & v6) | ✔ | ❌ | ❌ |
| Saves Players by UUID | ✔ | ❌ | ❌ |
| Per Faction Permissions | ✔ | ❌ | ❌ |
| SQLite3 Support | ✔ | ✔ | ✔ |
| MySQL Support | ✔ | ✔ | ❌ |
| Asynchronous Database I/O | ✔ | ✔ | ❌ |
| SQL Injection Protection | ✔ | ❌ | ❌ |
| Command Autocomplete | ✔ | ❌ | ❌ |
| Form UI | ✔ | ❌ | ❌ |
| Multi-Language Support | ✔ | ✔ | ❌ |
| Developer Friendly | ✔ | ❌ | ❌ |

## Issue Reporting
* If you experience an unexpected non-crash behavior with PiggyFactions, click [here](https://github.com/DaPigGuy/PiggyFactions/issues/new?assignees=DaPigGuy&labels=bug&template=bug_report.md&title=).
* If you experience a crash in PiggyFactions, click [here](https://github.com/DaPigGuy/PiggyFactions/issues/new?assignees=DaPigGuy&labels=bug&template=crash.md&title=).
* If you would like to suggest a feature to be added to PiggyFactions, click [here](https://github.com/DaPigGuy/PiggyFactions/issues/new?assignees=DaPigGuy&labels=suggestion&template=suggestion.md&title=).
* If you require support, please join our discord server [here](https://discord.gg/qmnDsSD).
* Do not file any issues related to outdated API version; we will resolve such issues as soon as possible.
* We do not support any spoons of PocketMine-MP. Anything to do with spoons (Issues or PRs) will be ignored.
  * This includes plugins that modify PocketMine-MP's behavior directly, such as TeaSpoon.

## Additional Information
* We do not support any spoons. Anything to do with spoons (Issues or PRs) will be ignored.
* We are using the following virions: [Commando](https://github.com/DaPigGuy/Commando-4.0.0), [libasynql](https://github.com/poggit/libasynql), [libPiggyEconomy](https://github.com/DaPigGuy/libPiggyEconomy) and [libFormAPI](https://github.com/DaPigGuy/FormAPI-4.0.0).
    * **Unless you know what you are doing, use the pre-compiled phar from [Poggit-CI](https://poggit.pmmp.io/ci/DaPigGuy/PiggyFactions/~) and not GitHub.**
    * If you wish to run it via source, check out [DEVirion](https://github.com/poggit/devirion).

## Translators
* **Chinese (Simplified)** - @Taylarity, @Aericio, TGPNG, prprprprprprpr
* **Chinese (Traditional)** - @Taylarity, @Aericio, TGPNG, prprprprprprpr
<!-- **Dutch** - @KingOfTurkey38 -->
* **French** - Thouv (@adeynes), @ItsMax123, steelfri_031, @superbobby2000
* **German** - @SalmonDE
* **Indonesian** - @MrAshshiddiq, @SillierShark195
<!-- **Korean** - @Nabibobettau -->
<!-- **Romanian** - @Gabitzuu -->
* **Serbian** - yuriiscute53925
* **Spanish** - @UnEnanoMas
<!-- **Turkish** - @KingOfTurkey38 -->

## License
```
   Copyright 2020 DaPigGuy

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

```
"[Castle](https://pixabay.com/images/id-2672317/)" vector graphic used in the [banner](https://raw.githubusercontent.com/DaPigGuy/PiggyFactions/master/resources/img/PiggyFactions-banner.png) and [icon](https://raw.githubusercontent.com/DaPigGuy/PiggyFactions/master/resources/img/PiggyFactions-icon.png) is licensed under the [Pixabay License](https://pixabay.com/service/license/).
