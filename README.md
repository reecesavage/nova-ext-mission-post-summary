# Mission Post Summary - A [Nova](https://anodyne-productions.com/nova) Extension

<p align="center">
  <a href="https://github.com/reecesavage/nova-ext-mission-post-summary/releases/tag/v1.2.0"><img src="https://img.shields.io/badge/Version-v1.2.0-brightgreen.svg"></a>
  <a href="http://www.anodyne-productions.com/nova"><img src="https://img.shields.io/badge/Nova-v2.7.5+-orange.svg"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-v8.x-blue.svg"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-red.svg"></a>
</p>

This extension provides a summary text field for long mission posts. A TL;DR Extension.

This extension requires:

- Nova 2.7.5+
- Nova Extension [`jquery`](https://github.com/jonmatterson/nova-ext-jquery)
- Nova Mod [`parser_events`](https://github.com/jonmatterson/nova-mod-parser_events)

## Upgrade Considerations

### Upgrading from a version older than 1.2.0
The feed code injected by older releases of this extension didn't carry version markers. After upgrading the extension files, open the admin Status panel - it will detect the existing `posts()` method in `application/controllers/Feed.php` and offer an **Update Feed Code** button to replace it in place with the new shim form. No manual surgery required.

If anything looks off, the fallback is always to replace `application/controllers/Feed.php` with the stock Nova stub, then click **Install Feed Code** on the admin page.

### Playing nicely with `nova_ext_ordered_mission_posts`
Both extensions can install a shim into `application/controllers/Feed.php`. The Status panel here detects when `nova_ext_ordered_mission_posts` already owns the feed and reports "Handled by Ordered Mission Posts" - in that case no install is needed because the other extension's feed builder already renders the summary. If you'd rather have this extension own the feed, replace `Feed.php` with the stock Nova stub and click **Install Feed Code** here.

### Upgrading Nova
- If upgrading Nova with this Nova Extension already deployed:
- Remove `$config['extensions']['enabled'][] = 'nova_ext_mission_post_summary';` from `application/config/extensions.php` prior to the Nova upgrade.
- After upgrading Nova to 2.7.5+, follow the installation steps below. The database tables still contain your data.

## Installation

- Install Required Extensions.
- Copy the entire directory into `application/extensions/nova_ext_mission_post_summary`.
- Add the following to `application/config/extensions.php` - be sure the `jquery` line appears before `nova_ext_mission_post_summary`:
```
$config['extensions']['enabled'][] = 'nova_ext_mission_post_summary';
```

### Setup Using Admin Panel

- Navigate to your Admin Control Panel.
- Choose **Mission Post Summary** under Manage Extensions.
- The **Status** panel at the top shows the live state of the database columns and the feed code.
- Click **Set Up Database** to add the required columns (one column on `posts`, one on `missions`) in a single click. The button only appears when something is missing; it's safe to re-run.
- Click **Install Feed Code** to inject the summary-aware RSS feed shim into `application/controllers/Feed.php` so `/feed/posts` includes the post summary. If Ordered Mission Posts already owns the feed, this button isn't shown - that extension already handles the summary.

Installation is complete when the Status panel reads "All present" / "Installed and up to date" (or "Handled by Ordered Mission Posts") across the board.

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
