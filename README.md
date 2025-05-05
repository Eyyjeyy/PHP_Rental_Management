# PHP_Rental_Monitoring
This repository contains the source code for a Rental Monitoring System, designed to streamline administrative tasks related to property management. The system facilitates the management of users, apartments, categories, tenants, and associated paperwork through a web-based interface. 

The project is built using PHP, MySQL for database management, and incorporates essential security measures for data integrity and user privacy.

🖥 <strong>Languages:</strong> PHP, Javascript, HTML, CSS <br>
📚 <strong>Libraries:</strong> Chart.js, JQuery, PHPMailer, JsPDF <br>
☁  <strong>API:</strong> Semaphore SMS <br>
🖼 <strong>Frameworks:</strong> Bootstrap 5 <br>
🔨 <strong>Tools:</strong> phpMyAdmin, Cron Job <br>
👨‍💻 <strong>Data Formats:</strong> JSON <br>



5/09/24 - Project Start <br>
6/30/24 - Admin Paper Categories - Realtime Data Fetching using SSE <br><br>

7/06/24 - Admin Paper Files <br>
- Added Pagination <br>
- Added Delete Functionality <br>
- Added Download Feature <br>

7/07/24 - Monthly Sending Emails <br>
- Installed PHPMailer <br>
- Added Monthly Email Sending Mechanism (Every 1 Month Sending) <br>
   - Lacks Cron Job (Not Yet Tested on Live Server/Host, Manually trigerred in URL) <br>
   - Admin Class has the sending email function
   - Created sendMonthlyEmails.php. Will serve as the trigger to checking for dates for months passed and if so, will send email <br>
   - If any number of months has passed from date_preferred of tenant but has days in difference example, June 5 and current date is July 9, email won't send <br>
     It has to be exactly months or a month for email to send <br>

7/13/24 - Monthly Sending Emails<br>
- Added Dynamic Price in Email Body, retrieving price based on tenant's house and number of tenants sharing the same house <br>

7/15/24 - Revisions <br>
- Revised Apartment addition and update <br>
  - House Number to House Name <br>
  - Category ID to Category Name <br>
    - Shown Option in dropdown from category's ID to name (Value input is still category's ID) <br>

7/16/24 - Added Meralco Account Autofill <br>
- Updated SQL query to join houses with houseaccounts on houses.id = houseaccounts.houses_id <br>
- Modified house_data generation to include data-meralconum attribute for the update button <br>
- Adjusted JavaScript to autofill the Meralco Account field in the update modal <br>

7/16/24 - Revised Adminhouses Forms <br>
- Revised New Modal Fields to be side by side <br>
- Revised Update Modal Fields to be side by side <br>

7/22/24 - Created Adminpayments <br>
- Admin can approve or decline payment/s of tenants <br>
- Approved payments are taken into payments.php's calculation mechanism <br>
- Added Image Preview on click <br>

7/24/24 - User Payments <br>
- Added Monthly Balance which resets to 0 when covered by payment, only shows price to pay for the month and not total <br>

7/26/24 - User Chat <br>
- Added Chat for User Side, different UI from admin chat <br>
- Implemented SSE for realtime update from database operations, such as receiving and display of message from other user while on chat page <br>

7/31/24 - Automated MySQL DB Setup <br>
- Created db_setup.php script responsible for checking and automated setup of MySQL database, requires .sql file <br>
- Modified admin.php, inserted code in constructor integrating db_setup.php <br>

8/2/24 - User Side Navbar Revision & Footer <br>
- Added History for Admin Activities <br>
- Applied same navbar from index.php for all non-admin pages for uniformity <br>

8/5/24 - Added History Page <br>
- Utilizes History function from admin.php <br>

8/9/24 - History Functionality <br>
- Added History: Delete user, Update user, Add User <br>
- Added History for Delete House/Apartments <br>

8/10/24 - History Functionality <br>
- Added History: Update House <br>

8/11/24 - History Functionality <br>
- Continuation until tenants <br>

8/12/24 - History Functionality <br>
- Completion of History for administrator actions <br>

8/12/24 - Added Expenses Page <br>
- Created expenses table for phpMyAdmin (MYSQL) <br>
- Completed, Add, Update and Delete for Expenses <br>

8/15/24 - Added Income Expenses Comparison Chart to Dashboard <br>
- Created function getIncomeExpensesData for retrieval of Data from MYSQL in admin.php <br>

8/17/24 - Added Profile Page Backend <br>
- Created function updateUserProfile, getUserProfile, updateTenantProfile, updatePaymentsTable <br>
- Utilizes History Function from admin.php <br>

8/18/24 - Added Info Page for Users <br>
- Info Page retrieves logged in user's account from houseaccounts table. <br>

9/21/24 - Added Info Page for Users <br>
- Merged table from admincategories.php to adminhouses.php. <br>
- Improved admin/includes/header.php code indentations <br>

