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

class Wp_WhitePages_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * 
	 * Loads the layout for the controller action and sets it to this.
	 * 
	 * @return this class obj
	 * 
	 */
	protected function _initAction()
	{
		$this->loadLayout();
		return $this;
	}
	
	/**
	 * 
	 * @return the test message from WhitePages API
	 * 
	 */
	public function testkeyAction()
	{
		echo $this->getWhitePagesApi()->testKey();
	}
	
	/**
	 * 
	 * @return the view results of the autocomplete request
	 * 
	 */
	public function autocompleteAction()
	{
		//TODO: Validate logged in or not

		$controller = $this;
		
		$update = $controller->getLayout()->getUpdate();
        $update->addHandle('WHITEPAGES_AUTOCOMPLETE_AJAX');
        $controller->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
		$controller->renderLayout();
		return $this;
	}

	/**
	 * 
	 * @return the WhitePages API model
	 * 
	 */
	protected function getWhitePagesApi()
	{
		return Mage::getModel('whitePages/api');
	}
}