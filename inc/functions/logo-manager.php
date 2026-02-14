<?php
/**
 * Gestion des logos par thème
 */

// Ajouter un menu dans l'admin
add_action('admin_menu', 'gestion_logos_themes_menu');
function gestion_logos_themes_menu() {
    add_menu_page(
        'Gestion des Logos',
        'Logos Thèmes',
        'manage_options',
        'gestion-logos-themes',
        'gestion_logos_themes_page',
        'dashicons-format-image',
        30
    );
}

// Page d'administration
function gestion_logos_themes_page() {
    // Sauvegarder les données
    if (isset($_POST['submit_logos'])) {
        check_admin_referer('logos_themes_nonce');
        
        // Gérer les uploads
        if (!empty($_FILES['logo_light']['name'])) {
            $upload = wp_handle_upload($_FILES['logo_light'], array('test_form' => false));
            if (!isset($upload['error'])) {
                update_option('logo_theme_light', $upload['url']);
            }
        }
        if (!empty($_FILES['logo_dark']['name'])) {
            $upload = wp_handle_upload($_FILES['logo_dark'], array('test_form' => false));
            if (!isset($upload['error'])) {
                update_option('logo_theme_dark', $upload['url']);
            }
        }
        if (!empty($_FILES['logo_green']['name'])) {
            $upload = wp_handle_upload($_FILES['logo_green'], array('test_form' => false));
            if (!isset($upload['error'])) {
                update_option('logo_theme_green', $upload['url']);
            }
        }
        
        // Favicon
        if (!empty($_FILES['favicon']['name'])) {
            $upload = wp_handle_upload($_FILES['favicon'], array('test_form' => false));
            if (!isset($upload['error'])) {
                update_option('site_favicon', $upload['url']);
            }
        }
        
        echo '<div class="updated"><p>Logos mis à jour avec succès!</p></div>';
    }
    
    // Récupérer les logos actuels
    $logo_light = get_option('logo_theme_light', '');
    $logo_dark = get_option('logo_theme_dark', '');
    $logo_green = get_option('logo_theme_green', '');
    $favicon = get_option('site_favicon', '');
    
    ?>
    <div class="wrap">
        <h1>Gestion des Logos par Thème</h1>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('logos_themes_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label>Logo "Lilac Wine" (Thème Light)</label></th>
                    <td>
                        <?php if ($logo_light): ?>
                            <img src="<?php echo esc_url($logo_light); ?>" style="max-width: 200px; display: block; margin-bottom: 10px;">
                        <?php endif; ?>
                        <input type="file" name="logo_light" accept="image/*">
                        <p class="description">Logo pour le thème mauve clair</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label>Logo "Purple Rain" (Thème Dark)</label></th>
                    <td>
                        <?php if ($logo_dark): ?>
                            <img src="<?php echo esc_url($logo_dark); ?>" style="max-width: 200px; display: block; margin-bottom: 10px;">
                        <?php endif; ?>
                        <input type="file" name="logo_dark" accept="image/*">
                        <p class="description">Logo pour le thème violet foncé</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label>Logo "Green Day" (Thème Green)</label></th>
                    <td>
                        <?php if ($logo_green): ?>
                            <img src="<?php echo esc_url($logo_green); ?>" style="max-width: 200px; display: block; margin-bottom: 10px;">
                        <?php endif; ?>
                        <input type="file" name="logo_green" accept="image/*">
                        <p class="description">Logo pour le thème beige/vert</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label>Favicon (Icône)</label></th>
                    <td>
                        <?php if ($favicon): ?>
                            <img src="<?php echo esc_url($favicon); ?>" style="max-width: 32px; display: block; margin-bottom: 10px;">
                        <?php endif; ?>
                        <input type="file" name="favicon" accept="image/*">
                        <p class="description">Format recommandé : 32x32px ou 64x64px</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Enregistrer les logos', 'primary', 'submit_logos'); ?>
        </form>
    </div>
    <?php
}