9/23/24 - Integrated Semaphore curl API, Revised format of email contents, changed sender gmail <br>

9/26/24 - Admin.php <br>
- Revised addPayment function to include name validation of the user due to tester's feedback being able to commit client-side manipulation through editing of value by inspect element before submitting user payment data <br> 

10/4/2024 - Created "Seen" feature for admin and user chats <br>
- Created mark_seen.php that updates message seen column when called by chat or chat_user.php <br>
- Revised chat.php to mark recipient's message as seen. Admin's message will also have its own seen stamp when recipient/regular user sees or fetches the conversation <br>
- Revised chat_user.php to mark recipient's message as seen. Regular user's message will also have its own seen stamp when recipient/admin sees or fetches the conversation <br>

10/6/2024 - Revised text formatting of sms <br>
- Revised admin.php's sendMonthlyPaymentNotifications function to pass tenant and user db info to sendMonthlyEmails.php for sms usage <br>
- Revised sendMonthlyEmails.php to iterate over each tenant for dynamic sending of sms to any number of tenants depending on how many met the condition in sendMonthlyPaymentNotifications <br>

10/25/2024 - adminhouses.php <br>
- Revised html and javascript for sorting data when column header clicked <br>

10/25/2024 - minor frontend content revisions <br>

10/26/2024 - admin and user chats updated to be able to send photos without text message <br>

10/27/2024 - login.php, admin.php <br>
- new column in users table <br>
- admin.php sendotp function sending to user's gmail and cellphone <br>
- login.php now has recovery method to change to new password if user forgot account through OTP method <br>

10/28/2024 - admin.php revised getyearlyincome for dashboard <br>
- revised data shown on chart to only show data/payments that are approved by admin in the income per year chart <br>
- revised "Send OTP" button color from blue to green <br>

11/3/2024 - all user and admin pages revised to include indicator of unseen chat messages in navbar and header <br>
- unable to use more than one sse on each page and had to use polling <br>
- created fetch_unread_count and fetch_unseen_count_specific for counting of total unread messages for all users and total unread messages per user <br>

11/7/2024
- Removed contact from input of adding new tenants in admintenants <br>
- added gcash and bank columns for houseaccounts <br>
- added realtime search bar feature for many admin pages with tables <br>

11/8/2024
- Added pagination for users, apartments, tenants, payments (pages) <br>
- Revised search_page files to integrate pagination in order for pagination, search, and sorting to be able to work together <br>

11/10/2024 - 11/12/2024
- Added admin_contract_template.php <br>
- Installed phpword and signaturepad libraries/api for the purpose of making admin-tenatn contracts(word file) for digital signatures and user input for admin <br>
- base template of word file located at asset/contract.docx <br>

11/12/2024
- Added contracts table for mysql/phpmyadmin database <br>

11/14/2024
- Added contract_user.php page for user side <br>

11/15/2024
- Added Delete button with functionality for admin_contract_template.php table <br>
- Added Download button for ms word contract for admin_contract_template.php table <br>

11/16/2924
- Added completeContract function in admin.php for tenant in user side to send lessee signature and witness signature of contract <br>

11/17/2024
- Created physical_contracts database table in phpmyadmin <br>
- Added search_physical_contract.php and search_contract.php for pagination and search functionality for admin_contract_template.php <br>
- Applied proper javascript and jquery for admin_contract_template.php <br>

11/19/2024
- Enabled multiple file upload for physical contracts, allowed to upload pdf, displays pdf file <br>

11/20/2024
- Created deposits folder to insert deposit images <br>
- Created admin/fetchdeposit/deposit.php script to dynamically fill up modal info when update button of a deposit(payment type) is clicked <br>
- Created deposit table in phpmyadmin <br>
- Created admin functions for approving and declining deposit in adminpayments.php <br>
- if approved or declined deposit, only update button shows. <br>

11/21/2024
- Admindelinquency page created <br>
- Admindelinquency page backend for display <br>

11/22/2024
- delinquencySendReminder function in admin.php created for backend of sending email reminders in Admindelinquency page <br>

11/24/2024
- revised admindelinquency to be able to determine and display total amount of missing payments on table <br>
- revised adminpayments due to image modal popup for payment_type of deposit not appearing when small image is clicked <br>

11/27/24
- revised some paginations, removed pagination for pages with 2 tables <br>
- revised adminpapers.php's javascript for delete button of 1st table <br>

11/28/24
- revised payments.php to display total balance of tenant for all months instead of just the monthly balance that displays the balance needed to pay for the current date's month <br>
- revised admindelinquency.php to only count the payments with approval column's value of "true" in the computation of missing months and missed months <br>

11/29/24
- Commented out this line in admindelinquency and user_delinquency pages: <br>
- $missing_payment_total -= $paid_total; <br>
- Created fetch_user_delinquency_month.php for notification indicator for header delinquency icon number indicator of number of missed months <br>
- Revised regular/includes/header_user.php script to include new delinquency page icon in header/navbar <br>

