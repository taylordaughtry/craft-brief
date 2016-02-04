<?php
namespace Craft;

class BriefService extends BaseApplicationComponent
{
	protected $settings;

	public function __construct()
	{
		$this->settings = craft()->plugins->getPlugin('brief')->getSettings();
	}

	public function notifyUsers($entry)
	{
		$body = $this->generateBody($entry);

		$subject = 'New Entry in the ' . $entry->section->name . ' channel';

		foreach ($this->getUsers() as $user) {
			$email = new EmailModel();

			$email->toEmail = $user->email;

			$email->subject = $subject;

			$email->body = $body;

			craft()->email->sendEmail($email);
		}
	}

	public function getSections()
	{
		$query = craft()->sections->getAllSections();

		foreach ($query as $object) {
			$sections[$object->id] = $object->name;
		}

		return $sections;
	}

	public function getGroups()
	{
		$data = craft()->userGroups->getAllGroups();

		foreach ($data as $group) {
			$groups[$group->name] = ucfirst($group->name);
		}

		return $groups;
	}

	public function getUsers()
	{
		$user_criteria = craft()->elements->getCriteria(ElementType::User);

		$user_criteria->group = $this->settings->user_group;

		return $user_criteria->find();
	}

	public function generateBody($entry)
	{
		$variables = [
			'sectionTitle' => $entry->section->name,
			'entryUrl' => craft()->getSiteUrl() . $entry['uri'],
		];

		return craft()->templates->render('brief/notification', $variables);
	}
}