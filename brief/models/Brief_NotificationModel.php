<?php
namespace Craft;

class Brief_NotificationModel extends BaseModel
{
	protected function defineAttributes()
	{
		return array(
			'section' => AttributeType::Number,
			'uri' => AttributeType::String,
			'subject' => AttributeType::Mixed,
			'body' => AttributeType::Mixed,
		);
	}
}