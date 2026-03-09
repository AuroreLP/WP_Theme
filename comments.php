<?php
/**
 * Comments Template
 *
 * Custom comment display and form layout. Handles:
 * - Comment count display
 * - Top-level approved comments with author name
 * - Inline reply forms (toggled by comments.js)
 * - Nested replies (one level deep)
 * - Main comment form at the bottom
 *
 * This is a fully custom implementation rather than using wp_list_comments()
 * and comment_form(). This gives full control over the HTML structure
 * but means WordPress comment-related filters (e.g. comment_form_defaults)
 * won't apply automatically.
 *
 * Reply forms include a nonce field for CSRF protection. While
 * wp-comments-post.php has its own verification, the extra nonce
 * adds defense-in-depth.
 *
 * Only one level of nesting is supported (parent → replies).
 * Deeper threading would require recursive rendering.
 *
 * @package turningpages
 */

$count = absint( get_comments_number() );
?>

<div class="comments-container">

    <?php /* Comment count header */ ?>
    <?php if ( $count >= 1 ) : ?>
        <span class="nv-comments">
            <?php echo absint( $count ); ?> Commentaire<?php echo $count > 1 ? 's' : ''; ?>
        </span>
    <?php endif; ?>

    <?php
    /**
     * Fetch top-level approved comments only (parent = 0).
     * Replies are fetched separately inside each comment card.
     */
    $comments = get_comments( array(
        'post_id' => get_the_ID(),
        'status'  => 'approve',
        'parent'  => 0,
    ) );

    if ( ! empty( $comments ) ) {
        foreach ( $comments as $comment ) {
            $comment_id = absint( $comment->comment_ID );
    ?>

        <?php /* ── Single comment card ── */ ?>
        <div class="comment-card">

            <?php /* Author name */ ?>
            <div class="comment-author">
                <p><?php echo esc_html( $comment->comment_author ); ?></p>
            </div>

            <?php /* Comment body — wp_kses_post allows safe HTML */ ?>
            <div class="comment-content">
                <p><?php echo wp_kses_post( $comment->comment_content ); ?></p>
            </div>

            <?php /* Date + reply toggle link */ ?>
            <div class="comment-meta">
                <p><?php echo esc_html( get_comment_date( 'j F Y', $comment_id ) ); ?></p>
                <a href="#" class="reply-link" data-comment-id="<?php echo $comment_id; ?>">Répondre</a>
            </div>

            <?php
            /**
             * Inline reply form — hidden by default.
             * Toggled visible by comments.js when "Répondre" is clicked.
             * comment_parent is set to this comment's ID for threading.
             */
            ?>
            <div class="comment-reply-form" id="comment-reply-form-<?php echo $comment_id; ?>" style="display:none;">
                <p>Vous répondez à <?php echo esc_html( $comment->comment_author ); ?></p>
                <form action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>" method="POST" class="comment-form">
                    <?php wp_nonce_field('comment_' . get_the_ID()); ?>

                    <label for="reply-author-<?php echo $comment_id; ?>">Nom<span class="required">*</span></label>
                    <input type="text" id="reply-author-<?php echo $comment_id; ?>" name="author" required>

                    <label for="reply-email-<?php echo $comment_id; ?>">Email<span class="required">*</span></label>
                    <input type="email" id="reply-email-<?php echo $comment_id; ?>" name="email" required>

                    <label for="reply-comment-<?php echo $comment_id; ?>">Commentaire<span class="required">*</span></label>
                    <textarea name="comment" id="reply-comment-<?php echo $comment_id; ?>" required></textarea>

                    <input type="submit" value="Commenter" class="btn" name="submit">
                    <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr( get_the_ID() ); ?>">
                    <input type="hidden" name="comment_parent" value="<?php echo $comment_id; ?>">
                </form>
            </div>

            <?php
            /**
             * Replies to this comment — one level of nesting.
             * Fetches approved child comments and renders them inline.
             */
            ?>
            <div class="reponse-content" id="comment-reply-content-<?php echo $comment_id; ?>">
                <?php
                $replies = get_comments( array(
                    'post_id' => get_the_ID(),
                    'status'  => 'approve',
                    'parent'  => $comment->comment_ID,
                ) );

                if ( ! empty( $replies ) ) {
                    foreach ( $replies as $reply ) {
                ?>
                    <div class="reply">
                        <div class="reply-content-author">
                            <p>Le <?php echo esc_html( get_comment_date( 'j F Y', $reply->comment_ID ) ); ?>, <?php echo esc_html( $reply->comment_author ); ?> a répondu:</p>
                        </div>
                        <div class="reply-content">
                            <p><?php echo wp_kses_post( $reply->comment_content ); ?></p>
                        </div>
                    </div>
                <?php
                    }
                }
                ?>
            </div>

        </div>

    <?php
        }
    } else {
        echo '<p class="no-comments">Aucun commentaire pour le moment.</p>';
    }
    ?>

    <?php /* ── Main comment form ── */ ?>
    <div class="main-comment-form">
        <h3>Laisser un commentaire</h3>
        <p>
            Votre adresse email ne sera jamais publiée. Elle est uniquement utilisée pour modérer les commentaires.<br>
            Veuillez consulter la <a href="<?php echo esc_url( home_url( '/politique-de-confidentialite/' ) ); ?>">politique de confidentialité</a> pour en savoir plus.
        </p>
        <form action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>" method="POST" id="commentform" class="comment-form">
            <?php wp_nonce_field('comment_' . get_the_ID()); ?>

            <label for="author">Nom<span class="required">*</span></label>
            <input type="text" id="author" name="author" required>

            <label for="email">Email<span class="required">*</span></label>
            <input type="email" id="email" name="email" required>

            <label for="comment">Commentaire<span class="required">*</span></label>
            <textarea name="comment" id="comment" required></textarea>

            <input type="submit" value="Commenter" class="btn" name="submit" id="submit">
            <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr( get_the_ID() ); ?>" id="comment_post_ID">
            <input type="hidden" name="comment_parent" value="0" id="comment_parent">
        </form>
    </div>

</div>
