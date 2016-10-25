<?php
/**
 * MailChimp3 plugin - Compains-object
 * not yet implemented
 *
 * @package     jtl_mailchimp3_plugin
 * @author      JTL-Software-GmbH
 * @copyright   2016 JTL-Software-GmbH
 *
 *
 * MailChimp Main-End-Point "Compains"
 * (http://developer.mailchimp.com/documentation/mailchimp/reference/overview/)
 *
 * Sub-EndPoints:
 *
 *  POST   	 /campaigns                                      	 Create a new campaign
 *  GET    	 /campaigns                                      	 Get all campaigns
 *  GET    	 /campaigns/{campaign_id}                        	 Get information about a specific campaign
 *  PATCH  	 /campaigns/{campaign_id}                        	 Update the settings for a campaign
 *  DELETE 	 /campaigns/{campaign_id}                        	 Delete a campaign
 *  POST   	 /campaigns/{campaign_id}/actions/cancel-send    	 Cancel a campaign
 *  POST   	 /campaigns/{campaign_id}/actions/pause          	 Pause an RSS-Driven campaign
 *  POST   	 /campaigns/{campaign_id}/actions/replicate      	 Replicate a campaign
 *  POST   	 /campaigns/{campaign_id}/actions/resume         	 Resume an RSS-Driven campaign
 *  POST   	 /campaigns/{campaign_id}/actions/schedule       	 Schedule a campaign
 *  POST   	 /campaigns/{campaign_id}/actions/send           	 Send a campaign
 *  POST   	 /campaigns/{campaign_id}/actions/test           	 Send a test email
 *  POST   	 /campaigns/{campaign_id}/actions/unschedule     	 Unschedule a campaign
 *  GET    	 /campaigns/{campaign_id}/content                	 Get campaign content
 *  PUT    	 /campaigns/{campaign_id}/content                	 Set campaign content
 *  POST   	 /campaigns/{campaign_id}/feedback               	 Add campaign feedback
 *  GET    	 /campaigns/{campaign_id}/feedback               	 Get feedback about a campaign
 *  GET    	 /campaigns/{campaign_id}/feedback/{feedback_id} 	 Get a specific feedback message
 *  PATCH  	 /campaigns/{campaign_id}/feedback/{feedback_id} 	 Update a campaign feedback message
 *  DELETE 	 /campaigns/{campaign_id}/feedback/{feedback_id} 	 Delete a campaign feedback message
 *  GET    	 /campaigns/{campaign_id}/send-checklist         	 Get the send checklist for a campaign
 *
 */

class MailChimpLists
{
    public $oRestClient = null;

    public function __construct(RestClient $oClient)
    {
        $this->RestClient = $oClient;
    }

}
