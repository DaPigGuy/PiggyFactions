# PiggyFactions [![Poggit-CI](https://poggit.pmmp.io/shield.dl/PiggyFactions)](https://poggit.pmmp.io/p/PiggyFactions) [![Discord](https://img.shields.io/discord/330850307607363585?logo=discord)](https://discord.gg/qmnDsSD)

PiggyFactions is an open-sourced factions plugin for [PocketMine-MP](https://github.com/pmmp/PocketMine-MP).

## Prerequisites
* Basic knowledge on how to install plugins from Poggit Releases and/or Poggit CI
* PMMP 3.2.0 or greater
<!-- Not quite a requirement yet :l
* Economy type supported by libPiggyEconomy:
  * [EconomyAPI](https://github.com/onebone/EconomyS/tree/3.x/EconomyAPI) by onebone
  * [MultiEconomy](https://github.com/TwistedAsylumMC/MultiEconomy) by TwistedAsylumMC
  * PMMP Player EXP
-->

## Features
| Feature | PiggyFactions | FactionsPro |
| --- | --- | --- |
| PiggyCE Integration | ✔ | ❌ |
| Hierarchy/HRKChat Integration | ✔ | ❌ |
| Saves Players by UUID | ✔ | ❌ |
| Per Faction Permissions | ✔ | ❌ |
| SQLite3 Support | ✔ | ✔ |
| MySQL Support | ✔ | ❌ |
| Async Queries | ✔ | ❌ |
| Command Autocomplete | ✔ | ❌ |
| Form UI | ✔ | ❌ |
| Multi-Language Support | ✔ | ❌ |

## Installation & Setup
1. Install the plugin from Poggit.
2. (Optional) Setup the data provider that PiggyFactions will be using. By default, PiggyFactions will use SQLite3 which requires no additional setup. If you would like to use MySQL instead, change database.type from sqlite to mysql & enter your MySQL credentials under database.mysql. 
3. (Optional) You may configure certain faction features in the `config.yml` file.
3. (Optional) You may configure messages in the `messages.yml` file.
4. (Optional) You may want to setup safezones & warzones
   1. Run the command `/f admin`
   2. Create a faction (preferably named Safezone/Warzone)
   3. Set the appropriate flags (either safezone/warzone) with `/f flag`
   4. Claim chunks for their respective types
   5. Leave the faction
5. You're done!

## Commands
| Command | Description | Permissions | Aliases |
| --- | --- | --- | --- |
| `/f` | PiggyFactions main command | `piggyfactions.command.faction.use` |
| `/f admin` | Toggle admin mode | `piggyfactions.command.faction.admin` |
| `/f ally <faction>` | Ally with other factions | `piggyfactions.command.faction.ally` |
| `/f allychat` | Toggle ally chat | `piggyfactions.command.faction.allychat` | `/f ac` |
| `/f ban <player>` | Ban a member from your faction | `piggyfactions.command.faction.ban` |
| `/f chat` | Toggle faction chat | `piggyfactions.command.faction.chat` | `/f c` |
| `/f claim [auto]` | Claim a chunk | `piggyfactions.command.faction.claim` |
| `/f create <name>` | Create a faction | `piggyfactions.command.faction.create` |
| `/f description <description>` | Set faction description | `piggyfactions.command.faction.description` | `/f desc` |
| `/f demote <player>` | Demote a faction member | `piggyfactions.command.faction.demote` |
| `/f disband` | Disband your faction | `piggyfactions.command.faction.disband` |
| `/f enemy <faction>` | Mark faction as an enemy | `piggyfactions.command.faction.enemy` |
| `/f flag <flag>` | Manage faction flags | `piggyfactions.command.faction.flag` |
| `/f home` | Teleport to faction home | `piggyfactions.command.faction.home` |
| `/f info [faction]` | Display faction info | `piggyfactions.command.faction.info` |
| `/f invite <player>` | Invite a player to your faction | `piggyfactions.command.faction.invite` |
| `/f join <faction>` | Join a faction | `piggyfactions.command.faction.join` |
| `/f kick <player>` | Kick a member from your faction | `piggyfactions.command.faction.kick` |
| `/f leader <player>` | Transfer leadership of your faction | `piggyfactions.command.faction.leader` |
| `/f leave` | Leave your faction | `piggyfactions.command.faction.leave` |
| `/f map` | View map of area | `piggyfactions.command.faction.map` |
| `/f motd <motd>` | Set faction MOTD | `piggyfactions.command.faction.motd` |
| `/f name <name>` | Rename your faction | `piggyfactions.command.faction.name` |
| `/f neutral <faction>` | Reset relation with another faction | `piggyfactions.command.faction.neutral` |
| `/f permission <role> <permission> [value]` | Set faction role permissions | `piggyfactions.command.faction.permission` | `/f perms`
| `/f promote <player>` | Promote a faction member | `piggyfactions.command.faction.promote` |
| `/f seechunk` | Toggle chunk visualizer | `piggyfactions.command.faction.seechunk` | `/f sc` |
| `/f sethome` | Set faction home | `piggyfactions.command.faction.sethome` |
| `/f top [page]` | Display top factions | `piggyfactions.command.faction.top` |
| `/f truce <faction>` | Truce with other factions | `piggyfactions.command.faction.truce` |
| `/f unally <faction>` | End faction alliance | `piggyfactions.command.faction.unally` |
| `/f unban <player>` | Unban a member from your faction | `piggyfactions.command.faction.unban` |
| `/f unclaim [all]` | Unclaim a chunk | `piggyfactions.command.faction.unclaim` |

## Permissions
| Permissions | Description | Default |
| --- | --- | --- |
| `piggyfactions` | Allow usage of all PiggyFactions features | `op` |
| `piggyfactions.command` | Allow usage of all PiggyFactions commands | `op`|
| `piggyfactions.command.faction` | Allow usage of /f subcommands | `op` |
| `piggyfactions.command.faction.use` | Allow usage of /f | `true` |
| `piggyfactions.command.faction.admin` | Allow usage of /f admin | `op` |
| `piggyfactions.command.faction.ally` | Allow usage of /f ally | `true` |
| `piggyfactions.command.faction.allychat` | Allow usage of /f allychat | `true` |
| `piggyfactions.command.faction.ban` | Allow usage of /f ban | `true` |
| `piggyfactions.command.faction.chat` | Allow usage of /f chat | `true` |
| `piggyfactions.command.faction.claim` | Allow usage of /f claim | `true` |
| `piggyfactions.command.faction.create` | Allow usage of /f create | `true` |
| `piggyfactions.command.faction.demote` | Allow usage of /f demote | `true` |
| `piggyfactions.command.faction.description` | Allow usage of /f description | `true` |
| `piggyfactions.command.faction.home` | Allow usage of /f home | `true` |
| `piggyfactions.command.faction.info` | Allow usage of /f info | `true` |
| `piggyfactions.command.faction.invite` | Allow usage of /f invite | `true` |
| `piggyfactions.command.faction.join` | Allow usage of /f join | `true` |
| `piggyfactions.command.faction.kick` | Allow usage of /f kick | `true` |
| `piggyfactions.command.faction.leader` | Allow usage of /f leader | `true` |
| `piggyfactions.command.faction.leave` | Allow usage of /f leave | `true` |
| `piggyfactions.command.faction.map` | Allow usage of /f map | `true` |
| `piggyfactions.command.faction.motd` | Allow usage of /f motd | `true` |
| `piggyfactions.command.faction.name` | Allow usage of /f name | `true` |
| `piggyfactions.command.faction.neutral` | Allow usage of /f neutral | `true` |
| `piggyfactions.command.faction.permission` | Allow usage of /f permission | `true` |
| `piggyfactions.command.faction.promote` | Allow usage of /f promote | `true` |
| `piggyfactions.command.faction.seechunk` | Allow usage of /f seechunk | `true` |
| `piggyfactions.command.faction.sethome` | Allow usage of /f sethome | `true` |
| `piggyfactions.command.faction.unally` | Allow usage of /f unally | `true` |
| `piggyfactions.command.faction.unban` | Allow usage of /f unban | `true` |
| `piggyfactions.command.faction.unclaim` | Allow usage of /f unclaim | `true` |

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
* We are using the following virions: [Commando](https://github.com/CortexPE/Commando), [libasynql](https://github.com/poggit/libasynql), [libFormAPI](https://github.com/jojoe77777/FormAPI), and [libPiggyEconomy](https://github.com/DaPigGuy/libPiggyEconomy).
    * **Unless you know what you are doing, use the pre-compiled phar from [Poggit-CI](https://poggit.pmmp.io/ci/DaPigGuy/PiggyFactions/~) and not GitHub.**
    * If you wish to run it via source, check out [DEVirion](https://github.com/poggit/devirion).

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