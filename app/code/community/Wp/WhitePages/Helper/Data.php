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
	
	public function getMatchScoreOptions($select=false)
	{
		if($select == false)
		{
			$matchScoresOptionArray = array(
										'A'=> $this->__('Match'),
										'B'=> $this->__('Near Match'),
										'C'=> $this->__('Near Match'),
										'D'=> $this->__('Poor Match'),
										'E'=> $this->__('Poor Match'),
										'F'=> $this->__('No Match')
									);
		}
		else
		{
			$grades = range('A', 'F');
	
			foreach ($grades as $key => $value)
			{			
				$matchScoresOptionArray[$value] = $value;
			}
		}
		return $matchScoresOptionArray;
	}
	
	public function getMatchScoreAddressOptions()
	{
		$matchScoreAddressOptionsArray = array(
									'A'=> $this->__('Receiving Mail'),
									'F'=> $this->__('Not Receiving Mail ')
								);

		return $matchScoreAddressOptionsArray;
	}
	
	public function getMatchScorePhoneOptions()
	{
		$matchScoresPhoneOptionsArray = array(
										'A'=> $this->__('Mobile or Landline'),
										'F'=> $this->__('Non-fixed VoIP')
									);

		return $matchScoresPhoneOptionsArray;
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
	
    public function getMatchScoreOptionsLabel($grade, $key)
    {
    	switch ($key)
    	{
    		case "phone_type":
    			$matchScoreOptions = $this->getMatchScorePhoneOptions();
    			break;
    		case "deliverable":
    			$matchScoreOptions = $this->getMatchScoreAddressOptions();
    			break;
    		default:
    			$matchScoreOptions = $this->getMatchScoreOptions();
    			break;
    		
    	}
    	
    	$matchScoreOptionsLabel = ( isset($matchScoreOptions[$grade]) ? $matchScoreOptions[$grade] : null);
    	
    	if( $grade == null)
    	{
    		$matchScoreOptionsLabel = $this->__('n/a');
    	}elseif($matchScoreOptionsLabel == null)
    	{
    		$matchScoreOptionsLabel = $grade;
    	}
    	
    	return $matchScoreOptionsLabel;
    }
}