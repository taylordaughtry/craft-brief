<?php
namespace Craft;

use Craft\Brief_NotificationModel as Notification;

class BriefService extends BaseApplicationComponent
{
	protected $settings;
	protected $slackUri;
	protected $entryUri;
	protected $sectionName;

	public function __construct()
	{
		$this->settings = craft()->plugins->getPlugin('brief')->getSettings();
		$this->slackUri = $this->settings->slackWebhook;
	}

	public function notifyUsers($entry, $groupId)
	{
		$notification = new Notification;
		$notification->section = $entry->section->name;
		$notification->uri = $entry['uri'];

		if ($this->slackUri) {
			$this->notifySlack($entry);
		}

		foreach ($this->getUsers($groupId) as $user) {
			$email = new EmailModel();
			$email->toEmail = $user->email;

			if ($this->settings->replyTo) {
				$email->replyTo = $this->settings->replyTo;
			}

			// DEV: Generated here to include the receiving user's model.
			$notification->subject = $this->generateSubject($entry, $user);
			$notification->body = $this->generateBody($entry, $user);

			$email->subject = $notification->subject;
			$email->htmlBody = $notification->body;

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

	public function getUsers($groupId)
	{
		$user_criteria = craft()->elements->getCriteria(ElementType::User);

		$user_criteria->groupId = $groupId;

		return $user_criteria->find();
	}

	public function generateSubject($entry, $user)
	{
		$subjectTemplate = base64_decode($this->settings->subject);

		$variables = [
			'section' => $entry->section->name,
			'title' => $entry->title,
			'entry' => $entry,
			'user' => $user
		];

		return craft()->templates->renderString($subjectTemplate, $variables);
	}

	public function generateBody($entry, $user)
	{
		$variables = [
			'siteName' => craft()->getSiteName(),
			'entry' => $entry,
			'user' => $user,
			'cpEditUrl' => UrlHelper::getCpUrl(),
			'sectionTitle' => $entry->section->name,
			'entryUrl' => craft()->getSiteUrl() . $entry->uri,
		];

		if ($this->settings['customTemplate']) {
			craft()->templates->setTemplateMode(TemplateMode::Site);

			$body = craft()->templates->render($this->settings['customTemplate'], $variables);

			craft()->templates->setTemplateMode(TemplateMode::CP);

			return $body;
		}

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
