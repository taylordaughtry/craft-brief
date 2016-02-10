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
		return '1.2.1';
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
		$defaultSubject = 'New entry for ' . craft()->getSiteName();

		return array(
			'trigger_section' => array(AttributeType::Mixed, 'default' => ''),
			'user_group' => array(AttributeType::Mixed, 'default' => ''),
			'slack_webhook' => array(AttributeType::String, 'default' => ''),
			'subject' => array(AttributeType::Mixed, 'default' => $defaultSubject),
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
			$sectionId = $event->params['entry']->sectionId;

			foreach (craft()->userGroups->getAllGroups() as $group) {
				$groupId = $group->id;

				$permission = 'getnotifications:' . $sectionId;

				if (craft()->userPermissions->doesGroupHavePermission($groupId, $permission)) {

					craft()->brief->notifyUsers($event->params['entry'], $groupId);
				}
			}
		});
	}

	public function onAfterInstall()
	{
		craft()->request->redirect(UrlHelper::getCpUrl('brief/welcome'));
	}

	public function registerUserPermissions()
	{
		$sections = craft()->brief->getSections();

		$data = [];

		foreach ($sections as $key => $value) {
			$data['getnotifications:' . $key] = [
				'label' => 'Recieves ' . $value . ' Notifications'
			];
		}

		return $data;
	}
}
