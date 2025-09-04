<?php
session_start(); // needed for auth check
$title = "Privacy - JKKNIU Marketplace";

ob_start();
?>
    <h1>Privacy Policy for [Your Company Name]</h1>
    <p class="last-updated"><strong>Last Updated:</strong> [Date]</p>

    <h2>1. Introduction</h2>
    <p>Welcome to [Your Company Name] ("we," "our," or "us"). We are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website [Your Website URL], use our mobile application [Your App Name], or use our services (collectively, the "Services").</p>
    <p>Please read this Privacy Policy carefully. By using our Services, you agree to the collection and use of information in accordance with this policy. If you do not agree with the terms of this privacy policy, please do not access the site or use our services.</p>
    <p>We reserve the right to make changes to this Privacy Policy at any time and for any reason. We will alert you about any changes by updating the "Last Updated" date of this Privacy Policy.</p>

    <h2>2. Information We Collect</h2>
    <p>We may collect information about you in a variety of ways. The information we may collect via the Services includes:</p>
    
    <h3>A. Personal Data You Provide to Us</h3>
    <ul>
        <li><strong>Personally identifiable information,</strong> such as your name, shipping address, email address, and telephone number, and demographic information, such as your age, gender, hometown, and interests, that you voluntarily give to us when you register with the Services or when you choose to participate in various activities related to the Services, such as online chat and message boards.</li>
        <li><strong>Financial information,</strong> such as data related to your payment method (e.g., valid credit card number, card brand, expiration date) that we may collect when you purchase, order, return, exchange, or request information about our services. [Note: You should specify if this data is stored by you or passed directly to a third-party payment processor].</li>
    </ul>

    <h3>B. Data We Collect Automatically</h3>
    <p>Information our servers automatically collect when you access the Services, such as your IP address, your browser type, your operating system, your access times, and the pages you have viewed directly before and after accessing the Services. This is often referred to as "Usage Data" or "Log Data."</p>

    <h3>C. Information from Tracking Technologies & Cookies</h3>
    <p>We may use cookies, web beacons, tracking pixels, and other tracking technologies on the Services to help customize the Services and improve your experience. When you access the Services, your personal information is not collected through the use of tracking technology. Most browsers are set to accept cookies by default. You can remove or reject cookies, but be aware that such action could affect the availability and functionality of the Services.</p>
    <p>For more information on how we use cookies, please refer to our Cookie Policy [Optional: Link to a separate Cookie Policy if you have one].</p>

    <h2>3. How We Use Your Information</h2>
    <p>Having accurate information about you permits us to provide you with a smooth, efficient, and customized experience. Specifically, we may use information collected about you via the Services to:</p>
    <ul>
        <li>Create and manage your account.</li>
        <li>Process your transactions and deliveries.</li>
        <li>Email you regarding your account or order.</li>
        <li>Respond to customer service requests.</li>
        <li>Send you a newsletter or other promotional materials.</li>
        <li>Monitor and analyze usage and trends to improve your experience with the Services.</li>
        <li>Prevent fraudulent transactions, monitor against theft, and protect against criminal activity.</li>
        <li>Comply with legal and regulatory requirements.</li>
    </ul>

    <h2>4. How We Share Your Information</h2>
    <p>We may share information we have collected about you in certain situations. Your information may be disclosed as follows:</p>
    
    <h3>A. By Law or to Protect Rights</h3>
    <p>If we believe the release of information about you is necessary to respond to legal process, to investigate or remedy potential violations of our policies, or to protect the rights, property, and safety of others, we may share your information as permitted or required by any applicable law, rule, or regulation.</p>

    <h3>B. Third-Party Service Providers</h3>
    <p>We may share your information with third parties that perform services for us or on our behalf, including payment processing, data analysis, email delivery, hosting services, customer service, and marketing assistance.</p>

    <h3>C. Business Transfers</h3>
    <p>We may share or transfer your information in connection with, or during negotiations of, any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.</p>

    <h3>D. With Your Consent</h3>
    <p>We may disclose your personal information for any other purpose with your consent. We do not sell your personal information to third parties.</p>

    <h2>5. Data Security</h2>
    <p>We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable, and no method of data transmission can be guaranteed against any interception or other type of misuse.</p>

    <h2>6. Data Retention</h2>
    <p>We will retain your personal information only for as long as is necessary for the purposes set out in this Privacy Policy. We will retain and use your information to the extent necessary to comply with our legal obligations (for example, if we are required to retain your data to comply with applicable laws), resolve disputes, and enforce our legal agreements and policies.</p>

    <h2>7. Your Data Protection Rights</h2>
    <p>Depending on your location, you may have the following rights regarding your personal information:</p>
    <ul>
        <li><strong>The right to access</strong> – You have the right to request copies of your personal data.</li>
        <li><strong>The right to rectification</strong> – You have the right to request that we correct any information you believe is inaccurate or complete information you believe is incomplete.</li>
        <li><strong>The right to erasure</strong> – You have the right to request that we erase your personal data, under certain conditions.</li>
        <li><strong>The right to restrict processing</strong> – You have the right to request that we restrict the processing of your personal data, under certain conditions.</li>
        <li><strong>The right to object to processing</strong> – You have the right to object to our processing of your personal data, under certain conditions.</li>
        <li><strong>The right to data portability</strong> – You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.</li>
    </ul>
    <p>If you would like to exercise any of these rights, please contact us using the contact information below.</p>

    <h2>8. Children's Privacy</h2>
    <p>Our Services are not intended for use by children under the age of 13 [Note: This age may be 16 in some jurisdictions, like the EU under GDPR. Please verify for your target audience]. We do not knowingly collect personally identifiable information from children under 13. If we become aware that we have collected personal data from a child without verification of parental consent, we take steps to remove that information from our servers.</p>

    <h2>9. Contact Us</h2>
    <p>If you have questions or comments about this Privacy Policy, please contact us at:</p>
    <p>
        <strong>[Your Company Name]</strong><br>
        [Your Physical Address]<br>
        [Your Contact Email Address]<br>
        [Your Contact Phone Number]
    </p>

<?php
$content = ob_get_clean();

include './lib/layout.php';
