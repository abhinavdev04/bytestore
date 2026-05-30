<?php
require '_template.php';
renderStaticPage('Contact Us', '
<div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;">
    <div>
        <h2 style="margin-bottom:1rem;">Get in Touch</h2>
        <div style="line-height:2.2;color:var(--color-text-secondary);">
            <p><strong><i class="fa-solid fa-location-dot"></i> Store Location</strong><br>2nd Floor, Times Square Mall<br>Durbar Marg, Kathmandu<br>Bagmati 44600, Nepal</p>
            <p><strong><i class="fa-solid fa-phone"></i> Phone</strong><br>+977-980-355-8024</p>
            <p><strong><i class="fa-solid fa-envelope"></i> Email</strong><br><a href="mailto:support@bytestore.com.np">support@bytestore.com.np</a></p>
            <p><strong><i class="fa-solid fa-clock"></i> Business Hours</strong><br>Sunday – Friday: 10:00 AM – 7:00 PM<br>Saturday: 11:00 AM – 5:00 PM</p>
        </div>
    </div>
    <div>
        <h2 style="margin-bottom:1rem;">Send a Message</h2>
        <form data-validate-form onsubmit="event.preventDefault();showToast(\'Thank you! We will respond within 24 hours.\',\'success\');this.reset();">
            <div class="form-group"><label>Name</label><input type="text" name="name" data-validate="name" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" data-validate="email" required></div>
            <div class="form-group"><label>Subject</label><input type="text" name="subject" required></div>
            <div class="form-group"><label>Message</label><textarea name="message" rows="5" required minlength="10"></textarea></div>
            <button type="submit" class="btn btn--primary">Send Message</button>
        </form>
    </div>
</div>
');
