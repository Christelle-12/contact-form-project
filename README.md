📞 Contact Form Project
A complete, working contact form with database storage. Simple, clean, and ready to use.

✨ What This Does
When someone fills out your contact form:

Their info gets saved to a database

You can view all messages in one place

Everything stays organized

No complicated setup needed

📁 Files in This Project
text
contact-form/
├── 📄 index.html          # The actual form people see
├── 🎨 style.css           # Makes the form look nice
├── ⚡ script.js           # Checks if email is valid before sending
├── 🔗 db.php              # Connects to database
├── 📤 process.php         # Saves form data to database
├── ✅ success.html        # "Thank you" page after submitting
└── 📊 view_data.php      # Page to see all messages received
🚀 Quick Start (5 Minutes)
Step 1: Get Your Files Ready
Download these files to your computer

Put them in: C:\xampp\htdocs\contact-form\ (if using XAMPP)

Step 2: Set Up Database
Open XAMPP, start Apache and MySQL

Go to: http://localhost/phpmyadmin

Click "New" → Name: webform_db → Create

Click on webform_db → SQL tab

Copy and paste this:

sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100),
    email VARCHAR(100),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
Click "Go"

Step 3: Test Your Form
Open browser

Go to: http://localhost/contact-form/

Fill out the form

Click Submit

You should see "Thank you!" page

👁️ View Messages Received
To see all form submissions:

Go to: http://localhost/contact-form/view_data.php

You'll see a table with all messages

Shows: Name, Email, Message, Date submitted

🎨 Customize Your Form
Change Colors
Open style.css and look for these lines:

css
/* Change button color (line ~65) */
button[type="submit"] {
    background: linear-gradient(to right, #3498db, #2ecc71);
    /* Change colors: first is left, second is right */
}

/* Change form background (line ~13) */
body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}
Add More Fields
In index.html, add new fields like this:

html
<div class="form-group">
    <label>Phone Number:</label>
    <input type="tel" name="phone" placeholder="Your phone number">
</div>
Then update process.php to save the new field.

🔒 Important Security Notes
⚠️ Don't share these files publicly:

config.php (if you create one) - contains database password

Your actual db.php with real password

✅ Safe to share:

All other files are safe

The form works without showing passwords

🛠️ Fix Common Problems
Form won't submit?
Check XAMPP: Both Apache and MySQL must be GREEN (running)

Check database: Make sure table name is users

Check file locations: All files should be in same folder

"Connection failed" error?
Open db.php

Check these lines match your setup:

php
$username = "root";    // Default XAMPP username
$password = "";        // Default XAMPP password (empty)
$database = "webform_db"; // Your database name
Style not showing?
Press Ctrl + F5 to refresh browser cache

Check index.html has: <link rel="stylesheet" href="style.css">

📱 Make It Mobile-Friendly
Already done! The form automatically:

Adjusts to phone screens

Makes buttons bigger on touch devices

Keeps everything readable

💡 Tips for Real Website
Add email alerts - Get notified when someone submits

Add CAPTCHA - Stop spam submissions

Export data - Save messages to Excel

Add search - Find messages by name or date

🆘 Need Help?
Form not saving? Check phpMyAdmin to see if database exists

Getting errors? Take screenshot of error message

Want to change something? Ask what you want to modify

✅ What Works Right Now
Beautiful contact form

Saves to database

Email validation

Success confirmation

View all messages

Mobile responsive

Secure against basic attacks

📞 Contact
Made by Christelle
GitHub: @Christelle-12
Feel free to modify and improve!