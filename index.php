<?php
$status = $_GET['status'] ?? '';
$isSuccess = $status === 'success';
$isError = $status === 'error';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Message Desk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="public-page">
    <main class="public-shell">
        <section class="hero-card">
            <div class="hero-copy">
                <span class="eyebrow">Church Care Desk</span>
                <h1>Share a prayer request, suggestion, or private note.</h1>
                <p class="lead">
                    This simple form helps church attendees send messages to the admin team.
                    Only your message and whether you are a member or visitor are required.
                </p>
                <div class="hero-quote">
                    "Carry each other's burdens, and in this way you will fulfill the law of Christ."
                    <span>Galatians 6:2</span>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-header">
                    <h2>Send a Message</h2>
                    <p>We read every message with care.</p>
                </div>

                <?php if ($isSuccess): ?>
                    <div class="alert success">Your message was received successfully.</div>
                <?php elseif ($isError): ?>
                    <div class="alert error">We could not send your message. Please try again.</div>
                <?php endif; ?>

                <form id="churchForm" action="process.php" method="POST" novalidate>
                    <div class="field-group">
                        <label for="full_name">Full name <span>Optional</span></label>
                        <input type="text" id="full_name" name="full_name" maxlength="100" placeholder="Your name">
                    </div>

                    <div class="field-group">
                        <label for="telephone">Telephone <span>Optional</span></label>
                        <input type="tel" id="telephone" name="telephone" maxlength="20" placeholder="Phone number">
                    </div>

                    <fieldset class="choice-group">
                        <legend>Are you a member or visitor? <span>Required</span></legend>
                        <label class="choice-pill">
                            <input type="radio" name="member_visitor" value="Member" checked>
                            <span>Member</span>
                        </label>
                        <label class="choice-pill">
                            <input type="radio" name="member_visitor" value="Visitor">
                            <span>Visitor</span>
                        </label>
                        <label class="choice-pill">
                            <input type="radio" name="member_visitor" value="Other">
                            <span>Other</span>
                        </label>
                    </fieldset>

                    <div class="field-group">
                        <label for="messageInput">Message <span>Required</span></label>
                        <textarea id="messageInput" name="message" rows="6" maxlength="5000" placeholder="Write your message, prayer request, or suggestion here..." required></textarea>
                    </div>

                    <button type="submit" class="primary-btn">Send Message</button>
                    <div id="message" class="alert error" hidden></div>
                </form>
            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>
