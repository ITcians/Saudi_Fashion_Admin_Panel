<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Custom Green Color Theme */
    :root {
      --primary-color: #28a745;  /* Green */
      --secondary-color: #155724; /* Darker Green */
    }

    /* Header Styling */
    .header {
      background-color: var(--primary-color);
      padding: 10px 0;
    }

    .header img {
      height: 50px;
      margin-left: 20px;
    }

    .banner {
      background-color: var(--secondary-color);
      color: white;
      padding: 40px 0;
      text-align: center;
    }

    .banner h1 {
      font-size: 36px;
      font-weight: bold;
    }

    .policy-content {
      padding: 30px;
    }

    .policy-content h2 {
      color: var(--primary-color);
    }

    .policy-content p {
      font-size: 1.1rem;
      line-height: 1.6;
    }

    /* Footer */
    .footer {
      background-color: #f8f9fa;
      padding: 20px;
      text-align: center;
      color: #6c757d;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .banner h1 {
        font-size: 28px;
      }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header class="header">
    <div class="container d-flex align-items-center">
      <img src="assets/images/brands/1715779200.png" alt="Logo" class="img-fluid">
      <h1 class="ms-3 text-white">
      </h1>
    </div>
  </header>

  <!-- Banner Section -->
  <section class="banner">
    <div class="container">
      <h1>Privacy Policy</h1>
      <p>Last updated: November 2024</p>
    </div>
  </section>

  <!-- Privacy Policy Content -->
  <div class="container policy-content">
    <h2>Introduction</h2>
    <p>Welcome to our Privacy Policy page! We respect your privacy and are committed to protecting your personal data. This Privacy Policy explains how we collect, use, and protect your information.</p>

    <h2>Information We Collect</h2>
    <p>We collect information in several ways, including when you provide it directly to us and when we collect it automatically through the use of our website. This includes personal details like name, email, and IP address.</p>

    <h2>How We Use Your Information</h2>
    <p>Your personal data is used to enhance your experience with our service, improve our website, and send promotional materials if you have opted in. We do not share your personal information with third parties unless required by law.</p>

    <h2>Data Protection</h2>
    <p>We implement various security measures to safeguard your data, including encryption and access control. However, please note that no method of transmission over the internet is 100% secure.</p>

    <h2>Cookies</h2>
    <p>We use cookies to improve your browsing experience. By using our website, you agree to the use of cookies as outlined in this policy.</p>

    <h2>Your Rights</h2>
    <p>You have the right to access, correct, or delete your personal information. You may also withdraw your consent at any time. For any inquiries, please contact us at the details below.</p>

    <h2>Changes to This Policy</h2>
    <p>We may update this Privacy Policy from time to time. Any changes will be posted on this page with an updated "Last Updated" date.</p>

    <h2>Contact Us</h2>
    <p>If you have any questions or concerns regarding this Privacy Policy, please contact us at <strong>   <?php  
        $setting = App\Models\SettingModel::where('key', 'brand_name')->get()->first();
        if ($setting) {
            echo $setting->value;
        } else {
            echo 'Brand name not found';
        }
        ?>@company.com</strong>.</p>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <p>&copy; 2024  
        <?php  
        $setting = App\Models\SettingModel::where('key', 'brand_name')->get()->first();
        if ($setting) {
            echo $setting->value;
        } else {
            echo 'Brand name not found';
        }
        ?>. All rights reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
