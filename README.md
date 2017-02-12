![Brief](http://i.imgur.com/EIHmVQM.png)

![Craft](https://img.shields.io/badge/craft-v2.6-orange.svg)

Brief adds notification functionality to your Craft site. It's maintained by [Taylor Daughtry](https://github.com/taylordaughtry).


## Features
- Control who gets notifications with Craft's native User Groups
- Customize the email's suject with dynamic titles
- Select who gets notifications for any section, or just a few sections
- Notify Slack channels when a section is updated with new or edited entries

### Installation

To install Brief, just follow these steps:

1. Upload the brief/ folder to your craft/plugins/ folder.
2. Go to Settings > Plugins from your Craft control panel and enable the Brief plugin.
3. Click the gear icon next to the uninstall button, and add your settings.
4. Click 'Save'. You're done!

Any users in your selected group will now receive email notifications when a new
entry is added to your section. If you add new users to that group, Brief will
add them to the notification list automatically; you don't need to do anything
else.

### How does it work?

Every time an entry is saved, Brief checks whether it's in the section you've specified. If it is, and the entry is enabled, Brief sends a notification email to the User Group you've selected. Note that it's a very light check to see if the saved entry is in your section; you won't have any performance issues if you're saving tons of entries everyday.


## Usage

In Brief's settings, specificy which section you'd like to be notified about. For example, if you'd like to be notified when an entry is updated or deleted in the 'News' section, you'd select News from the dropdown.

Also be sure and select a User Group. If you don't have any, Brief will ask you to go create one. If you don't have user groups and still try to use Brief, it won't send any emails so you don't inadvertently email every user on the site.

## Custom Email Templates

Brief supports custom email templates. If you'd like to use one, just specify the template in Brief's settings, and Brief will render your template and send it.

You'll have access to the entry variable within any template you choose to render.

### Slack Integration

Brief has Slack integration. Just paste your Slack webhook into Brief's
settings, and we'll notify your selected Slack Channel whenever there's an
update.

You can get a webhook for your team by going to your team's Apps & Custom Integrations page and creating a Custom Integration (Incoming Webhook). Select the channel you'd like to post to, then copy the Webhook URL provided.

Paste that URL into Brief's settings, and it'll let you know when entries are added/updated in your specified section.

## Feature Requests & Issues

If you need a feature, let me know and I'll add it as soon as reasonably possible. Brief is actively maintained, and I accept relevant, feature-adding pull requests. If you encounter any issues, please open an issue and I'll work with you and patch the problem. Thanks!

## Feature Roadmap
- Add email templates from ThemeForest, CodeCanyon, and
elsewhere that work with Brief automatically
- Specify whether you'd like notifications for *any* entry, or just new ones.
