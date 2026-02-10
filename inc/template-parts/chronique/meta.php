<div class="chronique-meta">
                <div class="article-tags">
                    <ul>
                        <?php 
                            $nationalites = get_the_terms(get_the_ID(), 'nationalite');
                            if ($nationalites && !is_wp_error($nationalites)) {
                                foreach ($nationalites as $nationalite) {
                                    echo '<li><a href="' . esc_url(get_term_link($nationalite)) . '"> ' . esc_html($nationalite->name) . '</a></li>';
                                }
                            }
                        ?>

                            <?php display_chronique_genres_list(); ?>
                            <?php display_chronique_themes_list(); ?>
                    </ul>
                </div>
            </div>