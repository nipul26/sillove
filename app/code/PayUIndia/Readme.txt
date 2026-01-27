***********************************************************************
*                                                                     *
* PayUBiz BOLT and Redirect Extension for Magento v2.4.x Extension    *
*                                                                     *
***********************************************************************

This extension supports Web Browser security update for cross-site request forgery (CSRF).

Note: For Redirect feature "SameSite=None; Secure" setting is mandatory at server level.

Added Features:

- Fixed Magento 2 bug for Order-Item 'base_original_price' not updated
- Send invoice email after generation of new invoice.
- Invoice generation after receiving payment confirmation.
- Send new order notification after receiving payment confirmation. (Read notes for email configuration)
- Minimum dependency on Samesite attribute
- Both BOLT and Redirect payment
- Verify payment using PayU inquiry API
- Unique txnid for retry of checkout
- GraphQl create order 
- Fix for order number not displyed on Onepage/Success
- Fix for Guest checkout - Email address validation in core Magento 2
- Affordability Widget (see steps below)

Pre-requisite:

- Magento 2.4.x installation (both frontend and admin) must be running on HTTPS. 
- Self-signed certificate will also work in case of localhost or demo installations.


Steps To Install PayU payment extension: 

1. Take a full backup of existing Magento 2.4.x (both code and database)

2. If previously installed, disable PayU payment extension.

3. Uninstall / Remove physical extension files from app/code/PayUIndia

4. Upload present extension files to Magento <app>/<code>.

5. upgrade and deploy Magento installation.

6. Login to Admin panel and enable payment extension.


Extension Configuration -

1. Enabled 		- Yes/No :: The extension will be active in checkout if Yes.
2. Payment Action 	- BOLT :: For popup payment
			- Redirect :: For off-site payment
3. Custom Tran. ID	- If Yes, it will append a unique string at the end of Magento ID. This is to avoid duplicate transaction ID error of PayU. Keep Yes for BOLT payment. It has no effect in case of GraphQl.

4. AccountType		- PayUBiz
5. Title		- A custom text as suitable.
6. Environment	 	- Sandbox :: for Test transaction
			- Production :: for live transaction
7. Merchant Key		- As provided by PayU
8. Salt Key		- As provided by PayU
9. Payment Verification - Double check on payment before finalising order. This has no effect in case of GraphQl.


Configure New Order Email sender -
** perform this step to send new order notification only after processing of captured payment.

1. Modify vendor/magento/module-quote/Observer/SubmitObserver.php. Comment the line "$this->orderSender->send($order);"
2. Save the modified file and then Magento commands for upgrade, compile, deploy, flush and change file ownerships and permissions.

Module Session code requirement -

If BOLT payment action chosen then 'Session/CustomConfig.php' not required. CustomConfig.php was introduced for Redirect payment and requirment of Samesite=None cookie setting. If CustomConfig.php gives trouble or using BOLT then 'Session' directory can be removed.

1. Delete 'Session' directory.
2. Modify etc/di.xml. Remove '<preference for="Magento\Framework\Session\Config\ConfigInterface"....' entry.
3. Compile Magento.


GraphQl Apis - 

Please follow Magento 2 guidelines and workflow for using below GraphQl custom apis for PayU payment.
 
mutation SetPayuPaymentDetailsOnCart {
    setPayuPaymentDetailsOnCart(input: { cart_id: "ICH9p0xRn5BhxhS2715oWUOJyxhkI66t", payu_payment_id: "temp1234" }) {
        cart {
            id
        }
    }
}


mutation PlacePayuOrder {
    placePayuOrder(cart_id: "ICH9p0xRn5BhxhS2715oWUOJyxhkI66t") {
        amount
        currency
        message
        order_quote_id
        payu_payment_id
        success
    }
}



Troubleshooting:

- In case this extension does not work properly immediately revert to old extension or for major issues restore site from backup.

- In case you are using Redirect payment feature and you need custom session cookie manager for SameSite=None; Secure, then open the file PayUIndia/PayU/Session/CustomConfig.php. Un-comment the custom code line 12 and line 23. Save and compile.

- To stop sending initial Pending order emails visit Admin->Store->Configuraton->Sales->Sales Emails. Set NO to New Order Email Enabled.

- For advanced modification of email codes, vendor/magento/module-quote/Observer/SubmitObserver.php. Comment the line "$this->orderSender->send($order);"

- By default, Magento sends new order confirmation mail during checkout - order confirmation. That means, for redirect payment option,
mail goes out evern before actual payment is done. 
To stop this new order email before payment, upload from "troubleshooting-neworder-email" folder to Magento root.

- cookie_httponly = On, cookie_secure=1, cookie_samesite=None to be configured in php environment.

- Magento Samesite=Lax issue fix is present. Copy included PublicCookieMetadata.php and SensitiveCookieMetadata.php to vendor\Magento\Framework\Stdlib\Cookie\

https://meetanshi.com/blog/solved-magento-2-2-7-and-2-3-admin-page-blank-issue/




******************************************
*                                        *
* Steps To Install Affordability Widget: *
*                                        *
******************************************

Add Header script:

1. Login to Admin and go to Content - [Design] - Configuration

2. Click {Edit} action on your target Store 

3. Expand HTML {Head} section of {Other Settings}

4. Enter the header script in {Scripts and Style Sheets box}.

<script  type="text/javascript"  src="https://jssdk.payu.in/widget/affordability-widget.min.js"></script>

<!--// Optionaly you may add custom CSS for placement of the widget content in product view. This step is optional and may be required if the widget panel not displayed properly. //-->
<style>
#payuWidget {
height: 0; 
position: relative; 
float: left;
}
</style>


5. Save Configuration.

6. Upload Widget code in App\code directory and follow Admin configuration steps.

7. The widget module name is 'PayUIndia/Affordability'. Friendly name is 'PayU Affordability Widget'

8. Go to Admin - Content - [Elements] - Widgets

9. Click {Add Widget} button on top right

10. Selct Type as 'PayU Affordability Widget' 

11. Select Your Theme e.g. Magento Luma

12. Enter a custom widget title as per your choice

13. Assign to your store from the option box.

14. Sort order 1 or as per your suitability in case more widgets are present.

15. Layout Updates - select {Specified Page}

16. Page - Catalog Product View (any), Container - Main Content Area

17. {Save and Continue Edit} widget configuration. 

18. Go to {Widget Options}

19. Enter Merchant Key as provided by PayU

20. Save widget. Check widget at product view page.