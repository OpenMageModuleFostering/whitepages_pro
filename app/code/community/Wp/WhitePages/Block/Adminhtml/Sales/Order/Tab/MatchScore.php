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

class Wp_WhitePages_Block_Adminhtml_Sales_Order_Tab_MatchScore extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
    {
    	
    	protected $_matchScore;  	
    	
    	/**
    	 * 
	     * Set the tab's content template 
	     *
	     */
	 	protected function _construct()
	    {
	        parent::_construct();
	        $this->setTemplate('whitepages/order/view/tab/matchscore.phtml');
	        $this->setMatchScore();
	    }

	    /**
	     * Retrieve order model instance
	     *
	     * @return Mage_Sales_Model_Order
	     */
	    protected function _getOrder()
	    {
	        return Mage::registry('current_order');
	    }
	    
	    /**
	     * Retrieve order model instance id
	     * 
	     * @return Current order id
	     */
	    protected function _getOrderId()
	    {
	    	return $this->_getOrder()->getId();
	    }
	    
	    /**
	     * Set the current orders MatchScore
	     */
	    public function setMatchScore()
	    {
	    	$orderId = $this->_getOrderId();
	    	
	    	$this->_matchScore = Mage::getModel('whitePages/matchScore')->getMatchScoreByOrderId($orderId);
	    }
	    
	    /**
	     * Retrieve MatchScore 
	     * 
	     * @return
	     */
    	public function getMatchScore()
	    {
	    	return $this->_matchScore;
	    }
	    
	    public function hasMatchScore()
	    {
	    	$hasMatchScore = false;
	    	if($this->_matchScore->getId() != null)
	    	{
	    		$hasMatchScore = true;
	    	}
	    	return $hasMatchScore;
	    }
	    
	    public function getDetailedMatchScore()
	    {
	    	return $this->_matchScore->getDetailedMatchScore();
	    }
	    
	    public function getOrderIp()
	    {
	    	 return $this->_getOrder()->getRemoteIp();
	    }
	    
	    public function getMatchScoreGrade()
	    {
	    	$matchScoreGrade = "&nbsp;&nbsp;";
	    	
	    	if($this->hasMatchScore() == true)
	    	{
	    		$matchScoreGrade .= "<strong class='grade-".$this->_matchScore->getGrade()."'>".$this->_matchScore->getGrade()." (".number_format( $this->_matchScore->getScore(), 2) .")</strong>";	
	    	}
	    	else
	    	{
	    		$matchScoreGrade .= '';
	    	}
	    	
	    	return $matchScoreGrade;
	    }
	    
	    public function getMatchScoreLabel($labelKey)
	    {
	    	$labelArray = array(
		    					'phone_name'	=> $this->__('Phone-Name Score'),
								'address_name'	=> $this->__('Address-Name Score'),
								'deliverable'	=> $this->__('Deliverable Score'),
	    						'family'		=> $this->__('Family Count'),
		    					'spam'			=> $this->__('Phone Spam Score'),
	    						'phone_type'	=> $this->__('Phone Type')
	    					);
	    	return $labelArray[$labelKey];
	    }
	    
	    public function getFamilyNames(array $familyNames)
	    {
	    	$nameArray = array();
	    	foreach ($familyNames as $key => $familyName)
	    	{
	    		$nameArray[] = $familyName['lastname'];
	    	}
	    	return implode(', ', $nameArray);
	    }
	    
	    public function getTransactionDetails()
	    {
	    	return $this->_matchScore->getTransactionDetails();
	    }
	    
	    
	    /**
	     * ######################## TAB settings #################################
	     */
	    
	    public function getTabClass()
	    {
	    	return "";
	    }
	    
	    public function getTabLabel()
	    {
	        return Mage::helper('sales')->__('MatchScore%s',$this->getMatchScoreGrade());
	    }
	
	    public function getTabTitle()
	    {
	        return Mage::helper('sales')->__('MatchScore%s',$this->getMatchScoreGrade());
	    }
	
	    public function canShowTab()
	    {
	        return true;
	    }
	
	    public function isHidden()
	    {
	        return false;
	    }
    }