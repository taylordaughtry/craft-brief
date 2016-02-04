![Brief](http://i.imgur.com/eoSbsh5.jpg)

# Brief

![Craft](https://img.shields.io/badge/craft-v2.5-orange.svg)

Brief is a light plugin that allows you to notify a certain user group when
new entries are added to a section.

## Features

This module is in active development. If you need a feature, let me know and I'll add it as soon as reasonably possible. If you encounter any issues, please open an issue and I'll work with you and patch the problem. Thanks!

Brief has Slack integration. Just paste your Slack webhook into Brief's settings, and we'll notify your selected Slack Channel whenever there's an update.

## Feature Roadmap
- Add email templates from ThemeForest, CodeCanyon, and elsewhere that work with Brief automatically
- Easily build custom email templates with Twig, just like any other template.
- Add multiple notification groups for multiple sections
- Specify whether you'd like notifications for *any* entry, or just new ones.

## Usage

1. Install the plugin by putting the `brief` directory in your `craft/plugins`
folder.
2. Go to the plugin settings and select which section you'd like Brief to watch.
3. Select the User Group you'd like to notify about any new entries. If you don't
   have any user groups, Brief will tell you.
4. Click 'Save'. You're done!

Any users in your selected group will now receive email notifications when a new
entry is added to your section. If you add new users to that group, Brief will
add them to the notification list automatically; you don't need to do anything else.
