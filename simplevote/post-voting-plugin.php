<?php
/*
Plugin Name: Post Voting Plugin
Description: A simple post voting plugin with positive and negative votes using Ajax.
Version: 0.1
Author: Vedran Bejatovic
*/

/** 
 * Enqueue your custom CSS and JS
 */
function enqueue_plugin_styles()
{
    if (is_single()) {
        wp_enqueue_style('post-voting-styles', plugin_dir_url(__FILE__) . 'css/post-voting-style.css');
    }
}
add_action('wp_enqueue_scripts', 'enqueue_plugin_styles');

function enqueue_ajax_scripts()
{
    wp_enqueue_script('post-voting-script', plugin_dir_url(__FILE__) . 'js/post-voting-script.js', array('jquery'), '1.0', true);
    wp_localize_script('post-voting-script', 'post_voting_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'post_id' => get_the_ID(),
        'ajax_nonce' => wp_create_nonce('post_voting_nonce'),
    ));
}

add_action('wp_enqueue_scripts', 'enqueue_ajax_scripts');

/** 
 * Voting form
 */
function add_voting_form($content)
{
    // Check if it's a single post
    if (is_single()) {

        $positive_count = get_post_meta(get_the_ID(), 'positive', true);
        $negative_count = get_post_meta(get_the_ID(), 'negative', true);

        // Make sure counts are numeric, if not set them to 0
        $positive_count = is_numeric($positive_count) ? $positive_count : 0;
        $negative_count = is_numeric($negative_count) ? $negative_count : 0;

        // Calculate average percentages
        $total_votes = $positive_count + $negative_count;
        $average_positive_percentage = ($total_votes > 0) ? round(($positive_count / $total_votes) * 100) : 0;
        $average_negative_percentage = ($total_votes > 0) ? round(($negative_count / $total_votes) * 100) : 0;

        ob_start();
?>
        <div class="voting-form">
            <div class="voting-message">
                Was this article helpful? <span class="separator"></span>
                <button class="vote-button" data-vote="positive"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-smile-fill" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zM4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8z" />
                    </svg><span>YES</span></button>
                <button class="vote-button" data-vote="negative"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown-fill" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm-2.715 5.933a.5.5 0 0 1-.183-.683A4.498 4.498 0 0 1 8 9.5a4.5 4.5 0 0 1 3.898 2.25.5.5 0 0 1-.866.5A3.498 3.498 0 0 0 8 10.5a3.498 3.498 0 0 0-3.032 1.75.5.5 0 0 1-.683.183zM10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8z" />
                    </svg><span>NO</span></button>
            </div>
            <div class="feedback-message positive-feedback">
                Thank you for your feedback. <span class="separator"></span>
                <button class="voted-button active" data-vote="positive"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-smile-fill" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zM4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8z" />
                    </svg><span class="positive-percentage"><?php echo $average_positive_percentage; ?>%</span><span class="positive-num"><?php echo $positive_count; ?></span></button>
                <button class="voted-button" data-vote="negative"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown-fill" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm-2.715 5.933a.5.5 0 0 1-.183-.683A4.498 4.498 0 0 1 8 9.5a4.5 4.5 0 0 1 3.898 2.25.5.5 0 0 1-.866.5A3.498 3.498 0 0 0 8 10.5a3.498 3.498 0 0 0-3.032 1.75.5.5 0 0 1-.683.183zM10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8z" />
                    </svg><span class="negative-percentage"><?php echo $average_negative_percentage; ?>%</span><span class="negative-num"><?php echo $negative_count; ?></span></button>
            </div>
            <div class="feedback-message negative-feedback">
                Thank you for your feedback. <span class="separator"></span>
                <button class="voted-button" data-vote="positive"><span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-smile-fill" viewBox="0 0 16 16">
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zM4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8z" />
                        </svg><span class="positive-percentage"><?php echo $average_positive_percentage; ?>%</span><span class="positive-num"><?php echo $positive_count; ?></span></span></button>
                <button class="voted-button active" data-vote="negative"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown-fill" viewBox="0 0 16 16">
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm-2.715 5.933a.5.5 0 0 1-.183-.683A4.498 4.498 0 0 1 8 9.5a4.5 4.5 0 0 1 3.898 2.25.5.5 0 0 1-.866.5A3.498 3.498 0 0 0 8 10.5a3.498 3.498 0 0 0-3.032 1.75.5.5 0 0 1-.683.183zM10 8c-.552 0-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5S10.552 8 10 8z" />
                    </svg><span class="negative-percentage"><?php echo $average_negative_percentage; ?>%</span><span class="negative-num"><?php echo $negative_count; ?></span></span></button>
            </div>
        </div>
    <?php
        return $content . ob_get_clean();
    }
}

add_filter('the_content', 'add_voting_form');

/**
 * Checks the vote status of a user for a specific post.
 */
