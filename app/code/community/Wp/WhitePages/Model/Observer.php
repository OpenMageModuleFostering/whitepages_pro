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

class Wp_WhitePages_Model_Observer
{
	
	public function getOrderMatchScore($observer)
	{
		// Set the order Obj
		$order = $observer['order'];
		
		
		
		// Get the order address
		$addressCollection = $order->getAddressesCollection();
		
		// Address Array
		$addressArray = array();

		//Loop all address and set the data
		foreach ($addressCollection as $address)
		{
			//Make sure we have a customer name to check
			
			if(Mage::getSingleton( 'customer/session' )->isLoggedIn() == true)
			{
				$addressArray["name"]	= $order->getCustomer()->getName();
			}
			elseif(strtolower($address->getAddressType()) == 'billing')
			{
				$addressArray["name"] =  $address->getFirstname(). " ". $address->getLastname();
			}
			$addressArray[$address->getAddressType()."_phone"]				= $address->getTelephone();
			$addressArray[$address->getAddressType()."_phone_name"] 		= $address->getName();
			$addressArray[$address->getAddressType()."_address_street"]		= $address->getData('street');
			$addressArray[$address->getAddressType()."_address_city"]		= $address->getCity();
			$addressArray[$address->getAddressType()."_address_state"]		= $address->getRegion();
			$addressArray[$address->getAddressType()."_address_zip"]		= $address->getPostcode();
		}
		
		// Connect to White Pages Api
		$apiConnection = Mage::getModel('whitePages/api');
		
		//Magento logging
		$isLogsActive = Mage::getStoreConfig('dev/log/active');
		if($isLogsActive)
		{
			$apiConnection->wpDebug("Order ID: ".$order->getIncrementId());
		}
		
		
		$apiConnection->setRequestQuery($addressArray);
		$matchScoreResults = $apiConnection->getMatchScore();

		//TODO: Add the correct messaging
		$isScoreDeclined = Mage::helper('whitePages')->isScoreDeclined($matchScoreResults["score"]["grade"]);

		if($isScoreDeclined == true)
		{
			Mage::throwException(Mage::getStoreConfig('whitePages_configuration/match_score/message'));
		}
		
		// Make sure there is data to set or do nothing
		if($apiConnection->getErrors() === false)
		{
			//Save MatchScore results and quote id
			$matchScore = Mage::getModel('whitePages/matchScore');
			$matchScore->setResults($matchScoreResults);
			$matchScore->setQuoteId($order->getQuote()->getId());
			$matchScore->save();
			
			//Set the MatchScore id
			$order->setMatchScoreId($matchScore->getId());
		}
	}
	
	public function setOrderMatchScore($observer)
	{
		$order = $observer['order'];
		$matchScoreId = $order->getMatchScoreId();
		
		//Make sure there is a MatchScore to update
		if($matchScoreId)
		{
			//Load MatchScore and set the order id to it
			$matchScore = Mage::getModel('whitePages/matchScore')->load($matchScoreId);
			$matchScore->setOrderId($order->getId());
			$matchScore->save();
		}
	}
	
	public function orderGridAddMatchScore($observer)
	{
		$showInOrderGrid = Mage::getStoreConfig('whitePages_configuration/match_score/show_in_order_grid');
		if($showInOrderGrid == true)
		{
			$block = $observer->getBlock();
	        if (!isset($block)) {
	            return $this;
	        }
	 
	        if($block->getId() == 'sales_order_grid')
	        {
	        	$block->addColumnAfter('match_score', array(
	                'header'    => 'MatchScore',
	                'index'     => 'match_score',
	        		'align'     => 'center',
	        		'width' => '75',
					'renderer'  => 'Wp_WhitePages_Block_Adminhtml_Sales_Order_Grid_Renderer_MatchScore',
					'filter_index'  => Mage::helper('whitePages')->getMatchScoreExpression('match_score'),
	        		'type'      => 'options',
	                'options'   => Mage::helper('whitePages')->getMatchScoreOptions(true)
	            ),
	            'grand_total');
	        }
		}
	}
	
	public function orderGridFilterCiollection($observer)
	{
		$showInOrderGrid = Mage::getStoreConfig('whitePages_configuration/match_score/show_in_order_grid');
		
		if($showInOrderGrid == true)
		{
			$block = $observer->getBlock();
	        if (!isset($block)) {
	            return $this;
	        }
	 
	        if($block->getId() == 'sales_order_grid')
	        {
				$collection = $block->getCollection();
				$block->getCollection()->addFieldToSelect('match_score');
			    $collection->getSelect()->columns(array(
			    		'match_score' => Mage::helper('whitePages')->getMatchScoreExpression('match_score'),
			    	));
			    
		      	$block->setCollection($collection);
		       
	        }
		}
	}
	
}