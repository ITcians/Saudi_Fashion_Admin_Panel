<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terms and Conditions</title>
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
      <h1>Terms and Conditions</h1>
      <p>Last updated: November 2024</p>
    </div>
  </section>

  <!-- Terms and Conditions Content -->
  <div class="container policy-content">
    <h2>1. Introduction</h2>
    <p>Welcome to <?php  
        $setting = App\Models\SettingModel::where('key', 'brand_name')->get()->first();
        if ($setting) {
            echo $setting->value;
        } else {
            echo 'Brand name not found';
        }
        ?>. By accessing or using our website or services, you agree to comply with and be bound by the following terms and conditions. If you do not agree with these terms, please do not use our website.</p>

    <h2>2. Use of Our Website</h2>
    <p>You may use our website for lawful purposes only. You agree to not use our website in any manner that could damage, disable, overburden, or impair the website's functionality or interfere with any other party's use of the site.</p>

    <h2>3. Intellectual Property</h2>
    <p>All content, images, and materials available on this website are protected by copyright and trademark laws. You may not copy, reproduce, distribute, or display any materials from our site without our written consent.</p>

    <h2>4. Limitation of Liability</h2>
    <p>In no event shall <?php  
        $setting = App\Models\SettingModel::where('key', 'brand_name')->get()->first();
        if ($setting) {
            echo $setting->value;
        } else {
            echo 'Brand name not found';
        }
        ?> be liable for any direct, indirect, incidental, special, or consequential damages arising from your use or inability to use the website or services, even if we have been advised of the possibility of such damages.</p>

    <h2>5. Privacy</h2>
    <p>Your privacy is important to us. Please refer to our <a href="{{url('privacy-policy')}}">Privacy Policy</a> for details on how we collect and use your personal data.</p>

    <h2>6. Changes to the Terms</h2>
    <p>We may update these Terms and Conditions from time to time. Any changes will be posted on this page with an updated "Last Updated" date. Please check periodically for updates.</p>

    <h2>7. Governing Law</h2>
    <p>These Terms and Conditions are governed by and construed in accordance with the laws of [Jurisdiction]. Any disputes arising from these terms shall be resolved in the courts located within [Jurisdiction].</p>

    <h2>8. Contact Us</h2>
    <p>If you have any questions about these Terms and Conditions, please contact us at: <strong> <?php  
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
    <p>&copy; 2024 <?php  
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
