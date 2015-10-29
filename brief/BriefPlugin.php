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
        return '0.1.1';
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
            'trigger_section'    =>    array(AttributeType::Mixed,    'default' => ''),
            'user_group'        =>    array(AttributeType::Mixed,    'default' => ''),
            'email_body'        =>    array(AttributeType::Mixed,    'default' => '<h1>Hi there!</h1><p>An entry has been updated</p>'),
            'frontend_link'        =>    array(AttributeType::Bool,    'default' => true),
            'backend_link'        =>    array(AttributeType::Bool,    'default' => true),
        );
    }

    public function getSettingsHtml()
    {
        $currentSections = $this->_getSectionNames();

        $currentGroups = $this->_getUserGroups();

        return craft()->templates->render('brief/settings', array('settings' => $this->getSettings(), 'sections' => $currentSections, 'groups' => $currentGroups));
    }

    public function prepSettings($settings)
    {
        return $settings;
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
    
            $settings = craft()->plugins->getPlugin('brief')->getSettings();
            
            $sectionId = $event->params['entry']->sectionId;
            
            if ($sectionId == $settings->trigger_section) {
    
                $sectionTitle = $this->_getSectionTitle($sectionId);

                // Criteria is basically a 'return elements that match this'
                $user_criteria = craft()->elements->getCriteria(ElementType::User);

                $user_criteria->groupId = $settings->user_group;

                $users = $user_criteria->find();
                
                // Build links for below the email body
                $isEnabled = ($event->params['entry']->enabled) ? 'enabled' : 'disabled';
                $body_links = '<strong>This entry is '.$isEnabled.'</strong><br>';
                if($settings->frontend_link)
                {
                    $body_links .= '<a href="'.$this->_getPageUrl($event->params['entry']->id).'">View on website</a>';
                }
                if($settings->backend_link)
                {
                    if($settings->frontend_link)$body_links .= ' / ';
                    $body_links .= '<a href="'.$this->_getCmsUrl($event->params['entry']->id).'">Edit in CMS</a>';
                }
                
                $body = '<div style="font-family: Helvetica, Arial, sans-serif; color: #222;">'
                        . $settings->email_body
                        . '<hr />'
                        . $body_links
                        . '</div>';
                
                foreach ($users as $user)
                {
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
    
    // return an entry from the entry_id
    private function _getElementFromId($entry_id)
    {
        // Get element from id
        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->id = $entry_id;
        return $criteria->first();
    }

    /**
     * Fetches the page URL via the Element Criteria functionality.
     *
     * @method _getPageUrl
     * @param string $id The entry id.
     * @return string The entry's URL.
     */
    private function _getPageUrl($id)
    {
        $entry = $this->_getElementFromId($id);

        return $entry ? $entry->getUrl() : false;
    }
    
    /**
     * Generates a link to the entry in the backend.
     *
     * @method _getCmsUrl
     * @param string $id The entry id.
     * @return string The entry URL in the CMS.
     */
    private function _getCmsUrl($id)
    {
        $entry = $this->_getElementFromId($id);
        
        return $entry ? $entry->getCpEditUrl() : false;
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

    /**
     * Get all section IDs and names.
     *
     * @method _getSectionNames
     * @return array Section IDs and Names as $id => $name
     */
    private function _getSectionNames()
    {
        $query = craft()->sections->getAllSections();

        foreach ($query as $object) {
            $sections[$object->id] = $object->name;
        }

        return $sections;
    }

    /**
     * Get all user groups.
     *
     * @method _getUserGroups
     * @return array User Group IDs and Names as $id => $name
     */
    private function _getUserGroups()
    {
        $query = craft()->db->createCommand()
            ->select('id, name')
            ->from('usergroups')
            ->queryAll();

        foreach ($query as $row) {
            $groups[$row['id']] = $row['name'];
        }

        return $groups;
    }
}
