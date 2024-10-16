@extends('layouts.shop')
@section('content')
<link href="{{ asset('assets/vendor/css/faqshelp.css') }}" rel="stylesheet">

<!-- FAQs & Help Page Start -->
<div class="faqs-container">
    <h1>FAQs & Help</h1>

    <h2>General Questions</h2>
    <div class="faq-item">
        <h3>How can I create an account?</h3>
        <p>You can create an account by clicking on the "Sign Up" button at the top right corner of the homepage. Fill in your details, and you’ll receive a confirmation email to activate your account.</p>
    </div>

    <div class="faq-item">
        <h3>What payment methods do you accept?</h3>
        <p>We accept various payment methods including credit cards, PayPal, and bank transfers. You can view the full list during the checkout process.</p>
    </div>

    <h2>Shipping & Delivery</h2>
    <div class="faq-item">
        <h3>How long does delivery take?</h3>
        <p>Delivery times vary based on your location. Typically, domestic orders are delivered within 3-5 business days, while international orders may take up to 10-15 business days.</p>
    </div>

    <div class="faq-item">
        <h3>Can I track my order?</h3>
        <p>Yes, once your order is shipped, we will provide a tracking number so you can monitor your shipment’s progress in real-time.</p>
    </div>

    <h2>Returns & Refunds</h2>
    <div class="faq-item">
        <h3>What is your return policy?</h3>
        <p>You can return items within 30 days of purchase if they are in their original condition. Please contact our support team to initiate a return.</p>
    </div>

    <div class="faq-item">
        <h3>How do I request a refund?</h3>
        <p>Refunds can be requested through your account. Navigate to "Order History", select the order, and click on the "Request Refund" button.</p>
    </div>
</div>
<!-- FAQs & Help Page End -->
@endsection
