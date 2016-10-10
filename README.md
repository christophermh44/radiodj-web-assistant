# RadioDJ Web Assistant (beta)

## Presentation

RadioDJ Web Assistant is a tool that allows you to:

* Create your shows guides from templates and song queries that you define
* Manage your songs tags and cue points
* Upload your audio podcasts to Mixcloud with automatic tracklist generation

## Prerequisites

To make RadioDJ Web Assistant run, you need to:

* Give access to the RadioDJ database for RadioDJ Web Assistant
* Give FTP access to your songs files (all songs files need to be in one accessible folder)

**It is strongly recommended to put this app behind an authentication system (like Apache basic auth or a VPN access), because it does not contain any logon form for now.**

## Some TODOs I will probably implement later

* Add documentation
* A better playlist generator for RadioDJ
* More song picking modes (least/most recently played, random pick, …)
* Create user account system and plugin system (then make Mixcloud uploads as a plugin) with right managments
* Full song managment from RadioDJ Web Assistant (if it becomes possible to use a kind of RadioDJ service to add or remove songs)

## Notice

Please note that this tool does not aim to replace RadioDJ track managers and playlist builders. It's just here to allow radio producers to manage their songs without having to access to RadioDJ application directly. It can be useful when RadioDJ is hosted on a small configuration VPS (the cue points editor needs a lot of RAM and CPU, so the stream can scramble) or if you want to give some access to other users without letting them accessing all RadioDJ settings (for now, RadioDJ plans to add an account system in the next version of the software).
