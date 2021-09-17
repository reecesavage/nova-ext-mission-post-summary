# Mission Post Summary - A [Nova](https://anodyne-productions.com/nova) Extension

<p align="center">
  <a href="https://github.com/reecesavage/nova-ext-mission-post-summary/releases/tag/v1.0.1"><img src="https://img.shields.io/badge/Version-v1.0.1-brightgreen.svg"></a>
  <a href="http://www.anodyne-productions.com/nova"><img src="https://img.shields.io/badge/Nova-v2.6.1-orange.svg"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-v5.3.0-blue.svg"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-red.svg"></a>
</p>

This extension provides a summary text field for long mission posts. A TL;DR Extension.

This extension requires:

- Nova 2.6+
- Nova Extension [`jquery`](https://github.com/jonmatterson/nova-ext-jquery)
- Nova Mod [`parser_events`](https://github.com/jonmatterson/nova-mod-parser_events)

## Installation

- Install Required Extensions.
- Copy the entire directory into `applications/extensions/nova_ext_mission_post_summary`.
- Add the following to `application/config/extensions.php`: - Be sure the `jquery` line appears before `nova_ext_mission_post_summary`
```
$config['extensions']['enabled'][] = 'nova_ext_mission_post_summary';
```

### Setup Using Admin Panel - Preferred

- Navigate to your Admin Control Panel
- Choose Mission Post Summary under Manage Extensions
- Create Database Columns by clicking "Create Column" for each column. Once all columns are added the message "All expected columns found in the database" will appear.

Installation is now complete!

### Manual Setup - If not using the method above.

- Run the following commands on your MySQL database:

```
ALTER TABLE nova_posts ADD COLUMN nova_ext_mission_post_summary TEXT NULL DEFAULT NULL;
ALTER TABLE nova_missions ADD COLUMN mission_ext_mission_post_summary_enable int(11) DEFAULT 0;
```

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

Copyright (c) 2021 Reece Savage.

This module is open-source software licensed under the **MIT License**. The full text of the license may be found in the `LICENSE` file.
