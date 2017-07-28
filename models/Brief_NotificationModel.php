<?php
namespace Craft;

class Brief_NotificationModel extends BaseModel
{
	protected function defineAttributes()
	{
		return array(
			'body' => AttributeType::Mixed,
			'section' => AttributeType::Number,
			'subject' => AttributeType::Mixed,
			'uri' => AttributeType::String,
		);
	}
}