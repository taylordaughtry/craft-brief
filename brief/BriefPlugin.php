<?php

namespace Craft;

Class BriefPlugin extends BasePlugin
{
	public function getName()
	{
		 return Craft::t('Brief');
	}

	public function getVersion()
	{
		return '0.1.0';
	}

	public function getDeveloper()
	{
		return 'Taylor Daughtry';
	}

	public function getDeveloperUrl()
	{
		return 'http://github.com/taylordaughtry';
	}

	public function getPluginName()
	{
		return 'Brief';
	}

	public function getPluginUrl()
	{
		return 'https://github.com/taylordaughtry/brief';
	}

	public function defineSettings()
	{
		return array(
			'test' => array(AttributeType::String, 'default' => 'value'),
			'trigger_section' => array(AttributeType::Mixed, 'default' => ''),
			'user_group' => array(AttributeType::Mixed, 'default' => ''),
			'slack_webhook' => array(AttributeType::String, 'default' => ''),
		);
	}

	public function getSettingsHtml()
	{
		return craft()
			->templates
			->render('brief/settings', array(
				'settings' => $this->getSettings(),
				'sections' => craft()->brief->getSections(),
				'groups' => craft()->brief->getGroups()
			)
		);
	}

	public function prepSettings($settings)
	{
		return $settings;
	}

	/**
	 * When an entry is saved, this checks to see if it's the section that we
	 * should be notifying users about. If it is, Brief sends an email. If not,
	 * then this plugin is ignored and the hook continues.
	 *
	 * @return void
	 */
	public function init()
	{
		parent::init();

		craft()->on('entries.SaveEntry', function(Event $event) {

			$triggerSect = $this->getSettings()->trigger_section;

			if ($event->params['entry']->sectionId === $triggerSect) {
				craft()->brief->notifyUsers($event->params['entry']);
			}

		});
	}
}