11/30/24
- created sendEviction function in admin.php for admindelinquency page for send eviction button <br>
- admindelinquency page, addition of send eviction word file to tenant by email, utilizing phpword library and phpmailer api as well as semaphore for sms <br>

12/1/24
- index.php revision for user to have a popup modal lasting 10 seconds before being closable, informing user of an eviction notice once the admin sent an evictoin email <br>
- eviction_seen_status.php made to update eviction_popup seen column to true once the timer in index.php is finished. So the popup would still display if the user exited and reopened the index.php before the timer ends <br>

12/2/24
- Installed tcpdf library using composer to enable convertion of docx files to pdf to be rendered as iframes <br>
- Created function "displayContractPDF" in admin.php for loading of docx and convertion to pdf to be rendered in web pages <br>

12/6/24
- admin's addArchive function now also sets archive of payments table to true for the archived tenant's payment records <br>

12/8/24
- contract_user.php revision, created additional contract preview display column for contract docx's pdf preview modal <br>

12/9/24
- Removed btn primary class from login and register buttons in login.php <br>
- Added reveal password text in adminuser.php <br>

12/11/24
- Added jspdf via cdn and html2canvas in adminarchive.php to download archived data as captured html in pdf <br>

12/15/24
- Changed carousel images in login.php and index.php <br>

12/16/24
- phpmyadmin, added file_path column for eviction_popup table <br>

1/07/25
- created testpdf.php to test pdf generate using fpdf <br>
- admin_contract_template.php, in adding a new digital contract, added a default "Select a Lessee" value to prevent issue with Apartment Address field
not being autofilled when there is only a single tenant in the database <br>

1/08/25
- installed fpdf and fpdi using composer

1/10/25
- Revising admin.php's addContract function to utilize fpdf and create pdf file instead of ms word <br>
- Revised search_contract.php to preview the pdf
- TO DO: <br>
  - Revise User Side Complete Contract function - DONE <br>
  - Revise Admin Side's Archiving for the revised contract process <br>

1/11/25
- ONGOING Revision of admin.php's sendeviction to utilizing pdf instead of word docx <br>

1/12/25
- Finished revision of admin.php's sendeviction to use pdf <br>
- Revised admindelinquency.php, added Eviction preview column <br>
  - Added Left Join for eviction_popup to retrieve pdf file_path <br>
  - Added Eviction PDF Popup Modal Preview <br>
  - Revised index.php's javascript code to include eviction file preview for the popup <br>

- Revised regular/includes/footer.php <br>
- Revised contract_user.php <br>
- Revised user/payments.php <br>

1/13
- Revised admin_contract_template.php's PDF Modal for digital to be larger <br>

1/15
- Created sendFourMonthsEviction.php file
- Added notification_sent_months column to tenants table <br>

1/18/25
- Added Confirmation Popup to contracts <br>
- Added automation for eviction <br>
- Added proper name format checker <br>
- PDF input changes <br>

Unfinished:
- Added adminreports.php for reports page <br>

1/21/25
- Added filter_reports_table_1.php and filter_reports_table_2.php for first two tables in adminreports.php <br>
- Enabled download for tables: <br>
  - General Income <br>
  - Income per Tenant/Apartment <br>

1/22/25
- Added sendYearlyTax.php to remind me the admin landlord to pay tax every year on december 1 <br>
- Finished filters for <br>
  - General Income <br>
  - Income per Tenant/Apartment <br>
  - Vacancies <br>
  - Tenant and Apartment Count <br>
  - Summary of Delinquencies <br>

1/23/25
- Added date_registered column for houses TABLE <br>
- Revised sql_3 query of adminreports.php for vacancies table to correctly display vacant dates of each house including houses with no tenants <br>
- Revised filter_reports_table_3.php for the filter date of new base query for table 3 Vacancies in adminreports.php <br>
- Revised admindelinquency.php for Amount paid column to fixed with to prevent squeezing other columns when amount paid has many variables <br>

1/24/25
- Added Confirm Popup for send eviction in admindelinquency.php

1/26/25
- Revised filter_reports_table_1.php for filter start and end dates query to use collation to avoid issue in hosted <br>
- Adminreports.php: <br>
  - Reports table now have Monthly, Quarterly, Yearly Button Filters <br>
    - filter_reports_table_2.php queries for the 3 button filters <br>

- Fixes <br>
  - filter_reports_table_2.php: Monthly, Quarterly, Yearly Revised to display by current date, Quarterly to display current month and 2 months before, Monthly for today's current month <br>

1/27/25
- filter_reports_table_5.php now has Quarterly and Yearly based on current date <br>
- 4th Table Now shown and counts total tenants and total houses <br>
- filter_reports_table_1.php quarterly revised to current month and previous two months <br>

1/28/25
- Tentative Final Meeting <br>