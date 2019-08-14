<?php
/**
 * @category	Wp
 * @package		Wp_Whitepages
 * @author		WhitePages <support@whitepages.com>
 * @copyright	WhitePages Inc. (c) 2013
 *
 * THIS SOFTWARE AND RELATED SERVICE IS PROVIDED ON AN “AS IS” BASIS. TO THE FULLEST EXTENT
 * PERMISSIBLE, WHITEPAGES AND ITS AFFILIATES DISCLAIM ALL WARRANTIES OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, WARRANTIES OF TITLE, NONINFRINGEMENT AND IMPLIED
 * WARRANTIES OF MERCHANTABILITY OR FITNESS FOR A PARTICULAR PURPOSEOR THOSE ARISING FROM
 * COURSE OF DEALING OR USAGE OF TRADE.  WITHOUT LIMITING THE FOREGOING, WHITEPAGES MAKES NO
 * REPRESENTATIONS THAT (I) THE SOFTWARE OR RELATED SERVICE WILL MEET YOUR REQUIREMENTS OR BE
 * ACCURATE, COMPLETE, RELIABLE OR ERROR FREE; (II) THAT THE SOFTWARE OR RELATED SERVICE
 * ALWAYS BE AVAILABLE OR WILL BE UNINTERRUPTED, ACCESSIBLE, TIMELY, RESPONSIVE OR SECURE; OR
 * (III) THAT ANY DEFECTS WILL BE CORRECTED, OR THAT THE SOFTWARE OR RELATED SERVICE WILL BE
 * FREE FROM VIRUSES, “WORMS,” “TROJAN HORSES” OR OTHER HARMFUL PROPERTIES.  YOU EXPRESSLY
 * AGREE THAT USE IS AT YOUR SOLE RISK AND THAT YOU WILL BE SOLELY RESPONSIBLE FOR ANY DAMAGE
 * TO YOUR COMPUTER SYSTEM OR LOSS OF DATA THAT RESULTS FROM AND FOR ANY DISCLOSURE OF
 * INFORMATION THAT YOU UNDERTAKE WHILE USING THE SOFTWARE OR RELATED SERVICE.  IN NO EVENT
 * SHALL WHITEPAGES OR ITS AFFILIATES BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHERLIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR RELATED SERVICE.
 * 
 */


class Wp_WhitePages_Model_AutocompleteAddress extends Mage_Core_Model_Abstract 
{
	protected $_type = null;
	protected $_formattedResults = array(
										'displayname' 			=> null,
										'address_prefix'		=> null,
										'address_firstname'		=> null,
										'address_middlename'	=> null,
										'address_lastname'		=> null,
										'address_suffix'		=> null,
										'address_company'		=> null,
										'address_street0'		=> null,
										'address_street1'		=> null,
										'address_city'			=> null,
										'address_country_id'	=> null,
										'address_region_id'		=> null,
										'address_region_code'	=> null,
										'address_postcode'		=> null,
										'address_telephone'		=> null,
										'address_fax'			=> null,
										'address_vat_id'		=> null
								);
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	protected function _getWhitePagesApi()
	{
		return Mage::getModel('whitePages/api');
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $type
	 */
	public function getAutocompleteResults($data,$type)
	{
		$resultsArray = array();
		$this->_type = $type;
		
		switch($this->_type)
		{
		    case "phone":
		        $resultsArray = $this->getReversePhoneResults($data['reverse_phone']);
		        break;
		    //TODO: Remove no function written yet
		    /*case "people":
		        $resultsArray = $this->getFindPeopleResults($data);
		        break;*/
		    case "business":
		        $resultsArray = $this->getFindBusiness($data['find_business_name'],$data['find_business_location']);
		        break;
		}
		
		return $resultsArray;
	}
	
/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $phoneNumber
	 */
	public function getFindBusiness($businessName, $businessLocation)
	{
		$whitePagesApi = $this->_getWhitePagesApi();
		$whitePagesApi->setRequestQuery(array('keywords'=>$businessName, 'location'=>$businessLocation));
		$apiResults = $whitePagesApi->getFindBusiness();
		
		$formatAutocompleteResults = array();

		if(isset($apiResults['listings']))
		{
			foreach ($apiResults['listings'] as $apiResultListings)
			{
				$formatAutocompleteResults[] = $this->_setFormatAutocompleteResults($apiResultListings);
			}
		}
		
		return  $formatAutocompleteResults;
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $phoneNumber
	 */
	public function getReversePhoneResults($phoneNumber)
	{
		$whitePagesApi = $this->_getWhitePagesApi();
		$whitePagesApi->setRequestQuery(array('phone'=>$phoneNumber));
		$apiResults = $whitePagesApi->getReversePhone();
		
		
		$formatAutocompleteResults = array();

		foreach ($apiResults['listings'] as $apiResultListings)
		{
			$formatAutocompleteResults[] = $this->_setFormatAutocompleteResults($apiResultListings);
		
		}
		return  $formatAutocompleteResults;
	}
	
	protected function _setFormatAutocompleteResults($apiResultListings)
	{
		$formattedResults = $this->_formattedResults;
		$resultKey = $this->_type;
		
		if($resultKey != 'business')
		{
			$resultKey = 'people';
		}
		
		if($this->_type == 'business' && count($apiResultListings[$resultKey]) > 0 )
		{
			$formattedResults['displayname']		= (isset($apiResultListings['displayname'])?$apiResultListings['displayname']:'');
			$formattedResults['address_company']	= (isset($apiResultListings['displayname'])?$apiResultListings['displayname']:'');
		}
		elseif( count($apiResultListings[$resultKey]) > 0  )
		{

			foreach ($apiResultListings[$resultKey] as $people)
			{
				if($people['rank'] == 'primary')
				{
					$formattedResults['displayname'] 		= (isset($apiResultListings['displayname'])?$apiResultListings['displayname']:'');	
					$formattedResults['address_prefix'] 	= (isset($people['prefix'])?$people['prefix']:'');	
					$formattedResults['address_firstname'] 	= (isset($people['firstname'])?$people['firstname']:'');	
					$formattedResults['address_middlename'] = (isset($people['middlename'])?$people['middlename']:'');	
					$formattedResults['address_lastname'] 	= (isset($people['lastname'])?$people['lastname']:'');	
					$formattedResults['address_suffix']	 	= (isset($people['suffix'])?$people['suffix']:'');	
				}
			}
		}

		foreach($apiResultListings['phonenumbers'] as $phone)
		{
			if($phone['rank'] == 'primary')
			{
				$formattedResults['address_telephone'] = $phone['fullphone'];
			}
		}
		
		$formattedResults['address_street0'] 		= (isset($apiResultListings['address']['fullstreet'])?$apiResultListings['address']['fullstreet']:'');	
		$formattedResults['address_city'] 			= (isset($apiResultListings['address']['city'])?$apiResultListings['address']['city']:'');	
		$formattedResults['address_country_id'] 	= (isset($apiResultListings['address']['country'])?$apiResultListings['address']['country']:'');	
		$formattedResults['address_region_id'] 		= Mage::getModel('directory/region')->loadByCode( $apiResultListings['address']['state'], $apiResultListings['address']['country'] )->getId();
		$formattedResults['address_region_code'] 	= (isset($apiResultListings['address']['state'])?$apiResultListings['address']['state']:'');	
		$formattedResults['address_postcode'] 		= (isset($apiResultListings['address']['zip'])?$apiResultListings['address']['zip']:'');	

		return $formattedResults;
	}
	
}