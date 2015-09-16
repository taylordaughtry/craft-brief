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

    /**
     * When an entry is saved, this checks to see if it's the section that we
     * should be notifying users about. If it is, it sends the email. If not,
     * then this plugin is ignored and the hook continues.
     *
     * @method init
     * @return boolean
     * @todo style notification email
     * @todo put table HTML in HEREDOCS
     */
    public function init()
    {
    	parent::init();

    	craft()->on('entries.SaveEntry', function(Event $event) {

		if ($event->params['entry']->sectionId == 2) {

			$sectionId = $event->params['entry']->sectionId;

			$sectionTitle = $this->_getSectionTitle($sectionId);

			$entryUrl = $this->_getPageUrl($event->params['entry']->slug);

    			// Criteria is basically a 'return elements that match this'
    			$user_criteria = craft()->elements->getCriteria(ElementType::User);

    			$user_criteria->group_id = '1';

    			$users = $user_criteria->find();

    			$body = '<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#FFF" style="background: #FFF; width: 100%;">	<tr>		<td height="36" style="font-size: 0; line-height: 0">&nbsp;</td>	</tr>	<tr>		<td width="100%">		<span style="font-size: 24px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #444; font-weight: bold;">Hey, There!</span><br>		</td>	</tr>	<tr>		<td height="48" style="font-size: 0; line-height: 0">&nbsp;</td>	</tr>	<tr>		<td>		<span style="font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #444;">A new entry was just added in the ' . $sectionTitle . ' channel. <a href="' . $entryUrl . '" style="font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #444;text-decoration: underline;">Take a look.</a></span><br>		</td>	</tr>	<tr>		<td height="36" style="font-size: 0; line-height: 0">&nbsp;</td>	</tr>	<tr>		<td style="border-top: 1px solid #DDD;" width="225">		<span style="font-size: 12px; line-height: 24px; font-family: arial, sans-serif; color: #888;">Sent via <a href="#" style="font-size: 12px; line-height: 24px; font-family: arial, sans-serif; color: #888; text-decoration: underline;">Brief</a></span>		</td>	</tr></table>';

    			foreach ($users as $user) {
    				$email = new EmailModel();

				$email->toEmail = $user->email;

				$email->subject = 'New Entry in ' . $sectionTitle . '.';

				$email->body    = $body;

				craft()->email->sendEmail($email);
    			}
    			return true;
    		}
    		return false;
    	});
    }

    /**
     * Fetches the page URL via the Element Criteria functionality.
     *
     * @method _getPageUrl
     * @param string $slug The entry slug.
     * @return string The entry's URL.
     */
    private function _getPageUrl($slug)
    {
    	$criteria = craft()->elements->getCriteria(ElementType::Entry);

    	$criteria->slug = $slug;

    	$entry = $criteria->first();

    	return $entry ? $entry->getUrl() : false;
    }

    /**
     * Gets the section's title with the ID.
     *
     * @method _getSectionTitle
     * @param int $sectionId The section's ID.
     * @return string The section's title.
     */
    private function _getSectionTitle($sectionId)
    {
    	return craft()->sections->getSectionById($sectionId)->name;
    }
}