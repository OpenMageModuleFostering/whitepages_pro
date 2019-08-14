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

AutoCompleteAddress = Class.create();
AutoCompleteAddress.prototype = {

	orderBillingAddressFields:  null,
	formAction: 				null,
	searchFields:				null,
	blockResult: 				null,
	blockWindow:                null,
    blockMask:                  null,
    requestType:				null,
    windowHeight:               null,
    blockMsg:					null,
    blockMsgError:				null,

    /**
     * Initialize object
     */
    initialize: function() {
    		this._initWindowElements();
    },

    /**
     * Initialize window elements
     */
    _initWindowElements: function() {

    	this.orderShippingAddressFields	= $('order-shipping_address_fields');
    	this.orderBillingAddressFields 	= $('order-billing_address_fields');
    	this.formAction					= $('autocomplete_action');
    	this.searchFields				= $('whitepages_autocomplete_fields');
    	this.blockWindow				= $('whitepages_autocomplete_fields_modal');
    	this.blockResult				= $('search-results');
        this.blockMask                  = $('popup-window-mask');
        this.windowHeight               = $('html-body').getHeight();
        this.blockMsg                   = $('product_composite_configure_messages');
        this.blockMsgError              = this.blockMsg.select('.error-msg')[0];
        
        
        $('submit_find_business').removeClassName('disabled');
        $('submit_reverse_phone').removeClassName('disabled');
        
    },
    /**
     * Show configuration window
     */
    _showWindow: function() {
        this.blockMask.setStyle({'height':this.windowHeight+'px'}).show();
        this.blockWindow.setStyle({'marginTop':-this.blockWindow.getHeight()/2 + "px", 'display':'block'});
    },
    /**
     * Close results window
     */
    _closeWindow: function() {
        toggleSelectsUnderBlock(this.blockMask, true);
        this.blockMask.style.display = 'none';
        this.blockWindow.style.display = 'none';
    },
    /**
     * Show and get it through ajax
     */
    showAutocompleteResults: function(type) {

    	var errorMessage = this._validateInput(type);
    	
    	if( errorMessage != null)
    	{
    		return alert(errorMessage);
    	}
    	this.requestType = type;
        this._getAutoCompleteAddress(this._getRequestData());
    },
    /**
     * Triggered on confirm button click
     * Do submit configured data through iFrame if needed
     */
    onSendLink: function(rowId,addressType) {

    	var sendLinkRow = $('whitepages_autocomplete_results_row_'+rowId);
    	var addressInputs = sendLinkRow.select('input[type="hidden"]');
    	var autoCompleteAddress = this;
    	var shippingAddressSelectOptions = $$('select#order-shipping_address_customer_address_id option');
    	var billingAddressSelectOptions = $$('select#order-billing_address_customer_address_id option');

    	//Set address id and unset same as billing
    	switch(addressType)
    	{
    		case 'billing':
    			billingAddressSelectOptions[0].selected = true;
    			break;
    		case 'shipping':
    			shippingAddressSelectOptions[0].selected = true;
    			break;
    		default:
    			billingAddressSelectOptions[0].selected = true;
    			shippingAddressSelectOptions[0].selected = true;
    	}
    	
    	for (var i=0;i<addressInputs.length;i++)
    	{
    		if(addressType != undefined)
    		{
    			this._insertAddressField(addressInputs[i],addressType);
    		}
    		else
    		{
    			this._insertAddressField(addressInputs[i],'billing');
    			this._insertAddressField(addressInputs[i],'shipping');
    		}
    	};
    	
    	order.setShippingAsBilling(false);
    	
    	this._closeWindow();
    	
        return this;
    },
    /**
     * 
     */
    _validateInput: function(addressType) {
    	
    	var errorMessage = null;
    	
    	if(addressType == 'phone')
    	{
    		var reversePhone = $('phone_autocomplete').value.trim();
    		if(reversePhone == '')
    		{
    			errorMessage = Translator.translate('Please enter a phone number.');
    		}
    	}
    	else
    	{
    		var findBusinessName = $('business_autocomplete_name').value.trim();
    		var findBusinessLocation = $('business_autocomplete_location').value.trim();
    		if( findBusinessName == '' || findBusinessLocation == '')
    		{
    			errorMessage = Translator.translate('Please enter a valid business name and location.');
    		}
    		
    	}
    	
    	return errorMessage;
    },
    /**
     * Triggered on cancel button click
     */
    onCloseBtn: function() {
        this._closeWindow();
        return this;
    },
    _getRequestData: function(){
    	var inputFields = this.searchFields.select('input[id^="'+this.requestType+'"]');

    	var data = new Object();
    	
    	inputFields.each(function(elm) {
    		
    		var elmName = elm.name;
    		var elmValue = elm.value;
    		
    		data[elmName] = elmValue;
        });
    	
    	//Set type
    	data['type'] = this.requestType;
    	return Object.toJSON(data);
    },
    _insertAddressField: function(inputElm, addressType) {
    
    	var addressField = $('order-'+addressType+'_'+inputElm.name);
    	if(addressField != undefined)
    	{
    		addressField.value = inputElm.value;;
    	}
    	else if (addressField != null)
    	{
    		addressField.value = null;
    	}

    },
    _getAutoCompleteAddress: function(data) {

    	var action = this.formAction.value;
	    new Ajax.Request(action, {
	    	parameters: data.evalJSON(),
	        onSuccess: function(transport) {
	            var response = transport.responseText;
	            if (response.isJSON()) {
                    response = response.evalJSON();
                    if (response.error) 
                    {
                    
	                	this.blockMsg.show();
	                	this.blockMsgError.innerHTML = response.message;
                    }
	                if (response.error) 
	                {
	                	alert(response.message);
	                }
	                
	                if(response.ajaxExpired && response.ajaxRedirect) 
	                {
	                	setLocation(response.ajaxRedirect);
	                }
	            } else if (response) {
	            	
	            	if(response.search('login-container') != -1)
	            	{
	            		location.href = BASE_URL;
	            	}else
	            	{
	            		//adminhtml-dashboard-index
	            		response = response+"";
	            		this.blockResult.update(response);
	            		// Show window
	            		this._showWindow();
	            	}
	            }
	        }.bind(this)
	    });
    }
};


Event.observe(window, 'load',  function() {
	autoCompleteAddress = new AutoCompleteAddress();
});