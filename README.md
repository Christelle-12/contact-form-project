# 📬 Contact Form Project

A **beautiful, secure, and fully functional contact form** with database integration, designed for real-world use. This project demonstrates clean UI design, client-side validation, and reliable server-side processing using PHP and MySQL.

🔗 **Live Demo** · 📸 **Screenshots** · ⚡ **Quick Start**

---

## ✨ Features at a Glance

| Feature                 | Description                                                   |
| ----------------------- | ------------------------------------------------------------- |
| 🎨 **Modern Design**    | Clean, responsive layout with gradients and subtle animations |
| 📱 **Mobile-Friendly**  | Optimized for phones, tablets, and desktops                   |
| 🔒 **Secure**           | Protected against SQL injection and basic attacks             |
| 📊 **Admin Dashboard**  | View all submissions in a structured table                    |
| ⚡ **Fast & Responsive** | Real-time validation and instant submission                   |
| 📧 **Email Validation** | Ensures correct email format before submission                |

---

## 🖼️ Screenshots

| Contact Form                                                                       | Success Page                                                                        | Submissions Dashboard                                                             |
| ---------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------- | --------------------------------------------------------------------------------- |
| ![](screenshots/contact-form.png) | ![](screenshots/success-page.png) | ![](screenshots/submissions-dashboard.png) |

---

## 🚀 Quick Start Guide

### Step 1: Download & Install

```bash
# Clone the repository
git clone https://github.com/Christelle-12/contact-form-project.git

# Move the project to your web server directory
mv contact-form-project /path/to/your/htdocs/
```

---

### Step 2: Database Setup

1. Open **XAMPP/WAMP** and start **Apache** and **MySQL**
2. Go to: `http://localhost/phpmyadmin`
3. Create a new database named **webform_db**
4. Run the SQL below:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### Step 3: Configuration

```php
// Copy configuration file
cp config.example.php config.php

// Update database credentials
$servername = "localhost";
$username = "root";
$password = "";
$database = "webform_db";
```

---

### Step 4: Launch the Application

🌐 Open your browser and visit:

```
http://localhost/contact-form-project/
```

---

## 📁 Project Structure

```text
contact-form-project/
│
├── index.html          # Main contact form
├── style.css           # UI styling and animations
├── script.js           # Client-side validation
├── db.php              # Database connection logic
├── process.php         # Form submission handler
├── success.html        # Submission success page
├── view_data.php       # Admin view for submissions
├── config.example.php  # Configuration template
└── README.md           # Project documentation
```

---

## 🛠️ Customization Guide

### 🎨 Change Theme Colors

Edit `style.css`:

```css
:root {
    --primary-color: #3498db;
    --success-color: #2ecc71;
    --background: #f5f7fa;
}

button {
    background: linear-gradient(45deg, var(--primary-color), var(--success-color));
}
```

---

### ➕ Add New Form Fields

**HTML (index.html)**

```html
<div class="form-group">
    <label>📞 Phone Number</label>
    <input type="tel" name="phone" placeholder="Your phone number">
</div>
```

Then update `process.php` and the database schema accordingly.

---

## 🔧 Troubleshooting

| Issue                     | Solution                           |
| ------------------------- | ---------------------------------- |
| Form does not submit      | Ensure Apache & MySQL are running  |
| Database connection error | Verify credentials in `config.php` |
| Styles not loading        | Hard refresh (Ctrl + F5)           |
| Submissions not visible   | Confirm table name is `users`      |

---

## 📞 Useful Field Additions

```html
<!-- Phone Number -->
<input type="tel" name="phone" placeholder="+1 234 567 890">

<!-- Subject Dropdown -->
<select name="subject">
    <option value="">Select a subject</option>
    <option value="support">Technical Support</option>
    <option value="sales">Sales Inquiry</option>
    <option value="general">General Question</option>
</select>

<!-- Newsletter Checkbox -->
<label><input type="checkbox" name="newsletter"> Subscribe to newsletter</label>
```

---

## 🌟 Pro Tips & Enhancements

* Enable **email notifications** for new submissions
* Add **CAPTCHA** to prevent spam
* Implement **CSV export** for submissions
* Add **search and filtering** to the admin dashboard
* Create an **auto-reply email** for users

---

## 👩‍💻 About the Developer

**Christelle**
Building practical, user-friendly web solutions with clean and maintainable code.

📧 Email: [mariechristellenirere@gmail.com](mailto:mariechristellenirere@gmail.com)
🐙 GitHub: [https://github.com/Christelle-12](https://github.com/Christelle-12)

---

## 📄 License

This project is open source and available under the **MIT License**.

---

<div align="center">

🌟 *Found this useful? Give it a star!* ⭐
⬆ **Back to Top**

</div>
