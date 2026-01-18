    <footer>
        <div>
            <div class="social">
            <?php if( get_theme_mod('youtube_url') ): ?>
                <a href="<?php echo esc_url( get_theme_mod('youtube_url') ); ?>" target="_blank" rel="noopener noreferrer">
                    <ion-icon name="logo-youtube"></ion-icon>
                </a>
            <?php endif; ?>
            
            <?php if( get_theme_mod('instagram_url') ): ?>
                <a href="<?php echo esc_url( get_theme_mod('instagram_url') ); ?>" target="_blank" rel="noopener noreferrer">
                    <ion-icon name="logo-instagram"></ion-icon>
                </a>
            <?php endif; ?>
            
            <?php if( get_theme_mod('mastodon_url') ): ?>
                <a href="<?php echo esc_url( get_theme_mod('mastodon_url') ); ?>" target="_blank" rel="noopener noreferrer">
                    <ion-icon name="logo-mastodon"></ion-icon>
                </a>
            <?php endif; ?>
            </div>
        <p>&copy; <?php echo esc_html( get_bloginfo('name') ); ?> - <?php echo esc_html( date('Y') ); ?></p>
        </div>
        <div class="legal">
            <a href="<?php echo esc_url(get_permalink(50)); ?>">Mentions Légales</a>
            <a href="<?php echo esc_url(get_permalink(253)); ?>">Politique de confidentialité</a>
        </div>
        
    </footer>
</section> 

<?php wp_footer(); ?>
</body>
</html>