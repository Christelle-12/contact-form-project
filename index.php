<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FGT Torun prayer form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Prayer Changes Everything</h2>
          <p class="motto">“The prayer of a righteous person is powerful and effective.” — James 5:16.</p>
        <form id="churchForm" action="process.php" method="POST">
            
            <!-- Personal Info -->
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required placeholder="Enter your full name" minlength="2">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="telephone">Telephone:</label>
                <input type="tel" id="telephone" name="telephone" placeholder="Enter your phone number" pattern="[0-9]{8,15}">
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" placeholder="Enter your address">
            </div>

            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" min="0" placeholder="Enter your age">
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender">
                    <option value="">Select</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- Membership Info -->
            <div class="form-group">
                <label>Member or Visitor:</label><br>
                <input type="radio" name="member_visitor" value="Member" checked> Member
                <input type="radio" name="member_visitor" value="Visitor"> Visitor
            </div>

            <!-- Prayer / Message -->
            <div class="form-group">
                <label for="message">Prayer Request or Message:</label>
                <textarea id="messageInput" name="message" placeholder="Type your prayer request or message here..." required minlength="10"></textarea>
            </div>

            <button type="submit">Submit</button>
        </form>
        
        <div id="message" class="message"></div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>