<?php
/**
 * Template Name: Donate
 * Description: Donation page with Paystack payment integration for KES
 */

// Security check
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Handle AJAX requests for payment verification
add_action('wp_ajax_verify_paystack_payment', 'verify_paystack_payment');
add_action('wp_ajax_nopriv_verify_paystack_payment', 'verify_paystack_payment');

function verify_paystack_payment() {
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'paystack_verification_nonce')) {
        wp_die('Security check failed');
    }
    
    $reference = sanitize_text_field($_POST['reference']);
    
    // PAYSTACK CONFIGURATION - REPLACE WITH YOUR ACTUAL KEYS
    $test_secret_key = 'sk_test_your_secret_key_here'; // Replace with your test secret key
    $live_secret_key = 'sk_live_your_live_secret_key_here'; // Replace with your live secret key
    $is_live = false; // Set to true for live environment
    
    $secret_key = $is_live ? $live_secret_key : $test_secret_key;
    
    // Verify payment with Paystack API
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $reference,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . $secret_key,
            "Cache-Control: no-cache",
        ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        wp_send_json_error('Payment verification failed: ' . $err);
    } else {
        $result = json_decode($response, true);
        
        if ($result['status'] == true && $result['data']['status'] == 'success') {
            // Payment successful - you can save to database here
            $amount = $result['data']['amount'] / 100; // Convert from kobo to KES
            $email = $result['data']['customer']['email'];
            
            // Optional: Save donation to database
            save_donation_to_database($reference, $amount, $email, $result['data']);
            
            wp_send_json_success([
                'message' => 'Thank you! Your donation of KES ' . number_format($amount, 2) . ' has been processed successfully.',
                'amount' => $amount,
                'reference' => $reference
            ]);
        } else {
            wp_send_json_error('Payment verification failed. Please contact support.');
        }
    }
}

function save_donation_to_database($reference, $amount, $email, $transaction_data) {
    global $wpdb;
    
    // Create donations table if it doesn't exist
    $table_name = $wpdb->prefix . 'donations';
    
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        reference varchar(100) NOT NULL,
        amount decimal(10,2) NOT NULL,
        email varchar(100) NOT NULL,
        status varchar(50) NOT NULL,
        transaction_data text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Insert donation record
    $wpdb->insert(
        $table_name,
        array(
            'reference' => $reference,
            'amount' => $amount,
            'email' => $email,
            'status' => 'completed',
            'transaction_data' => json_encode($transaction_data)
        )
    );
}

get_header();
?>
<section class="shadow-sm">
    <div class="h-[500px] md:h-[400px] lg:h-[554px] relative"
        style="background-color: #0f6041;">
        <div class="absolute inset-0 flex items-end">
            <div class="container mx-auto w-full px-10 lg:px-0 pb-4 md:pb-12 lg:pb-12 text-white">
                <h1 class="text-2xl md:text-4xl lg:text-4xl font-bold" data-aos="fade-up" data-aos-delay="200">
                    <?php the_title(); ?>
                </h1>
                <p class="text-base lg:text-lg my-4 max-w-4xl" data-aos="fade-up" data-aos-delay="400">
                    Join our volunteer network and play a vital role in mobilizing communities, educating citizens, and safeguarding the electoral process.
                </p>
                <a href="/volunteer" class="inline-block bg-black text-white px-5 py-2 mt-4 font-bold text-lg shadow-md hover:bg-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-gray-500 transition flex items-center">
                    I want to help
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>
<img src="/wp-content/uploads/2025/08/BM-TERRACE-BRANDING-Final-1_page-0001_11zon-1-scaled.jpg" class="w-full shadow-sm object-contain" alt="">

