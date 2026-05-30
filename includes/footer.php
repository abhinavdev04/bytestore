</main>

<footer class="site-footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <div class="footer-brand__logo">
                <span style="color:var(--color-accent);">Byte</span>Store
            </div>
            <p class="footer-brand__desc">Nepal's premium destination for laptops, smartphones, gaming gear, and cutting-edge electronics. Official warranty, competitive prices, and expert support.</p>
            <div class="footer-social">
                <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook">f</a>
                <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">ig</a>
                <a href="https://www.whatsapp.com/" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">wa</a>
            </div>
        </div>
        <div class="footer-col">
            <h4 class="footer-col__title">Shop</h4>
            <ul class="footer-col__links">
                <li><a href="<?php echo e(getBasePath()); ?>customer/shop.php">All Products</a></li>
                <li><a href="<?php echo e(getBasePath()); ?>customer/shop.php?category=1">Laptops</a></li>
                <li><a href="<?php echo e(getBasePath()); ?>customer/shop.php?category=17">Phones</a></li>
                <li><a href="<?php echo e(getBasePath()); ?>customer/shop.php?category=3">Gaming</a></li>
                <li><a href="<?php echo e(getBasePath()); ?>customer/shop.php?sort=bestseller">Best Sellers</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4 class="footer-col__title">Support</h4>
            <ul class="footer-col__links">
                <li><a href="<?php echo e(getBasePath()); ?>pages/contact.php">Contact Us</a></li>
                <li><a href="<?php echo e(getBasePath()); ?>pages/faq.php">FAQ</a></li>
                <li><a href="<?php echo e(getBasePath()); ?>pages/shipping.php">Shipping Info</a></li>
                <li><a href="<?php echo e(getBasePath()); ?>pages/returns.php">Return Policy</a></li>
                <li><a href="<?php echo e(getBasePath()); ?>pages/warranty.php">Warranty</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4 class="footer-col__title">Company</h4>
            <ul class="footer-col__links">
                <li><a href="<?php echo e(getBasePath()); ?>pages/about.php">About Us</a></li>
                <li><a href="<?php echo e(getBasePath()); ?>pages/terms.php">Terms & Conditions</a></li>
                <li><a href="<?php echo e(getBasePath()); ?>employee/login.php">Staff Login</a></li>
            </ul>
            <div style="margin-top:1rem;font-size:0.8rem;opacity:0.7;line-height:1.8;">
                <i class="fa-solid fa-location-dot"></i> Times Square Mall, Durbar Marg<br>
                Kathmandu 44600, Nepal<br>
                <i class="fa-solid fa-phone"></i> +977-980-355-8024<br>
                <i class="fa-solid fa-envelope"></i> support@bytestore.com.np
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; <?php echo date('Y'); ?> ByteStore. All rights reserved.</span>
        <span>Secure payments via eSewa &amp; COD</span>
    </div>
</footer>

<button id="back-to-top" class="back-to-top" aria-label="Back to top">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>
</button>

<?php include __DIR__ . '/chatbot.php'; ?>
<?php include __DIR__ . '/modal.php'; ?>

<script src="<?php echo e(getBasePath()); ?>assets/js/admin-search.js"></script>
<script src="<?php echo e(getBasePath()); ?>assets/js/modal.js"></script>
<script src="<?php echo e(getBasePath()); ?>assets/js/quantity.js"></script>
<script src="<?php echo e(getBasePath()); ?>assets/js/main.js"></script>
<script src="<?php echo e(getBasePath()); ?>assets/js/search.js"></script>
<script src="<?php echo e(getBasePath()); ?>assets/js/chatbot.js"></script>
<script src="<?php echo e(getBasePath()); ?>assets/js/validation.js"></script>
<script src="<?php echo e(getBasePath()); ?>assets/js/sale-countdown.js"></script>
<script src="<?php echo e(getBasePath()); ?>assets/js/categories-scroll.js"></script>
</body>
</html>
