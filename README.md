# tokopini-Magento-Extension
Tokopini  for Magento 1.9.X extension Version 10

Tokopini 
Tokopini is an App that allows users to add feedback after making a purchase. The App and APIs have already been built. We now need plugins to be built for the most popular eCommerce platforms and we shall start with Magento 1.9x. The seller will be able to easily install the plugin and add a seller token which will allow the customers to use Tokopini for the webshop. 
Requirements 
Function 1: SEND ORDER TO TOKOPINI (after invoicing) 
Customer makes an order normally on webshop and leaves phone number and email. 
Plugin will send these details after creating the invoice to 
URL: http://backendtokopini.pre.slashmobility.com/api/purchases Method: POST JSON { 
"email" : "jon.snow@gmail.com", 
"phone" : "+34666123456" , 
"description": "name1 1; name2 2; name3 2", 
"reference" : "12345", 
"total_buy" : "5.00", 
"token" : "$2y$10$ZwRZ4VRFCJ926mvj19rZLOU84yNFaOSEBftHveRFeLexqB833sZBu" 
} 
“code” : “la primera compra vacío, si canjea cupón el código del cupón”
Responses: 200 OK, 400 error It doesn’t matter what the response is as there is not much we can do if it fails here. 
phone: 9 digits + countrycode, no spaces, description: names of products purchased with quantity, reference: order id total_buy: 2 decimal places point separated, for example 18.59 

Function 2: DISPLAY FEEDBACK 
Plugin will have different size/colour feedback widgets. Seller chooses which one to add to home page or template. A shortcut code can be used to add widget. 
Widget displays typical review summary of the average feedback score 
Clicking on it shows all feedback for the seller (No pop ups). 
E.g. John has left feedback for product 9 service 8 delivery 9 with comment: I was very happy with my adidas t-shirt, it came fast and just what I wanted. 
URL: http://backendtokopini.pre.slashmobility.com/api/feedbacks?token=$2y$10$F5w8bXP.Z/ieP CIyBuY5UO7jO7Q43/srMXvl3VS.3vqryzPcgnRB6 Method: GET 

Function 3: VOUCHER 
Customer creates voucher on App. 
Plugin places a field for Tokopini voucher code in checkout area and checks validity 
of voucher when “Add voucher” button is pressed. URL: http://backendtokopini.pre.slashmobility.com/api/verify Method: POST JSON { 
"code" : "token" : 
"7EERKhlavZ", "$2y$10$F5w8bXP.Z/iePCIyBuY5UO7jO7Q43/srMXvl3VS.3vqryzPcgnRB6" 
} RESPONSE OK { 
"credit": 9.99, 
"used": false } 
If valid, apply discount/credit amount to checkout and add message to say the voucher will be considered used when moving from the checkout page to the payment options page. Please contact the seller if payment issues occur. 
If not valid, message to say voucher is invalid. 
When the user clicks buy, check the voucher code again. 
If invalid, go back to checkout and remove voucher code with message to say it’s 
invalid. 
If valid, use the API to tell Tokopini the voucher is being used so it cannot be used 
again. Proceed as normal to payment options page. 
URL: http://backendtokopini.pre.slashmobility.com/api/purchases Method: POST JSON { 
"email" : "jon.snow@gmail.com", 
"phone" : "+34666123456" , 
"description": "name1 1; name2 2; name3 2", 
"reference" : "12345", 
"total_buy" : "5.00", 
"token" : "$2y$10$ZwRZ4VRFCJ926mvj19rZLOU84yNFaOSEBftHveRFeLexqB833sZBu" 
} 
“code” : “el código del cupón”

Function 4: EXCLUSION RULES 
The seller may want to exclude certain customers from using the Tokopini voucher. Plugin allows the seller to add multiple exclusion rules. If the customer has this or this or this, do not allow Tokopini options. 
Email pattern e.g. This is a buyer from amazon 0fkzxlqgr6x@marketplace.amazon.es Plugin allows seller to add *@marketplace.amazon.* as an exclusion. 
Customer Group Magento allows seller to create customer groups. Plugin allows seller to choose a customer group to exclude. 
Exclusion rules can be edited or deleted. 
Functions 1 & 3 will be skipped if the customer is exluded. 
Multi-lingual 
There will be language options for English, Spanish and Dutch. 


Comentarios adicionales.
Queremos utilizar el campo de descuento estándar de magento.
Los números de teléfono que dejan los compradores tendremos que renombrar a +34 (en caso de España) con todos los números seguidos sin espacios.
