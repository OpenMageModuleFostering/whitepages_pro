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

class Wp_WhitePages_Model_Api extends Mage_Core_Model_Abstract
{
	//Configuration
	const API_OUTPUT_TYPE   				= 'JSON';
	const API_URL							= 'http://proapi.whitepages.com';
	const API_VERSION 						= '1.1';
	const API_REQUEST_METHOD_MATCHSCORE 	= 'match_score';
	const API_REQUEST_METHOD_TEST 			= 'reverse_phone'; //TODO:get test method action
	const API_REQUEST_METHOD_REVERSEPHONE 	= 'reverse_phone';
	const API_REQUEST_METHOD_FINDPERSON 	= 'find_person';
	const API_REQUEST_METHOD_FINDBUSINESS	= 'find_business';
	const API_REQUEST_TEST_PHONENUMBER		= 2069735100;
	const API_REQUEST_TEST_SUCCESS_MESSAGE	= 'Your WhitePages API is valid.';
	const API_REQUEST_TEST_ERROR_MESSAGE	= 'There was an error connecting to the server please contact WhitePages Support. ';
	
	const ERROR_CODE_DEBUGGING				= 200;
	//TODO: Add other methods
	//const REQUEST_METHOD_REVERSPHONE = 'match_score';

	protected $_apiKey;
	protected $_requestQuery;
	protected $_result;
	protected $_hasError;

	
	/*
	 * Builds and sets the request query string
	 */
	public function setRequestQuery($requestArray=array())
	{
		//Add API Config to Request
		$this->_requestQuery  = '?'.http_build_query( $requestArray + $this->getConfig() );
		$this->_hasError = false;
	}
	
	/**
	 * Make a free test conection to validate key
	 * 
	 * @return the result Message string
	 */
	public function testKey()
	{
		$retunMessage = '';
		
		$this->setRequestQuery( array( 'phone'=>Wp_WhitePages_Model_Api::API_REQUEST_TEST_PHONENUMBER) );
		$testRequest = $this->_makeRequest(Wp_WhitePages_Model_Api::API_REQUEST_METHOD_TEST);

		if($this->_result["result"]['type'] == 'error')
		{
			$retunMessage = $this->_result["result"]["message"];
		}
		elseif($this->_result["result"])
		{
			$retunMessage = Mage::helper('whitePages')->__(Wp_WhitePages_Model_Api::API_REQUEST_TEST_SUCCESS_MESSAGE);
		}
		else
		{
			$retunMessage = Mage::helper('whitePages')->__(Wp_WhitePages_Model_Api::API_REQUEST_TEST_ERROR_MESSAGE);
		}

		return $retunMessage;
	}
	
	
	/**
	 * Make MatchScore request and return results
	 * 
	 * @return the result array
	 */
	public function getMatchScore()
	{
		$this->_makeRequest(Wp_WhitePages_Model_Api::API_REQUEST_METHOD_MATCHSCORE);
		return $this->_result;
	}
	
	/**
	 * Make Reverse Phone request and return results
	 * 
	 * @return the result array
	 */
	public function getReversePhone()
	{
		$this->_makeRequest(Wp_WhitePages_Model_Api::API_REQUEST_METHOD_REVERSEPHONE);
		return $this->_result;
	}
	
	/**
	 * Make Find Business request and return results
	 * 
	 * @return the result array
	 */
	public function getFindBusiness()
	{
		$this->_makeRequest(Wp_WhitePages_Model_Api::API_REQUEST_METHOD_FINDBUSINESS);
		return $this->_result;
	}
	
	/**
	 * Make Find Person request and return results
	 * 
	 * @return the result array
	 */
	public function getFindPerson()
	{
		$this->_makeRequest(Wp_WhitePages_Model_Api::API_REQUEST_METHOD_FINDPERSON);
		return $this->_result;
	}
	
	public function getErrors()
	{
		return $this->_hasError;
	}
	
	/*
	 * Make Curl request and return JSON string as array
	 */
	private function _makeRequest($type)
	{
	
		$http = new Varien_Http_Adapter_Curl();
		$config = array('timeout' => 30);
		
		//Build URL from the 
		$url = Wp_WhitePages_Model_Api::API_URL.'/'.$type.'/'.Wp_WhitePages_Model_Api::API_VERSION.'/'. $this->_requestQuery;

		//Magento logging
		$isLogsActive = Mage::getStoreConfig('dev/log/active');
		if($isLogsActive)
		{
			$this->wpDebug("Requests URL: ".$url);
		}
		//echo $url; die;
		
		try 
		{
			//Make Curl Request
			$http->write('GET',	$url, '1.1');
			$this->_result = json_decode(Zend_Http_Response::extractBody($http->read()), true );
			
			//Check for Curl Error
			if($http->getErrno() != 0)
			{
				Mage::throwException($http->getError(). ' error number '. $http->getErrno());
			}
			//Check for API result Errors
			elseif(array_key_exists('errors',$this->_result))
			{
				Mage::throwException($this->_result["errors"][0]);
			}
		}
		catch (Exception $e) 
		{
		  	$this->wpDebug($e->getMessage(), Zend_Log::ERR);
			$this->_hasError = true;
		}
		
		//Magento logging
		if($isLogsActive)
		{
			$this->wpDebug( "Response Data: ".var_export($this->_result, true));
		}
		
		return $this->_result;
	}
	
	
	/*
	 * Return the API Key
	 */
	private function getApiKey()
	{
		return Mage::helper('core')->decrypt(Mage::getStoreConfig('whitePages_configuration/options/api_key'));
	}
	
	
	/*
	 * Returns the API Key and URL in an Array
	 */
	private function getConfig()
	{
		$apiKey = $this->getApiKey();
		$configArray = array('api_key'=> $apiKey,'outputtype'=>Wp_WhitePages_Model_Api::API_OUTPUT_TYPE);
		
		return $configArray;
	}
	
	/*
	 * Write any errors to our logs for easy reading
	 */
	public function wpDebug($message, $code=Zend_Log::INFO)
	{
		Mage::log($message, $code, "WhitePageswpDebug.log");
	}
}