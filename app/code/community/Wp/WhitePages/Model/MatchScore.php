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

class Wp_WhitePages_Model_MatchScore extends Mage_Core_Model_Abstract 
{
    public function _construct()
    {
        parent::_construct(); 
        $this->_init('whitePages/matchScore');
    }

    public function getTransactionDetails()
    {
    	$result = $this->getResultsArray();
    	$transactionId = $result['transaction_id'];
    	
    	return array( 'transaction_id' => $transactionId);
    }
    
    /**
     * 
     * Retrieves the results from current loaded object  
     * 
     * @return Array of the MatchScore details  values
     */
    public function getDetailedMatchScore()
    {
    	$result = $this->getResultsArray();

    	//Messages are not always availible so set them that way
    	$billingPhoneNameMessages 	= ( isset( $result['score_vector']['billing_phone_name']['messages'] ) ? $result['score_vector']['billing_phone_name']['messages'] : null );
    	$billingAddressNameMessages = ( isset( $result['score_vector']['billing_address_name']['messages'] ) ? $result['score_vector']['billing_address_name']['messages'] : null );
    	$billingDeliverableMessages = ( isset( $result['score_vector']['billing_address_deliverable']['messages'] ) ? $result['score_vector']['billing_address_deliverable']['messages'] : null );
    	$billingFamilyMessages		= ( isset( $result['score_vector']['billing_address_family_count']['meta']['messages'] ) ? $result['score_vector']['billing_address_family_count']['meta']['messages']  : null );
    	$billingSpamMessages		= ( isset( $result['score_vector']['billing_phone_spam_score']['messages'] ) ? $result['score_vector']['billing_phone_spam_score']['messages'] : null );
    	$billingPhoneTypeMessages	= ( isset( $result['score_vector']['billing_phone_type']['messages'] ) ? $result['score_vector']['billing_phone_type']['messages'] : null );
    	
    	$shippingPhoneNameMessages 		= ( isset( $result['score_vector']['shipping_phone_name']['messages'] ) ? $result['score_vector']['shipping_phone_name']['messages'] : null );
    	$shippingAddressNameMessages 	= ( isset( $result['score_vector']['shipping_address_name']['messages'] ) ? $result['score_vector']['shipping_address_name']['messages'] : null );
    	$shippingDeliverableMessages	= ( isset( $result['score_vector']['shipping_address_deliverable']['messages'] ) ? $result['score_vector']['shipping_address_deliverable']['messages'] : null );
    	$shippingFamilyMessages			= ( isset( $result['score_vector']['shipping_address_family_count']['meta']['messages'] ) ? $result['score_vector']['shipping_address_family_count']['meta']['messages']  : null );
    	$shippingSpamMessages			= ( isset( $result['score_vector']['shipping_phone_spam_score']['messages'] ) ? $result['score_vector']['shipping_phone_spam_score']['messages'] : null );
    	$shippingPhoneTypeMessages		= ( isset( $result['score_vector']['shipping_phone_type']['messages'] ) ? $result['score_vector']['shipping_phone_type']['messages'] : null );

		$detailedMatchScoreArray = array(
	    								'billing'	=> array(
							    			'phone_name'	=> array( 
														'grade' => $result['score_vector']['billing_phone_name']['grade'], 
														'messages' => $billingPhoneNameMessages),
							    			'address_name'	=> array( 
														'grade' => $result['score_vector']['billing_address_name']['grade'], 
														'messages' => $billingAddressNameMessages),
							    			'deliverable'	=> array( 
														'grade' => $result['score_vector']['billing_address_deliverable']['grade'], 
														'messages' => $billingDeliverableMessages),
    										'family'		=> array( 
														'names' => $result['score_vector']['billing_address_family_count']['meta']['name_frequencies'],
														'messages' => $billingFamilyMessages),
    										/*'spam'			=> array( 
														'grade' => $result['score_vector']['billing_phone_spam_score']['grade'], 
														'messages' =>  $billingSpamMessages),*/
											'phone_type'			=> array( 
														'grade' => $result['score_vector']['billing_phone_type']['grade'], 
														'messages' =>  $billingPhoneTypeMessages)
		
							    		),
							    		'shipping' => array(
							    			'phone_name'	=> array(
							    						'grade' => $result['score_vector']['shipping_phone_name']['grade'], 
							    						'messages' => $shippingPhoneNameMessages),
							    			'address_name'	=> array( 
							    						'grade' => $result['score_vector']['shipping_address_name']['grade'], 
							    						'messages' => $shippingAddressNameMessages),
							    			'deliverable'	=> array( 
							    						'grade' => $result['score_vector']['shipping_address_deliverable']['grade'], 
							    						'messages' => $shippingDeliverableMessages),
    										'family'		=> array( 
    													'names' => $result['score_vector']['shipping_address_family_count']['meta']['name_frequencies'],
														'messages' => $shippingFamilyMessages),
							    			/*'spam'			=> array( 
							    						'grade' => $result['score_vector']['shipping_phone_spam_score']['grade'], 
							    						'messages' =>  $shippingSpamMessages),*/
											'phone_type'			=> array( 
														'grade' => $result['score_vector']['shipping_phone_type']['grade'], 
														'messages' =>  $shippingPhoneTypeMessages)
							    		),
    								);

    	return $detailedMatchScoreArray; //array($detailedMatchScoreArray, $result);// 
    }
    
	public function setResults($results)
	{
		$this->setScore($results["score"]["score"]);
		$this->setGrade($results["score"]["grade"]);
		$jsonResults = json_encode($results);
		$this->setResult($jsonResults);
		$this->setIsMarkedAsFraud($results["meta"]["is_marked_as_fraud"]);
	}
	
	public function getMatchScoreByOrderId($orderId)
	{
		return $this->getCollection()->addFieldToFilter('order_id', $orderId)->getFirstItem();
	}
	
	public function getResultsArray()
	{
		return json_decode($this->getResult() ,true);
	}
	
	public function getMessagesString($type,$fieldKey,$grade)
	{
		$detailedMatchScore = $this->getDetailedMatchScore();
		$hasLabelValue = Mage::helper('whitePages')->getMatchScoreOptionsLabel($grade , $fieldKey );

		if( ($fieldKey != 'phone_type')  || ($fieldKey == 'phone_type' &&  $hasLabelValue == null) )
		{
			$messages = $detailedMatchScore[$type][$fieldKey]['messages'];
	    	foreach ( $messages as $key => $message)
	    	{
	    		$pos = strripos( $message, 'but' );
	    		if($pos != null)
	    		{
		    		$first = substr( $message, 0, $pos );
	    			$second = substr( $message, $pos );
		    		$messages[$key] = $first."<br>".$second;
	    		}
	    	}
		}
			
	    return $messages;
	}
}