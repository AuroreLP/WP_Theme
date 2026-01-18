<?php
$count = absint(get_comments_number());
?>

<div class="comments-container">
    <?php if($count >= 1): ?>
      <span class="nv-comments"><?php echo absint($count); ?> Commentaire<?php echo $count > 1 ? 's' : ''; ?></span>
    <?php endif; ?>

    <?php
    // Récupération des commentaires approuvés
    $comments = get_comments(array(
      'post_id' => get_the_ID(),
      'status' => 'approve',
      'parent' => 0
    ));

    if(!empty($comments)) {
      foreach($comments as $comment) {
    ?>
      <!-- DÉBUT DE L'AFFICHAGE DU COMMENTAIRE -->
      <div class="comment-card">
        <div class="comment-author">
          <?php echo get_avatar($comment->comment_author_email, 60); ?>
          <p><?php echo esc_html($comment->comment_author); ?></p>
        </div>
        <div class="comment-content">
          <p><?php echo wp_kses_post($comment->comment_content); ?></p>
        </div>
        <div class="comment-meta">
          <p><?php echo esc_html(get_comment_date('j F Y', $comment->comment_ID)); ?></p>
          <a href="#" class="reply-link" data-comment-id="<?php echo absint($comment->comment_ID); ?>">Répondre</a>
        </div>
        
        <!-- Formulaire de réponse (caché par défaut) -->
        <div class="comment-reply-form" id="comment-reply-form-<?php echo absint($comment->comment_ID); ?>" style="display:none;">
          <p>Vous répondez à <?php echo esc_html($comment->comment_author); ?></p>
          <form action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>" method="POST" class="comment-form"> 
            <?php wp_nonce_field('comment_reply_' . absint($comment->comment_ID), 'comment_nonce'); ?>

            <label for="reply-author-<?php echo absint($comment->comment_ID); ?>">Nom<span class="required">*</span></label>
            <input type="text" id="reply-author-<?php echo absint($comment->comment_ID); ?>" name="author" required>
            
            <label for="reply-email-<?php echo absint($comment->comment_ID); ?>">Email<span class="required">*</span></label>
            <input type="email" id="reply-email-<?php echo absint($comment->comment_ID); ?>" name="email" required>
            
            <label for="reply-comment-<?php echo absint($comment->comment_ID); ?>">Commentaire<span class="required">*</span></label>
            <textarea name="comment" id="reply-comment-<?php echo absint($comment->comment_ID); ?>" required></textarea>
            
            <input type="submit" value="Commenter" class="btn" name="submit">
            <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr(get_the_ID()); ?>">
            <input type="hidden" name="comment_parent" value="<?php echo absint($comment->comment_ID); ?>">
          </form>
        </div>
        
        <!-- Affichage des réponses -->
        <div class="reponse-content" id="comment-reply-content-<?php echo absint($comment->comment_ID); ?>">
          <?php 
          $replies = get_comments(array(
            'post_id' => get_the_ID(),
            'status' => 'approve',
            'parent' => $comment->comment_ID
          ));
          
          if(!empty($replies)) {
            foreach($replies as $reply) { 
          ?>
            <div class="reply">
              <div class="reply-content-author">
                <?php echo get_avatar($reply->comment_author_email, 40); ?>
                <p>Le <?php echo esc_html(get_comment_date('j F Y', $reply->comment_ID)); ?>, <?php echo esc_html($reply->comment_author); ?> a répondu:</p>
              </div>
              <div class="reply-content">
                <p><?php echo wp_kses_post($reply->comment_content); ?></p>
              </div>
            </div>
          <?php 
            } 
          } 
          ?>
        </div>
      </div>
      <!-- FIN DE L'AFFICHAGE DU COMMENTAIRE -->
    <?php 
      } 
    } else {
      echo '<p class="no-comments">' . esc_html('Aucun commentaire pour le moment.') . '</p>';
    } 
    ?>

  <!-- Formulaire de commentaire principal -->
  <div class="main-comment-form">
    <h3>Laisser un commentaire</h3>
    <form action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>" method="POST" id="commentform" class="comment-form"> 
      <?php wp_nonce_field('comment_main_' . get_the_ID(), 'comment_nonce'); ?>
      
      <label for="author">Nom<span class="required">*</span></label>
      <input type="text" id="author" name="author" required>
      
      <label for="email">Email<span class="required">*</span></label>
      <input type="email" id="email" name="email" required>
      
      <label for="comment">Commentaire<span class="required">*</span></label>
      <textarea name="comment" id="comment" required></textarea>
      
      <input type="submit" value="Commenter" class="btn" name="submit" id="submit">
      <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr(get_the_ID()); ?>" id="comment_post_ID">
      <input type="hidden" name="comment_parent" value="0" id="comment_parent">
    </form>
  </div>
</div>