function check_user_vote_status()
{
    $post_id = absint($_POST['post_id']);
    $user_id = get_current_user_id();

    if ($user_id !== 0) {
        // Logged-in user
        $has_voted = get_user_meta($user_id, 'voted_posts', true);
        $has_voted = is_array($has_voted) ? $has_voted : array();
    } else {
        // Not logged-in user (use IP address)
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $has_voted = get_option('voted_ips', array());
        $has_voted = is_array($has_voted) ? $has_voted : array();
    }

    $user_has_voted = in_array($post_id . '-positive', $has_voted) || in_array($post_id . '-negative', $has_voted);
    $vote_type = '';

    if ($user_has_voted) {
        $vote_type = in_array($post_id . '-positive', $has_voted) ? 'positive' : 'negative';
    }

    wp_send_json_success(array('user_has_voted' => $user_has_voted, 'vote_type' => $vote_type));
}

add_action('wp_ajax_check_user_vote_status', 'check_user_vote_status');
add_action('wp_ajax_nopriv_check_user_vote_status', 'check_user_vote_status');

/**
 * Processes votes
 */
function process_vote()
{
    try {
        if (isset($_POST['vote_nonce']) && wp_verify_nonce($_POST['vote_nonce'], 'post_voting_nonce')) {
            $post_id = absint($_POST['post_id']);
            $vote_type = sanitize_text_field($_POST['vote_type']);
            $user_id = get_current_user_id();

            // Check if the user has already voted on this post
            $has_voted = array();

            if ($user_id !== 0) {
                // Logged-in user
                $has_voted = get_user_meta($user_id, 'voted_posts', true);
                $has_voted = is_array($has_voted) ? $has_voted : array();
            } else {
                // Not logged-in user (use IP address)
                $user_ip = $_SERVER['REMOTE_ADDR'];
                $has_voted = get_option('voted_ips', array());
                $has_voted = is_array($has_voted) ? $has_voted : array();

                if (in_array($post_id . '-' . $vote_type, $has_voted)) {
                    throw new Exception('You have already voted on this article.');
                }
            }

            // Update the list of voted posts for the user or IP
            $has_voted[] = ($user_id !== 0) ? $post_id . '-' . $vote_type : $post_id . '-' . $vote_type;

            if ($user_id !== 0) {
                // Logged-in user
                update_user_meta($user_id, 'voted_posts', $has_voted);
            } else {
                // Not logged-in user (use IP address)
                update_option('voted_ips', $has_voted);
            }

            // Retrieve current positive and negative counts
            $positive_count = get_post_meta($post_id, 'positive', true);
            $negative_count = get_post_meta($post_id, 'negative', true);

            // Make sure counts are numeric, if not set them to 0
            $positive_count = is_numeric($positive_count) ? $positive_count : 0;
            $negative_count = is_numeric($negative_count) ? $negative_count : 0;

            // Update positive and negative vote counts
            if ($vote_type === 'positive') {
                $positive_count++;
            } elseif ($vote_type === 'negative') {
                $negative_count++;
            }

            update_post_meta($post_id, 'positive', $positive_count);
            update_post_meta($post_id, 'negative', $negative_count);

            // Return updated counts in the response
            wp_send_json_success(array('positive_count' => $positive_count, 'negative_count' => $negative_count));
        } else {
            throw new Exception('Invalid nonce.');
        }
    } catch (Exception $e) {
        error_log('Error processing vote: ' . $e->getMessage());
        wp_send_json_error($e->getMessage());
    }
}

add_action('wp_ajax_process_vote', 'process_vote');
add_action('wp_ajax_nopriv_process_vote', 'process_vote');

/**
 * Adds a voting meta box to the post editor.
 */
function add_voting_meta_box()
{
    add_meta_box(
        'voting_results_meta_box',
        'VOTING RESULTS',
        'display_voting_meta_box',
        'post',  // Check if it should be 'post' or a different post type
        'normal',
        'high'
    );
}

/**
 * Displays the voting meta box for a given post.
 */
function display_voting_meta_box($post)
{
    $positive_count = get_post_meta($post->ID, 'positive', true);
    $negative_count = get_post_meta($post->ID, 'negative', true);

    // Ensure that $positive_count and $negative_count are numeric values
    $positive_count = is_numeric($positive_count) ? intval($positive_count) : 0;
    $negative_count = is_numeric($negative_count) ? intval($negative_count) : 0;

    $total_votes = $positive_count + $negative_count;

    $average_positive_percentage = ($total_votes > 0) ? round(($positive_count / $total_votes) * 100) : 0;
    $average_negative_percentage = ($total_votes > 0) ? round(($negative_count / $total_votes) * 100) : 0;
    ?>
    <p><strong>Total Votes:</strong> <?php echo $total_votes; ?></p>
    <p><strong>Positive:</strong> <?php echo $positive_count; ?></p>
    <p><strong>Negative:</strong> <?php echo $negative_count; ?></p>
    <p><strong>Average Positive:</strong> <?php echo $average_positive_percentage; ?>%</p>
    <p><strong>Average Negative:</strong> <?php echo $average_negative_percentage; ?>%</p>
<?php
}

add_action('add_meta_boxes', 'add_voting_meta_box', 10, 1);
