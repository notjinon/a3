<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>IE332 Group 7</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@400;700&family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

    <!-- IE332 Group 7 header at top of login page -->
    <header>
        <div>
            <h1>IE332 Group 7</h1>
        </div>
    </header>

    <div>
        <!-- Group Member Image Placeholders -->
        <div class="member-container">
            <div class="member">
                <div class="image-box"></div>
                <div>Lance Kim</div>
            </div>
            <div class="member">
                <div class="image-box"></div>
                <div>Jackson Gray</div>
            </div>
            <div class="member">
                <img src="pictures/Cover PIc.jpg" class="image-box">
                <div>Suchir Peyyeti</div>
            </div>
            <div class="member">
                <div class="image-box"></div>
                <div>Jino Nicolas</div>
            </div>
            <div class="member">
                <div class="image-box"></div>
                <div>Aryan Deshpande</div>
            </div>
        </div>

        <div class="container">
            <div class="left-section">
                <form action="login_auth.php" method="POST" class="form-container">
                    <input type="text" name="username" placeholder="Username" required />
                    <input type="password" name="password" placeholder="Password" required />
                    <button type="submit">Login</button>
                </form>

                <form action="empty_database.php" method="POST" class="form-container" style="margin-top:10px;">
                    <button type="submit">Empty Database</button>
                </form>
            </div>

            <!-- Right Section -->
            <div class="right-section">
                <form action="submit_complaint.php" method="POST" class="form-container">
                    <input type="text" name="first name" placeholder="Your First Name" required />
                    <input type ="text" name="last name" placeholder ="Your Last Name" required />
                    <input type="email" name="email" placeholder="Your Email" required />
                    <textarea name="complaint" placeholder="Type your complaint here..." required></textarea>
                    <button type="submit">Submit Complaint</button>
                </form>
            </div>
        </div>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div id='errorMessage' class='error'>" . htmlspecialchars($_SESSION['error']) . "</div>";
            unset($_SESSION['error']);
        } else if (isset($_SESSION['success'])) {
            echo "<div id='successMessage' class='success'>" . htmlspecialchars($_SESSION['success']) . "</div>";
            unset($_SESSION['success']);
        }
        ?>
    </div>
</body>
</html>