<style>
    /* Minimal styling for donation page */
    .donate-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
        font-family: Arial, sans-serif;
    }

    .donate-form {
        background: #f9f9f9;
        padding: 2rem;
        border-radius: 8px;
        margin: 2rem 0;
    }

    .amount-tiers {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
        margin: 1rem 0;
    }

    .amount-btn {
        padding: 1rem;
        border: 2px solid #ddd;
        background: white;
        cursor: pointer;
        text-align: center;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .amount-btn:hover,
    .amount-btn.selected {
        border-color: #007cba;
        background: #007cba;
        color: white;
    }

    .custom-amount {
        width: 100%;
        padding: 1rem;
        border: 2px solid #ddd;
        border-radius: 4px;
        font-size: 1.1rem;
        margin: 1rem 0;
    }

    .payment-type {
        margin: 1.5rem 0;
    }

    .payment-type label {
        margin-right: 1rem;
        cursor: pointer;
    }

    .donate-btn {
        background: #28a745;
        color: white;
        padding: 1rem 2rem;
        border: none;
        border-radius: 4px;
        font-size: 1.2rem;
        cursor: pointer;
        width: 100%;
        margin: 1rem 0;
    }

    .donate-btn:hover {
        background: #218838;
    }

    .donate-btn:disabled {
        background: #6c757d;
        cursor: not-allowed;
    }

    .message {
        padding: 1rem;
        border-radius: 4px;
        margin: 1rem 0;
    }

    .success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .transparency {
        background: #e9ecef;
        padding: 1.5rem;
        border-radius: 4px;
        margin: 2rem 0;
    }

    .loading {
        display: none;
        text-align: center;
    }

    @media (max-width: 600px) {
        .donate-container {
            padding: 1rem;
        }
        
        .amount-tiers {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="donate-container">    
    <div id="message-container"></div>
    
    <div class="donate-form">
        <h3 class="text-4xl font-bold">Choose Your Contribution Amount</h3>
        <p class="text-lg mt-3"><em>Every contribution counts.</em></p>
        
        <div class="amount-tiers">
            <div class="amount-btn" data-amount="100">KES 100</div>
            <div class="amount-btn" data-amount="500">KES 500</div>
            <div class="amount-btn" data-amount="1000">KES 1,000</div>
            <div class="amount-btn" data-amount="5000">KES 5,000</div>
        </div>
        
        <input type="number" 
               class="custom-amount" 
               placeholder="Enter custom amount (KES)" 
               min="10" 
               step="1" 
               id="custom-amount">
        
        <div class="payment-type">
            <label>
                <input type="radio" name="payment_type" value="one-time" checked> One-time donation
            </label>
            <label>
                <input type="radio" name="payment_type" value="recurring"> Monthly recurring
            </label>
        </div>
        
        <input type="email" 
               id="donor-email" 
               placeholder="Your email address" 
               required 
               style="width: 100%; padding: 1rem; border: 2px solid #ddd; border-radius: 4px; margin: 1rem 0;">
        
        <button id="donate-btn" class="donate-btn">Proceed to Payment</button>
        
        <div class="loading" id="loading">
            <p>Processing your donation...</p>
        </div>
    </div>
    
    <div class="transparency">
        <h3 class="text-2xl font-bold">How Your Donation Makes a Difference</h3>
        <p class="text-lg font-normal mt-3">Your contribution directly funds:</p>
        <ul class="text-base font-normal my-5 list-disc space-y-6 px-8">
            <li><strong>Grassroots Mobilization:</strong> Organizing community meetings, training volunteers, and building local networks to amplify our message at the ground level.</li>
            <li><strong>Outreach Programs:</strong> Digital campaigns, printed materials, media advertisements, and communication tools to reach voters across all demographics.</li>
            <li><strong>Logistics & Operations:</strong> Event coordination, transportation, venue rentals, and administrative costs to ensure smooth campaign operations.</li>
        </ul>
        <p>We maintain full transparency in our expenditures and regularly publish financial reports showing how every shilling is utilized to advance our campaign objectives.</p>
    </div>
</div>

<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // PAYSTACK CONFIGURATION - REPLACE WITH YOUR ACTUAL KEYS
    const PAYSTACK_PUBLIC_KEY = 'pk_live_51c7017a7c85efa034e52f8298c8aa54968be126'; // Replace with your test public key
    // For live: const PAYSTACK_PUBLIC_KEY = 'pk_live_your_live_public_key_here';
    
    let selectedAmount = 0;
    let paymentType = 'one-time';
    
    // Amount selection handling
    const amountBtns = document.querySelectorAll('.amount-btn');
    const customAmount = document.getElementById('custom-amount');
    
    amountBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove selected class from all buttons
            amountBtns.forEach(b => b.classList.remove('selected'));
            // Add selected class to clicked button
            this.classList.add('selected');
            // Set selected amount
            selectedAmount = parseInt(this.dataset.amount);
            // Clear custom amount input
            customAmount.value = '';
        });
    });
    
    customAmount.addEventListener('input', function() {
        // Remove selected class from all preset buttons
        amountBtns.forEach(btn => btn.classList.remove('selected'));
        // Set selected amount to custom input
        selectedAmount = parseInt(this.value) || 0;
    });
    
    // Payment type handling
    document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            paymentType = this.value;
        });
    });
    
    // Donation button handling
    document.getElementById('donate-btn').addEventListener('click', function() {
        const email = document.getElementById('donor-email').value;
        
        if (!email) {
            showMessage('Please enter your email address.', 'error');
            return;
        }
        
        if (!selectedAmount || selectedAmount < 10) {
            showMessage('Please select a donation amount of at least KES 10.', 'error');
            return;
        }
        
        // Show loading state
        this.disabled = true;
        document.getElementById('loading').style.display = 'block';
        
        initiatePayment(selectedAmount, email, paymentType);
    });
    
    function initiatePayment(amount, email, type) {
        // Generate unique reference
        const reference = 'donation_' + Math.floor(Math.random() * 1000000000) + '_' + Date.now();
        
        let plan = null;
        if (type === 'recurring') {
            // For recurring payments, you would need to create a plan first
            // This is a simplified implementation
            plan = 'PLN_monthly_' + amount; // You need to create this plan on Paystack dashboard
        }
        
        const handler = PaystackPop.setup({
            key: PAYSTACK_PUBLIC_KEY,
            email: email,
            amount: amount * 100, // Convert to kobo (Paystack uses kobo, not KES)
            currency: 'KES',
            ref: reference,
            plan: plan, // Only used for recurring payments
            metadata: {
                payment_type: type,
                source: 'campaign_website'
            },
            callback: function(response) {
                // Payment successful, verify on server
                verifyPayment(response.reference);
            },
            onClose: function() {
                // Payment cancelled
                resetForm();
                showMessage('Payment was cancelled.', 'error');
            }
        });
        
        handler.openIframe();
    }
    
    function verifyPayment(reference) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'verify_paystack_payment',
                reference: reference,
                nonce: '<?php echo wp_create_nonce('paystack_verification_nonce'); ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            resetForm();
            if (data.success) {
                showMessage(data.data.message, 'success');
                // Optional: Redirect to thank you page
                // window.location.href = '/thank-you/';
            } else {
                showMessage(data.data || 'Payment verification failed. Please contact support.', 'error');
            }
        })
        .catch(error => {
            resetForm();
            showMessage('Network error occurred. Please try again.', 'error');
            console.error('Error:', error);
        });
    }
    
    function showMessage(message, type) {
        const container = document.getElementById('message-container');
        container.innerHTML = `<div class="message ${type}">${message}</div>`;
        container.scrollIntoView({ behavior: 'smooth' });
    }
    
    function resetForm() {
        document.getElementById('donate-btn').disabled = false;
        document.getElementById('loading').style.display = 'none';
    }
});
</script>

<?php get_footer(); ?>