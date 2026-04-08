##Testimonials Extension

It helps to add trustworthy testimonials to your webpage feedback which consists of info about the author and so gain the user's trust and credibility.

##Support: 
version - 2.3.x, 2.4.x

##How to install Extension

1. Download the archive file.
2. Unzip the file
3. Create a folder [Magento_Root]/app/code/Sparsh/Testimonials
4. Drop/move the unzipped files to directory '[Magento_Root]/app/code/Sparsh/Testimonials'

#Enable Extension:
- php bin/magento module:enable Sparsh_Testimonials
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush

#Disable Extension:
- php bin/magento module:disable Sparsh_Testimonials
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush
