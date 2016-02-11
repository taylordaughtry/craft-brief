<?php
namespace Craft;

class BriefService extends BaseApplicationComponent
{
	protected $settings;

	protected $slackUri;

	protected $entryUri;

	protected $sectionName;

	public function __construct()
	{
		$this->settings = craft()->plugins->getPlugin('brief')->getSettings();
		$this->slackUri = $this->settings->slack_webhook;
	}

	public function notifyUsers($entry, $groupId)
	{
		$this->sectionName = $entry->section->name;

		$this->entryUri = $entry['uri'];

		if ($this->slackUri) {
			$this->notifySlack($entry);
		}

		$body = $this->generateBody($entry);

		$subjectTemplate = $this->settings->subject;

		$variables = [
			'section' => $entry->section->name,
			'title' => $entry->title,
		];

		$renderedSubject = craft()->templates->renderString($subjectTemplate, $variables);

		foreach ($this->getUsers($groupId) as $user) {
			$email = new EmailModel();

			$email->toEmail = $user->email;

			$email->subject = $renderedSubject;

			$email->htmlBody = $body;

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
		try {
			$data = craft()->userGroups->getAllGroups();
		} catch (\Exception $e) {
			BriefPlugin::log('No User Groups have been set.', LogLevel::Info);

			return false;
		}

		foreach ($data as $group) {
			$groups[$group->name] = ucfirst($group->name);
		}

		return $groups;
		}
	}

	public function getUsers($groupId)
	{
		$user_criteria = craft()->elements->getCriteria(ElementType::User);

		$user_criteria->groupId = $groupId;

		return $user_criteria->find();
	}

	public function generateBody($entry)
	{
		$variables = [
			'siteName' => craft()->getSiteName(),
			'cpEditUrl' => UrlHelper::getCpUrl(),
			'sectionTitle' => $entry->section->name,
			'entryUrl' => craft()->getSiteUrl() . $this->entryUri,
		];

		return craft()->templates->render('brief/email', $variables);
	}

	public function notifySlack($entry)
	{
		$client = new \Guzzle\Http\Client();

	 	$request = $client
			->post($this->slackUri)
			->setPostField('payload',
				json_encode([
					'text' => 'An entry has been added or updated in the ' .
					$this->sectionName . ' channel. <' . craft()->getSiteUrl() .
					$this->entryUri .'|Take a look>.'
				])
			);

		$response = $request->send();

	}
}