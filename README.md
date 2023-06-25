# Mission Post Summary - A [Nova](https://anodyne-productions.com/nova) Extension

<p align="center">
  <a href="https://github.com/reecesavage/nova-ext-mission-post-summary/releases/tag/v1.1.0"><img src="https://img.shields.io/badge/Version-v1.1.0-brightgreen.svg"></a>
  <a href="http://www.anodyne-productions.com/nova"><img src="https://img.shields.io/badge/Nova-v2.7.5-orange.svg"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-v8.x-blue.svg"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-red.svg"></a>
</p>

This extension provides a summary text field for long mission posts. A TL;DR Extension.

This extension requires:

- Nova 2.7.5+
- Nova Extension [`jquery`](https://github.com/jonmatterson/nova-ext-jquery)
- Nova Mod [`parser_events`](https://github.com/jonmatterson/nova-mod-parser_events)

## Upgrade Considerations
- If upgrading Nova 2.6+ with this Nove Extension already deployed:
- Remove `$config['extensions']['enabled'][] = 'nova_ext_mission_post_summary';` from `application/config/extensions.php` prior to the Nova upgrade.
- After upgrading Nova to 2.7.5+, follow the installation steps below. The database tables still contain your data

## Installation

- Install Required Extensions.
- Copy the entire directory into `applications/extensions/nova_ext_mission_post_summary`.
- Add the following to `application/config/extensions.php`: - Be sure the `jquery` line appears before `nova_ext_mission_post_summary`
```
$config['extensions']['enabled'][] = 'nova_ext_mission_post_summary';
```

### Setup Using Admin Panel

- Navigate to your Admin Control Panel
- Choose Mission Post Summary under Manage Extensions
- Create Database Columns by clicking "Create Column" for each column. Once all columns are added, the message "All expected columns found in the database" will appear.

Installation is now complete!

## Usage

- Create or Edit a mission.
- Check Summary Field Enable.
- Enter other values as normal.
- Click submit.

### Labels and Configuration
The admin control panel for this extension allows the admin to change:
- Labels to suit your games. 
- Enable or disable including the summary field in post emails. 
- The Number of lines to show in the text box when authoring a mission post. Default 5.

## Issues

If you encounter a bug or have a feature request, please report it on GitHub in the issue tracker here: https://github.com/reecesavage/nova-ext-mission-post-summary/issues

## License

Copyright (c) 2023 Reece Savage.

This module is open-source software licensed under the **MIT License**. The full text of the license may be found in the `LICENSE` file.
