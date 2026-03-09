<?php
/**
 * Theme Logo Manager — Admin Page
 *
 * Provides a dedicated admin page (Logos Thèmes) for uploading the site logo
 * for each color scheme: Lilac Wine / Purple Rain / Green Day.
 *
 * Logos are stored as WordPress attachment IDs in the options table:
 * - logo_theme_light_id
 * - logo_theme_dark_id
 * - logo_theme_green_id
 *
 * Storing IDs (not URLs) means:
 * - Logos appear in the Media Library (editable, deletable, thumbnails generated)
 * - They survive domain changes and SSL migrations
 * - Any image size can be retrieved via wp_get_attachment_image_url()
 *
 * Favicon is NOT managed here — use the native WordPress Customizer instead:
 * Appearance > Customize > Site Identity > Site Icon.
 * It auto-generates all required sizes and meta tags.
 *
 * @package turningpages
 */

/**
 * Register the admin menu page.
 */
add_action( 'admin_menu', 'tp_logo_manager_menu' );
function tp_logo_manager_menu() {
    add_menu_page(
        'Gestion des Logos',       // Page title
        'Logos Thèmes',            // Menu label
        'manage_options',          // Capability required
        'gestion-logos-themes',    // Menu slug
        'tp_logo_manager_page',    // Render callback
        'dashicons-format-image',  // Icon
        30                         // Position
    );
}

/**
 * Allowed MIME types for logo uploads.
 *
 * Server-side validation — the HTML accept="image/*" attribute is client-side
 * only and can be bypassed. This array is the actual security gate.
 */
define( 'TP_LOGO_ALLOWED_MIMES', array(
    'image/png',
    'image/jpeg',
    'image/gif',
    'image/webp',
    'image/svg+xml',
) );

/**
 * Handle a single file upload, validate MIME type, and attach to Media Library.
 *
 * Uses media_handle_upload() instead of wp_handle_upload() so the file
 * gets a proper attachment post in wp_posts — visible in the Media Library,
 * with generated thumbnails and metadata.
 *
 * @param  string  $input_name  The name attribute of the <input type="file">.
 * @param  string  $option_key  The option name to store the attachment ID.
 * @return string  'success', 'skipped', or an error message.
 */
function tp_handle_logo_upload( $input_name, $option_key ) {
    // No file submitted for this field — skip silently
    if ( empty( $_FILES[ $input_name ]['name'] ) ) {
        return 'skipped';
    }

    // Server-side MIME validation
    $file_type = wp_check_filetype( $_FILES[ $input_name ]['name'] );
    if ( ! $file_type['type'] || ! in_array( $file_type['type'], TP_LOGO_ALLOWED_MIMES, true ) ) {
        return sprintf(
            'Type de fichier non autorisé pour « %s » : %s',
            esc_html( $input_name ),
            esc_html( $_FILES[ $input_name ]['type'] )
        );
    }

    // Ensure WordPress media handling functions are loaded
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    // Upload file and create attachment in Media Library
    $attachment_id = media_handle_upload( $input_name, 0 );

    if ( is_wp_error( $attachment_id ) ) {
        return $attachment_id->get_error_message();
    }

    update_option( $option_key, $attachment_id );

    return 'success';
}

/**
 * Retrieve a logo URL by theme key.
 *
 * Checks the new ID-based option first. If empty, falls back to the legacy
 * URL-based option (from the previous version of this manager) so that
 * logos aren't lost during migration. The fallback can be removed once
 * all three logos have been re-uploaded through this admin page.
 *
 * @param  string  $theme_key  One of: 'light', 'dark', 'green'.
 * @param  string  $size       WordPress image size. Default 'medium'.
 * @return string  Logo URL or empty string.
 */
function tp_get_logo_url( $theme_key, $size = 'medium' ) {
    $attachment_id = get_option( "logo_theme_{$theme_key}_id", 0 );

    if ( $attachment_id ) {
        $url = wp_get_attachment_image_url( $attachment_id, $size );
        if ( $url ) {
            return $url;
        }
    }

    // Legacy fallback — old URL-based option from previous version
    $legacy_url = get_option( "logo_theme_{$theme_key}", '' );
    return $legacy_url;
}

/**
 * Render the admin page.
 */
function tp_logo_manager_page() {

    // ── Handle form submission ──
    if ( isset( $_POST['submit_logos'] ) ) {
        check_admin_referer( 'logos_themes_nonce' );

        $fields = array(
            'logo_light' => 'logo_theme_light_id',
            'logo_dark'  => 'logo_theme_dark_id',
            'logo_green' => 'logo_theme_green_id',
        );

        $errors = array();

        foreach ( $fields as $input_name => $option_key ) {
            $result = tp_handle_logo_upload( $input_name, $option_key );
            if ( $result !== 'success' && $result !== 'skipped' ) {
                $errors[] = $result;
            }
        }

        if ( ! empty( $errors ) ) {
            echo '<div class="error"><p>' . wp_kses_post( implode( '<br>', $errors ) ) . '</p></div>';
        } else {
            echo '<div class="updated"><p>Logos mis à jour avec succès !</p></div>';
        }
    }

    // ── Logo field configuration ──
    $logo_fields = array(
        array(
            'label'       => 'Logo « Lilac Wine » (Thème Light)',
            'input_name'  => 'logo_light',
            'theme_key'   => 'light',
            'description' => 'Logo pour le thème mauve clair',
        ),
        array(
            'label'       => 'Logo « Purple Rain » (Thème Dark)',
            'input_name'  => 'logo_dark',
            'theme_key'   => 'dark',
            'description' => 'Logo pour le thème violet foncé',
        ),
        array(
            'label'       => 'Logo « Green Day » (Thème Green)',
            'input_name'  => 'logo_green',
            'theme_key'   => 'green',
            'description' => 'Logo pour le thème beige/vert',
        ),
    );

    ?>
    <div class="wrap">
        <h1>Gestion des Logos par Thème</h1>
        <p class="description">
            Pour le favicon (icône du site), utilisez le Customizer WordPress :
            Apparence &gt; Personnaliser &gt; Identité du site &gt; Icône du site.
        </p>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field( 'logos_themes_nonce' ); ?>

            <table class="form-table">
                <?php foreach ( $logo_fields as $field ) :
                    $preview_url = tp_get_logo_url( $field['theme_key'] );
                ?>
                <tr>
                    <th scope="row">
                        <label><?php echo esc_html( $field['label'] ); ?></label>
                    </th>
                    <td>
                        <?php if ( $preview_url ) : ?>
                            <img
                                src="<?php echo esc_url( $preview_url ); ?>"
                                style="max-width: 200px; display: block; margin-bottom: 10px;"
                                alt="<?php echo esc_attr( $field['label'] ); ?>"
                            >
                        <?php endif; ?>
                        <input type="file" name="<?php echo esc_attr( $field['input_name'] ); ?>" accept="image/*">
                        <p class="description"><?php echo esc_html( $field['description'] ); ?></p>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

            <?php submit_button( 'Enregistrer les logos', 'primary', 'submit_logos' ); ?>
        </form>
    </div>
    <?php
}