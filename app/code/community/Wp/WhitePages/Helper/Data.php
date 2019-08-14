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

class Wp_WhitePages_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getMatchScoreExpression($key) 
	{
	    switch ($key) 
	    {
	        case 'match_score':
	       		$result = new Zend_Db_Expr("IFNULL((SELECT grade as match_score_id FROM wp_match_score WHERE order_id = main_table.entity_id LIMIT 1),'Z') ");
	        break;
	    }
	    return $result;
	}
	
	/*
	 * 
	 */
	
	public function getMatchScoreOptions()
	{
		$grades = range('A', 'F');
		$matchScoresOptionArray = array();
		
		foreach ($grades as $key => $value)
		{			
			$matchScoresOptionArray[$value] = $value;
		}
		
		return $matchScoresOptionArray;
	}
	
	public function isScoreDeclined($grade)
	{
		$declined = false;
		
		// Get MatchScore decline configuration
		$matchScoreDeclineGrade = Mage::getStoreConfig('whitePages_configuration/match_score/decline');

		if(ctype_alpha($matchScoreDeclineGrade) == true && $this->toNumber($matchScoreDeclineGrade) < $this->toNumber($grade) )
		{	
			$declined = true;
		}

		return $declined;
	}
	
	private function toNumber($dest)
    {
        if ($dest)
            return ord(strtolower($dest)) - 96;
        else
            return 0;
    }
	
}